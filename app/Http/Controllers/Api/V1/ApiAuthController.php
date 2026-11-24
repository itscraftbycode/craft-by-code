<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiLoginRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\ApiToken;
use App\Models\User;
use App\Security\RolePermissions;
use App\Security\SecurityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    /**
     * Authenticate user credentials and issue a cryptographically secure API Bearer token.
     */
    public function login(ApiLoginRequest $request): JsonResponse
    {
        $email = Str::lower(trim((string) $request->input('email')));
        $password = (string) $request->input('password');

        $user = User::where('email', $email)->first();

        // Support multi-email alias lookup for primary admin
        if (! $user && in_array($email, ['admin@uppage.tech', 'admin@craftbycode.tech', 'admin@craftbycode.in'], true)) {
            $user = User::find(1);
        }

        // Check account lock status
        if ($user && $user->isLockedOut()) {
            $minutes = max(1, (int) ceil($user->locked_until->diffInSeconds(now()) / 60));
            SecurityLogger::log('login_locked_attempt', 'API login attempt on locked account', 'warning', $user, $request);

            return response()->json([
                'status' => 'error',
                'code' => 'ACCOUNT_LOCKED',
                'message' => "Account is temporarily locked due to repeated failed attempts. Try again in {$minutes} minute(s).",
            ], 423);
        }

        // Verify credentials with constant-time password check
        if (! $user || ! Hash::check($password, $user->password)) {
            if ($user) {
                $attempts = $user->failed_login_attempts + 1;
                $updates = ['failed_login_attempts' => $attempts];

                if ($attempts >= 5) {
                    $updates['locked_until'] = now()->addMinutes(15);
                }

                $user->forceFill($updates)->save();

                SecurityLogger::log('api_login_failed', 'Failed API login attempt (invalid password)', 'warning', $user, $request);
            } else {
                SecurityLogger::log('api_login_failed', 'Failed API login attempt (unknown account)', 'warning', null, $request, [
                    'attempted_email' => $email,
                ]);
            }

            return response()->json([
                'status' => 'error',
                'code' => 'INVALID_CREDENTIALS',
                'message' => 'Invalid email or password.',
            ], 401);
        }

        // Verify user active status
        if (! $user->is_active) {
            SecurityLogger::log('unauthorized_access', 'API login attempt by inactive/suspended user', 'high', $user, $request);

            return response()->json([
                'status' => 'error',
                'code' => 'ACCOUNT_INACTIVE',
                'message' => 'Your account has been deactivated. Please contact security administration.',
            ], 403);
        }

        // Reset failed login attempts on success
        $user->forceFill([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        // Determine token abilities based on RBAC matrix
        $roleAbilities = RolePermissions::MATRIX[$user->role] ?? [];
        if ($user->role === 'admin' || $user->role === 'super_admin') {
            $roleAbilities = ['*'];
        }

        // Issue high-entropy 64-char hex Bearer token
        $plainToken = 'cbc_' . bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $plainToken);

        $tokenName = $request->input('token_name') ?: 'api_token_' . now()->format('Ymd_His');
        $expiryDays = (int) ($request->input('expires_in_days') ?: 7);
        $expiresAt = now()->addDays($expiryDays);

        $tokenRecord = ApiToken::create([
            'user_id' => $user->id,
            'name' => mb_substr($tokenName, 0, 100),
            'token_hash' => $tokenHash,
            'abilities' => $roleAbilities,
            'last_used_at' => now(),
            'last_used_ip' => $request->ip(),
            'expires_at' => $expiresAt,
        ]);

        SecurityLogger::log('api_login_success', "API Bearer token issued for user '{$user->name}'", 'info', $user, $request, [
            'token_id' => $tokenRecord->id,
            'token_name' => $tokenRecord->name,
            'expires_at' => $expiresAt->toIso8601String(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Authenticated successfully.',
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $plainToken,
                'expires_at' => $expiresAt->toIso8601String(),
                'user' => new UserResource($user),
                'abilities' => $roleAbilities,
            ],
        ], 200);
    }

    /**
     * Return authenticated user profile and active token scope.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        /** @var ApiToken|null $apiToken */
        $apiToken = $request->attributes->get('api_token');

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => new UserResource($user),
                'token' => [
                    'id' => $apiToken?->id,
                    'name' => $apiToken?->name,
                    'abilities' => $apiToken?->abilities ?? [],
                    'last_used_at' => $apiToken?->last_used_at?->toIso8601String(),
                    'expires_at' => $apiToken?->expires_at?->toIso8601String(),
                ],
            ],
        ]);
    }

    /**
     * Atomically rotate the current active Bearer token.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        /** @var ApiToken $currentToken */
        $currentToken = $request->attributes->get('api_token');

        // Revoke old token
        $currentToken->forceFill([
            'revoked_at' => now(),
        ])->save();

        // Generate new token with inherited abilities
        $plainToken = 'cbc_' . bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $plainToken);
        $expiresAt = now()->addDays(7);

        $newToken = ApiToken::create([
            'user_id' => $user->id,
            'name' => $currentToken->name . '_refreshed_' . now()->format('Ymd_His'),
            'token_hash' => $tokenHash,
            'abilities' => $currentToken->abilities ?? ['*'],
            'last_used_at' => now(),
            'last_used_ip' => $request->ip(),
            'expires_at' => $expiresAt,
        ]);

        SecurityLogger::log('api_token_refreshed', "API token rotated for user '{$user->name}'", 'info', $user, $request, [
            'old_token_id' => $currentToken->id,
            'new_token_id' => $newToken->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Token rotated successfully.',
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $plainToken,
                'expires_at' => $expiresAt->toIso8601String(),
            ],
        ]);
    }

    /**
     * Revoke the currently presented Bearer token.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        /** @var ApiToken $currentToken */
        $currentToken = $request->attributes->get('api_token');

        if ($currentToken) {
            $currentToken->forceFill(['revoked_at' => now()])->save();
            SecurityLogger::log('api_token_revoked', "API token '{$currentToken->name}' revoked via logout", 'info', $user, $request, [
                'token_id' => $currentToken->id,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Token revoked successfully. You have been logged out.',
        ]);
    }

    /**
     * Revoke all active Bearer tokens for the authenticated user.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $user = $request->user();

        $revokedCount = ApiToken::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);

        SecurityLogger::log('api_tokens_all_revoked', "Revoked {$revokedCount} active API tokens for user '{$user->name}'", 'warning', $user, $request);

        return response()->json([
            'status' => 'success',
            'message' => "All {$revokedCount} active tokens have been revoked.",
        ]);
    }
}


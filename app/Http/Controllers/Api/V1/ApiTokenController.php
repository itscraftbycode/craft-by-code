<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ApiCreateTokenRequest;
use App\Http\Resources\Api\ApiTokenResource;
use App\Models\ApiToken;
use App\Security\SecurityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiTokenController extends Controller
{
    /**
     * List tokens belonging to the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = ApiToken::latest();
        if ($user->role !== 'admin' && $user->role !== 'super_admin') {
            $query->where('user_id', $user->id);
        }

        $tokens = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => ApiTokenResource::collection($tokens),
            'meta' => [
                'current_page' => $tokens->currentPage(),
                'total' => $tokens->total(),
            ],
        ]);
    }

    /**
     * Generate a new scoped Bearer token.
     */
    public function store(ApiCreateTokenRequest $request): JsonResponse
    {
        $user = $request->user();

        $abilities = $request->input('abilities') ?: ['*'];
        $days = (int) ($request->input('expires_in_days') ?: 30);
        $expiresAt = now()->addDays($days);

        $plainToken = 'cbc_' . bin2hex(random_bytes(32));
        $hash = hash('sha256', $plainToken);

        $token = ApiToken::create([
            'user_id' => $user->id,
            'name' => $request->input('name'),
            'token_hash' => $hash,
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
            'last_used_ip' => $request->ip(),
        ]);

        SecurityLogger::log('api_token_created', "API token '{$token->name}' created for user '{$user->name}'", 'info', $user, $request, [
            'token_id' => $token->id,
            'abilities' => $abilities,
            'expires_at' => $expiresAt->toIso8601String(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'API token created successfully. Store this secret safely; it will not be displayed again.',
            'data' => [
                'token_id' => $token->id,
                'name' => $token->name,
                'token_type' => 'Bearer',
                'access_token' => $plainToken,
                'abilities' => $abilities,
                'expires_at' => $expiresAt->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Revoke an API token (with strict BOLA / ownership check).
     */
    public function destroy(Request $request, ApiToken $token): JsonResponse
    {
        $user = $request->user();

        if (Gate::forUser($user)->denies('delete', $token)) {
            SecurityLogger::log('api_bola_blocked', 'BOLA check failed: User attempted to revoke token owned by another account', 'critical', $user, $request, [
                'target_token_id' => $token->id,
                'token_owner' => $token->user_id,
            ]);

            return response()->json([
                'status' => 'error',
                'code' => 'FORBIDDEN',
                'message' => 'You do not have authorization to revoke this token.',
            ], 403);
        }

        $token->forceFill(['revoked_at' => now()])->save();

        SecurityLogger::log('api_token_revoked', "API token '{$token->name}' revoked via API by user '{$user->name}'", 'warning', $user, $request, [
            'token_id' => $token->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "API token '{$token->name}' has been revoked.",
        ]);
    }
}


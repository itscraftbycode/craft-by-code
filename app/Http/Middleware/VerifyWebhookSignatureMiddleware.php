<?php

namespace App\Http\Middleware;

use App\Security\SecurityLogger;
use App\Security\WebhookVerifier;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSignatureMiddleware
{
    /**
     * Handle an incoming webhook request and verify its cryptographic HMAC signature.
     */
    public function handle(Request $request, Closure $next, ?string $secretEnvVar = 'WEBHOOK_SECRET'): Response
    {
        $secret = (string) env($secretEnvVar ?? 'WEBHOOK_SECRET', '');

        if ($secret === '') {
            SecurityLogger::log('webhook_misconfigured', 'Webhook secret not configured in environment', 'critical', null, $request);

            return response()->json([
                'status' => 'error',
                'message' => 'Webhook verification service is misconfigured on server.',
            ], 500);
        }

        if (! WebhookVerifier::verifyRequest($request, $secret)) {
            SecurityLogger::log('webhook_signature_invalid', 'Invalid or missing webhook cryptographic signature', 'high', null, $request, [
                'ip' => $request->ip(),
                'signature_header' => $request->header('X-Hub-Signature-256') ?? $request->header('X-Signature'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or forged webhook signature.',
            ], 403);
        }

        return $next($request);
    }
}


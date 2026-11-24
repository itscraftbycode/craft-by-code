<?php

namespace App\Security;

use Illuminate\Http\Request;

class WebhookVerifier
{
    /**
     * Verify an incoming webhook HMAC-SHA256 signature against the raw payload.
     * Prevents timing attacks with hash_equals and replay attacks with timestamp validation.
     *
     * @param  string  $payload  Raw HTTP request body
     * @param  string  $signature  Signature from header (e.g., sha256=... or raw hex)
     * @param  string  $secret  Shared webhook secret
     * @param  int|null  $timestamp  Optional timestamp from request header
     * @param  int  $toleranceSeconds  Maximum allowable drift for timestamp (default 300s = 5 min)
     */
    public static function verify(
        string $payload,
        string $signature,
        string $secret,
        ?int $timestamp = null,
        int $toleranceSeconds = 300
    ): bool {
        if ($secret === '' || $signature === '') {
            return false;
        }

        // Replay attack prevention: verify timestamp within acceptable window
        if ($timestamp !== null) {
            $currentTime = time();
            if (abs($currentTime - $timestamp) > $toleranceSeconds) {
                return false;
            }
            $payloadToSign = $timestamp.'.'.$payload;
        } else {
            $payloadToSign = $payload;
        }

        // Normalize signature by stripping optional prefix (e.g. 'sha256=')
        $normalizedSignature = preg_replace('/^sha256=/i', '', trim($signature));

        $expectedSignature = hash_hmac('sha256', $payloadToSign, $secret);

        return hash_equals($expectedSignature, $normalizedSignature);
    }

    /**
     * Convenience method to verify a Laravel Request directly.
     */
    public static function verifyRequest(
        Request $request,
        string $secret,
        string $signatureHeader = 'X-Hub-Signature-256',
        ?string $timestampHeader = 'X-Webhook-Timestamp'
    ): bool {
        $rawSignature = (string) $request->header($signatureHeader, '');
        if ($rawSignature === '') {
            // Also check alternate common header names
            $rawSignature = (string) $request->header('X-Signature', (string) $request->header('Stripe-Signature', ''));
        }

        $timestamp = null;
        if ($timestampHeader && $request->hasHeader($timestampHeader)) {
            $timestamp = (int) $request->header($timestampHeader);
        }

        return self::verify(
            (string) $request->getContent(),
            $rawSignature,
            $secret,
            $timestamp
        );
    }
}


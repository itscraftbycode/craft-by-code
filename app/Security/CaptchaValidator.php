<?php

namespace App\Security;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CaptchaValidator
{
    /**
     * Verify the CAPTCHA response token from the incoming HTTP request.
     * Supports both Google reCAPTCHA and Cloudflare Turnstile.
     */
    public static function verify(Request $request): bool
    {
        // Automatically pass in testing environment unless explicitly testing captcha
        if (app()->environment('testing') && ! config('services.captcha.test_enforcement', false)) {
            return true;
        }

        $secret = (string) config('services.captcha.secret_key', env('CAPTCHA_SECRET_KEY', ''));

        // If no secret key is configured, gracefully fall back to Honeypot protection
        if (empty(trim($secret))) {
            return true;
        }

        $driver = strtolower((string) config('services.captcha.driver', env('CAPTCHA_DRIVER', 'recaptcha')));

        $token = $request->input('g-recaptcha-response')
            ?? $request->input('cf-turnstile-response')
            ?? $request->input('captcha_token');

        if (empty($token) || ! is_string($token)) {
            SecurityLogger::log('captcha_missing', 'Form submission missing required CAPTCHA token', 'medium', null, $request);

            return false;
        }

        $verifyUrl = $driver === 'turnstile'
            ? 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            : 'https://www.google.com/recaptcha/api/siteverify';

        try {
            $response = Http::timeout(6)->asForm()->post($verifyUrl, [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            $success = (bool) $response->json('success', false);

            if (! $success) {
                SecurityLogger::log('captcha_failed', 'CAPTCHA verification rejected by upstream provider', 'medium', null, $request, [
                    'driver' => $driver,
                    'error_codes' => $response->json('error-codes', []),
                ]);
            }

            return $success;
        } catch (\Throwable $e) {
            SecurityLogger::log('captcha_error', 'CAPTCHA verification network error: '.$e->getMessage(), 'high', null, $request);

            return false;
        }
    }
}

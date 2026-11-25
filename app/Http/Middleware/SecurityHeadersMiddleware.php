<?php

namespace App\Http\Middleware;

use App\Security\SecurityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (SecurityLogger::isIpBlocked($request->ip())) {
            SecurityLogger::log('blocked_ip_rejected', 'Rejected request from temporarily blocked IP address', 'high', null, $request);

            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Access temporarily restricted due to suspicious activity.',
                ], 429);
            }

            return response('Too many suspicious security events detected from your IP. Access temporarily restricted.', 429);
        }

        $nonce = base64_encode(random_bytes(16));
        Vite::useCspNonce($nonce);
        View::share('cspNonce', $nonce);
        $request->attributes->set('csp_nonce', $nonce);

        $response = $next($request);

        $response->headers->remove('X-Powered-By');
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
        }

        $isLocal = app()->environment('local');
        $isPrivateArea = $request->is('admin', 'admin/*', 'api', 'api/*');

        $ga4Id = '';
        if (! $isPrivateArea) {
            try {
                $candidateGa4 = (string) \App\Models\WebsiteSetting::get('ga4_measurement_id', '');
                if (preg_match('/^G-[A-Z0-9]{6,20}$/', $candidateGa4)) {
                    $ga4Id = $candidateGa4;
                }
            } catch (\Throwable $e) {
                $ga4Id = '';
            }
        }

        $scriptSources = "'self' 'nonce-{$nonce}' 'unsafe-eval' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/ https://challenges.cloudflare.com";
        $connectSources = $isLocal
            ? "'self' http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:* https://challenges.cloudflare.com"
            : "'self' https://challenges.cloudflare.com";

        if ($ga4Id !== '') {
            $scriptSources .= ' https://www.googletagmanager.com';
            $connectSources .= ' https://www.google-analytics.com https://*.google-analytics.com https://*.analytics.google.com https://*.googletagmanager.com';
        }

        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            "script-src {$scriptSources}",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: https:",
            "frame-src 'self' https://www.google.com https://maps.google.com https://www.google.com/recaptcha/ https://recaptcha.google.com/ https://challenges.cloudflare.com",
            "connect-src {$connectSources}",
        ];

        if ($request->isSecure()) {
            $directives[] = 'upgrade-insecure-requests';
        }

        $response->headers->set('Content-Security-Policy', implode('; ', $directives));
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        if ($isPrivateArea) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        }

        return $response;
    }
}


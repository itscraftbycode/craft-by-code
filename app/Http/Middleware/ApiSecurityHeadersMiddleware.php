<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiSecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Enforce HTTPS in production environments
        if (app()->environment('production') && ! $request->isSecure()) {
            return response()->json([
                'status' => 'error',
                'code' => 'HTTPS_REQUIRED',
                'message' => 'Encrypted HTTPS connection is strictly required for API communication.',
            ], 403);
        }

        $response = $next($request);

        // 2. Strip sensitive server signatures
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
            @header_remove('Server');
        }

        // 3. Inject strict API security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}


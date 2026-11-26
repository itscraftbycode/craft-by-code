<?php

namespace App\Http\Middleware;

use App\Models\WebsiteVisitor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUniqueVisitors
{
    /**
     * Common search engine and crawler bot user-agent patterns.
     */
    private const BOT_PATTERNS = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'googlebot',
        'bingbot', 'yandex', 'baidu', 'duckduckgo', 'facebookexternalhit',
        'twitterbot', 'linkedinbot', 'slackbot', 'bytespider', 'ahrefs',
        'semrush', 'curl', 'wget', 'python',
    ];

    /**
     * Handle an incoming request and track unique human visitors.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Only track GET requests for web pages
        if (! $request->isMethod('GET')) {
            return $next($request);
        }

        // 2. Ignore non-frontend routes, APIs, admin panel, and static assets
        if ($request->is('admin', 'admin/*', 'api', 'api/*', 'media', 'media/*', 'build/*', 'assets/*', 'up', 'health')) {
            return $next($request);
        }

        // 3. Ignore AJAX, JSON API, and background prefetch requests
        if ($request->ajax() || $request->prefetch() || $request->expectsJson()) {
            return $next($request);
        }

        $ua = (string) $request->userAgent();

        // 4. Exclude automated bots and search engine crawlers
        if ($this->isBot($ua)) {
            return $next($request);
        }

        // 5. Generate unique hardware/network fingerprint
        $ip = (string) ($request->ip() ?: '127.0.0.1');
        $visitorHash = hash('sha256', $ip.'|'.$ua);

        $sessionKey = 'tracked_visitor_'.substr($visitorHash, 0, 16);

        // 6. Record or update unique visitor record safely
        if (! $request->session()->has($sessionKey)) {
            try {
                $location = \App\Services\GeoLocationService::resolve($request, $ip);

                $visitor = WebsiteVisitor::firstOrCreate(
                    ['visitor_hash' => $visitorHash],
                    [
                        'ip_address' => $ip,
                        'user_agent' => substr($ua, 0, 250),
                        'landing_page' => substr($request->path(), 0, 250),
                        'referer' => substr((string) $request->header('referer'), 0, 250),
                        'country' => $location['country'] ?? null,
                        'country_code' => $location['country_code'] ?? null,
                        'city' => $location['city'] ?? null,
                        'region' => $location['region'] ?? null,
                        'first_visited_at' => now(),
                        'last_visited_at' => now(),
                        'hits' => 1,
                    ]
                );

                if (! $visitor->wasRecentlyCreated) {
                    $updatePayload = ['last_visited_at' => now()];

                    if (empty($visitor->country) && ! empty($location['country'])) {
                        $updatePayload['country'] = $location['country'];
                        $updatePayload['country_code'] = $location['country_code'];
                        $updatePayload['city'] = $location['city'];
                        $updatePayload['region'] = $location['region'];
                    }

                    $visitor->increment('hits', 1, $updatePayload);
                }

                $request->session()->put($sessionKey, true);
            } catch (\Throwable $e) {
                // Fail silently so visitor page load is never interrupted
            }
        }

        return $next($request);
    }

    /**
     * Check if user-agent represents a crawler or bot.
     */
    private function isBot(string $userAgent): bool
    {
        if (trim($userAgent) === '') {
            return true;
        }

        $lowerUa = strtolower($userAgent);
        foreach (self::BOT_PATTERNS as $pattern) {
            if (str_contains($lowerUa, $pattern)) {
                return true;
            }
        }

        return false;
    }
}


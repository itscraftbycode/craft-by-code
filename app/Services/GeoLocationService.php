<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeoLocationService
{
    /**
     * Map of common ISO 3166-1 alpha-2 codes to country names.
     */
    private const COMMON_COUNTRIES = [
        'IN' => 'India',
        'US' => 'United States',
        'GB' => 'United Kingdom',
        'CA' => 'Canada',
        'AU' => 'Australia',
        'DE' => 'Germany',
        'FR' => 'France',
        'NL' => 'Netherlands',
        'SG' => 'Singapore',
        'AE' => 'United Arab Emirates',
        'SA' => 'Saudi Arabia',
        'JP' => 'Japan',
        'BR' => 'Brazil',
        'ZA' => 'South Africa',
        'NZ' => 'New Zealand',
        'IE' => 'Ireland',
        'IT' => 'Italy',
        'ES' => 'Spain',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'BD' => 'Bangladesh',
        'PK' => 'Pakistan',
        'NP' => 'Nepal',
        'LK' => 'Sri Lanka',
    ];

    /**
     * Resolve visitor location details (Country, Country Code, City, Region).
     *
     * @return array{country: ?string, country_code: ?string, city: ?string, region: ?string}
     */
    public static function resolve(Request $request, string $ip): array
    {
        // 1. Instant check: Cloudflare or reverse proxy CDN headers
        $cfCountry = strtoupper(trim((string) ($request->header('cf-ipcountry') ?? $request->header('x-country-code') ?? '')));
        $cfCity = trim((string) ($request->header('cf-ipcity') ?? ''));
        $cfRegion = trim((string) ($request->header('cf-region') ?? $request->header('cf-region-code') ?? ''));

        if (! empty($cfCountry) && $cfCountry !== 'XX' && $cfCountry !== 'T1') {
            return [
                'country' => self::getCountryName($cfCountry),
                'country_code' => $cfCountry,
                'city' => ! empty($cfCity) ? $cfCity : null,
                'region' => ! empty($cfRegion) ? $cfRegion : null,
            ];
        }

        // 2. Private or localhost IPs (e.g. 127.0.0.1, ::1, 192.168.x.x)
        if (self::isPrivateOrLocal($ip)) {
            return [
                'country' => 'Local Network',
                'country_code' => 'LOCAL',
                'city' => 'Localhost',
                'region' => 'Development',
            ];
        }

        // 3. Cache public GeoIP lookups for 30 days to avoid repeat HTTP calls
        $cacheKey = 'geoip_lookup_'.str_replace([':', '.'], '_', $ip);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($ip) {
            try {
                // Free GeoIP lookup with 2-second timeout so page rendering is never blocked
                $response = Http::timeout(2)
                    ->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,regionName,city");

                if ($response->successful() && $response->json('status') === 'success') {
                    $countryCode = strtoupper(trim((string) $response->json('countryCode')));
                    $country = trim((string) $response->json('country'));
                    $city = trim((string) $response->json('city'));
                    $region = trim((string) $response->json('regionName'));

                    return [
                        'country' => ! empty($country) ? $country : self::getCountryName($countryCode),
                        'country_code' => ! empty($countryCode) ? $countryCode : null,
                        'city' => ! empty($city) ? $city : null,
                        'region' => ! empty($region) ? $region : null,
                    ];
                }
            } catch (\Throwable $e) {
                // Fail silently
            }

            return [
                'country' => null,
                'country_code' => null,
                'city' => null,
                'region' => null,
            ];
        });
    }

    /**
     * Check if an IP address is a private, loopback, or reserved address.
     */
    public static function isPrivateOrLocal(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost', '0.0.0.0'], true)) {
            return true;
        }

        return ! filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * Get human-readable country name from ISO code.
     */
    public static function getCountryName(string $code): string
    {
        $code = strtoupper($code);

        return self::COMMON_COUNTRIES[$code] ?? $code;
    }

    /**
     * Get country flag emoji from a 2-letter ISO country code.
     */
    public static function getFlagEmoji(?string $code): string
    {
        $code = strtoupper(trim((string) $code));

        if (strlen($code) === 2 && ctype_alpha($code)) {
            return mb_chr(127397 + ord($code[0])).mb_chr(127397 + ord($code[1]));
        }

        if ($code === 'LOCAL') {
            return '💻';
        }

        return '📍';
    }
}

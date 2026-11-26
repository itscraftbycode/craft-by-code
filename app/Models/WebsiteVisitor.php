<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteVisitor extends Model
{
    protected $fillable = [
        'visitor_hash',
        'ip_address',
        'user_agent',
        'landing_page',
        'referer',
        'country',
        'country_code',
        'city',
        'region',
        'hits',
        'first_visited_at',
        'last_visited_at',
    ];

    protected $casts = [
        'first_visited_at' => 'datetime',
        'last_visited_at' => 'datetime',
        'hits' => 'integer',
    ];

    public function scopeToday($query)
    {
        return $query->whereDate('first_visited_at', today());
    }

    public function scopeActivePast24Hours($query)
    {
        return $query->where('last_visited_at', '>=', now()->subDay());
    }

    /**
     * Human-readable device / browser label helper.
     */
    public function getDeviceLabelAttribute(): string
    {
        $ua = (string) $this->user_agent;

        if (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) {
            return 'Apple iOS';
        }
        if (stripos($ua, 'Android') !== false) {
            return 'Android Mobile';
        }
        if (stripos($ua, 'Macintosh') !== false || stripos($ua, 'Mac OS') !== false) {
            return 'Mac Desktop';
        }
        if (stripos($ua, 'Windows') !== false) {
            return 'Windows PC';
        }
        if (stripos($ua, 'Linux') !== false) {
            return 'Linux Device';
        }

        return 'Web Client';
    }

    /**
     * Human-readable visitor location (City, Country).
     */
    public function getLocationLabelAttribute(): string
    {
        $city = trim((string) $this->city);
        $country = trim((string) $this->country);

        if (! empty($city) && ! empty($country)) {
            return "{$city}, {$country}";
        }

        if (! empty($country)) {
            return $country;
        }

        if (! empty($city)) {
            return $city;
        }

        if (\App\Services\GeoLocationService::isPrivateOrLocal((string) $this->ip_address)) {
            return 'Local Environment';
        }

        return 'Unknown Location';
    }

    /**
     * Flag emoji corresponding to visitor country code.
     */
    public function getCountryFlagAttribute(): string
    {
        return \App\Services\GeoLocationService::getFlagEmoji($this->country_code);
    }

    /**
     * Masked IP address for GDPR/privacy compliance.
     */
    public function getMaskedIpAttribute(): string
    {
        if (empty($this->ip_address)) {
            return 'Unknown IP';
        }

        if (filter_var($this->ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $this->ip_address);
            if (count($parts) === 4) {
                return $parts[0].'.'.$parts[1].'.***.***';
            }
        }

        return substr($this->ip_address, 0, 8).'****';
    }
}


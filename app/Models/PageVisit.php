<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageVisit extends Model
{
    protected $fillable = [
        'url',
        'page_type',
        'is_landing',
        'is_bounce',
        'vehicle_id',
        'loueur_id',
        'ip_address',
        'session_id',
        'user_agent',
        'referer',
        'traffic_source',
        'traffic_medium',
        'referrer_domain',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'country',
        'country_code',
        'city',
        'region',
        'region_code',
        'latitude',
        'longitude',
        'timezone',
        'isp',
        'device_type',
        'browser',
        'os',
        'is_bot',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'is_landing' => 'boolean',
        'is_bounce' => 'boolean',
        'is_bot' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    /**
     * Helper to detect device type from user agent.
     */
    public static function detectDeviceType(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);

        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i', $userAgent)) {
            return 'tablet';
        }

        if (preg_match('/(mobile|iphone|ipod|android|blackberry|opera mini|opera mobi)/i', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Helper to detect browser from user agent.
     */
    public static function detectBrowser(string $userAgent): string
    {
        if (str_contains($userAgent, 'Edg/') || str_contains($userAgent, 'Edge/')) return 'Edge';
        if (str_contains($userAgent, 'OPR/') || str_contains($userAgent, 'Opera')) return 'Opera';
        if (str_contains($userAgent, 'Chrome/') && !str_contains($userAgent, 'Chromium')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox/')) return 'Firefox';
        if (str_contains($userAgent, 'Safari/') && !str_contains($userAgent, 'Chrome')) return 'Safari';
        if (str_contains($userAgent, 'MSIE') || str_contains($userAgent, 'Trident/')) return 'IE';
        return 'Other';
    }

    /**
     * Helper to detect OS from user agent.
     */
    public static function detectOS(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);

        if (str_contains($userAgent, 'windows nt 10')) return 'Windows 10/11';
        if (str_contains($userAgent, 'windows nt 6.3')) return 'Windows 8.1';
        if (str_contains($userAgent, 'windows nt 6.2')) return 'Windows 8';
        if (str_contains($userAgent, 'windows nt 6.1')) return 'Windows 7';
        if (str_contains($userAgent, 'windows')) return 'Windows';

        if (str_contains($userAgent, 'macintosh') || str_contains($userAgent, 'mac os x')) return 'macOS';

        if (str_contains($userAgent, 'iphone')) return 'iOS (iPhone)';
        if (str_contains($userAgent, 'ipad')) return 'iOS (iPad)';

        if (str_contains($userAgent, 'android')) {
            if (preg_match('/android\s([0-9]+)/i', $userAgent, $matches)) {
                return 'Android ' . $matches[1];
            }
            return 'Android';
        }

        if (str_contains($userAgent, 'linux')) return 'Linux';
        if (str_contains($userAgent, 'ubuntu')) return 'Ubuntu';
        if (str_contains($userAgent, 'chromeos')) return 'Chrome OS';

        return 'Other';
    }

    /**
     * Scope for visits from a specific country.
     */
    public function scopeFromCountry($query, string $countryCode)
    {
        return $query->where('country_code', strtoupper($countryCode));
    }

    /**
     * Scope for visits from a specific traffic source.
     */
    public function scopeFromSource($query, string $source)
    {
        return $query->where('traffic_source', $source);
    }

    /**
     * Scope for organic traffic.
     */
    public function scopeOrganic($query)
    {
        return $query->where('traffic_medium', 'organic');
    }

    /**
     * Scope for social traffic.
     */
    public function scopeSocial($query)
    {
        return $query->where('traffic_medium', 'social');
    }

    /**
     * Scope for direct traffic.
     */
    public function scopeDirect($query)
    {
        return $query->where('traffic_source', 'direct');
    }

    /**
     * Scope for landing pages.
     */
    public function scopeLandingPages($query)
    {
        return $query->where('is_landing', true);
    }

    /**
     * Scope for bounced visits.
     */
    public function scopeBounced($query)
    {
        return $query->where('is_bounce', true);
    }
}

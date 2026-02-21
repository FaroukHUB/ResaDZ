<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    /**
     * Get geolocation data from IP address.
     * Uses ip-api.com (free, 45 requests/minute limit)
     */
    public function getLocation(string $ip): array
    {
        // Skip for local/private IPs
        if ($this->isPrivateIp($ip)) {
            return $this->getDefaultLocation();
        }

        // Cache for 24 hours to reduce API calls
        $cacheKey = "geo_ip_{$ip}";

        return Cache::remember($cacheKey, 86400, function () use ($ip) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,country,countryCode,region,regionName,city,lat,lon,timezone,isp,query',
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'success') {
                        return [
                            'country' => $data['country'] ?? null,
                            'country_code' => $data['countryCode'] ?? null,
                            'region' => $data['regionName'] ?? null,
                            'region_code' => $data['region'] ?? null,
                            'city' => $data['city'] ?? null,
                            'latitude' => $data['lat'] ?? null,
                            'longitude' => $data['lon'] ?? null,
                            'timezone' => $data['timezone'] ?? null,
                            'isp' => $data['isp'] ?? null,
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning("GeoLocation failed for IP {$ip}: " . $e->getMessage());
            }

            return $this->getDefaultLocation();
        });
    }

    /**
     * Check if IP is private/local.
     */
    protected function isPrivateIp(string $ip): bool
    {
        if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }

        // Check for private IP ranges
        $privateRanges = [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
        ];

        foreach ($privateRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is in CIDR range.
     */
    protected function ipInRange(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = ~((1 << (32 - $mask)) - 1);

        return ($ip & $mask) === ($subnet & $mask);
    }

    /**
     * Get default location for unknown IPs.
     */
    protected function getDefaultLocation(): array
    {
        return [
            'country' => null,
            'country_code' => null,
            'region' => null,
            'region_code' => null,
            'city' => null,
            'latitude' => null,
            'longitude' => null,
            'timezone' => null,
            'isp' => null,
        ];
    }

    /**
     * Get traffic source from referer URL.
     */
    public static function getTrafficSource(?string $referer): array
    {
        if (empty($referer)) {
            return ['source' => 'direct', 'medium' => 'none', 'referrer_domain' => null];
        }

        $parsed = parse_url($referer);
        $host = $parsed['host'] ?? '';
        $domain = strtolower($host);

        // Remove www prefix
        $domain = preg_replace('/^www\./', '', $domain);

        // Search engines
        $searchEngines = [
            'google' => ['google.com', 'google.fr', 'google.dz', 'google.co'],
            'bing' => ['bing.com'],
            'yahoo' => ['yahoo.com', 'search.yahoo'],
            'duckduckgo' => ['duckduckgo.com'],
            'yandex' => ['yandex.ru', 'yandex.com'],
            'baidu' => ['baidu.com'],
        ];

        foreach ($searchEngines as $engine => $domains) {
            foreach ($domains as $d) {
                if (str_contains($domain, $d)) {
                    return ['source' => $engine, 'medium' => 'organic', 'referrer_domain' => $domain];
                }
            }
        }

        // Social networks
        $socialNetworks = [
            'facebook' => ['facebook.com', 'fb.com', 'fb.me', 'm.facebook.com', 'l.facebook.com'],
            'instagram' => ['instagram.com', 'l.instagram.com'],
            'twitter' => ['twitter.com', 't.co', 'x.com'],
            'linkedin' => ['linkedin.com', 'lnkd.in'],
            'youtube' => ['youtube.com', 'youtu.be'],
            'tiktok' => ['tiktok.com'],
            'pinterest' => ['pinterest.com'],
            'reddit' => ['reddit.com'],
            'whatsapp' => ['whatsapp.com', 'wa.me'],
            'telegram' => ['telegram.org', 't.me'],
        ];

        foreach ($socialNetworks as $network => $domains) {
            foreach ($domains as $d) {
                if (str_contains($domain, $d)) {
                    return ['source' => $network, 'medium' => 'social', 'referrer_domain' => $domain];
                }
            }
        }

        // Email providers (for newsletter tracking)
        $emailProviders = [
            'gmail' => ['mail.google.com', 'gmail.com'],
            'outlook' => ['outlook.live.com', 'outlook.com'],
            'yahoo_mail' => ['mail.yahoo.com'],
        ];

        foreach ($emailProviders as $provider => $domains) {
            foreach ($domains as $d) {
                if (str_contains($domain, $d)) {
                    return ['source' => $provider, 'medium' => 'email', 'referrer_domain' => $domain];
                }
            }
        }

        // Default: referral from other website
        return ['source' => $domain, 'medium' => 'referral', 'referrer_domain' => $domain];
    }
}

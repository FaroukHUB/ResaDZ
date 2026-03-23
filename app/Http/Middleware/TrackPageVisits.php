<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use App\Services\GeoLocationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisits
{
    protected GeoLocationService $geoService;

    /**
     * Bot user agent patterns to exclude.
     */
    protected array $botPatterns = [
        'bot', 'crawl', 'spider', 'slurp', 'facebookexternalhit',
        'mediapartners', 'googlebot', 'bingbot', 'yandex', 'baidu',
        'duckduckbot', 'semrush', 'ahrefs', 'mj12bot', 'dotbot',
        'petalbot', 'uptimerobot', 'pingdom', 'curl', 'wget',
        'python-requests', 'go-http-client', 'headlesschrome',
        'phantomjs', 'selenium', 'lighthouse', 'pagespeed', 'scraper',
    ];

    public function __construct(GeoLocationService $geoService)
    {
        $this->geoService = $geoService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track GET requests and successful responses
        if ($request->isMethod('GET') && $response->isSuccessful()) {
            $this->trackVisit($request);
        }

        return $response;
    }

    protected function trackVisit(Request $request): void
    {
        // Skip excluded IPs
        $ip = $request->ip();
        if ($this->isExcludedIp($ip)) {
            return;
        }

        // Skip bots
        $userAgent = $request->userAgent() ?? '';
        if ($this->isBot($userAgent)) {
            return;
        }

        // Skip tracking for admin panels, API routes, and assets
        $path = $request->path();
        if ($this->shouldSkipPath($path)) {
            return;
        }

        // Skip if authenticated as admin
        $user = $request->user();
        if ($user && ($user->role === 'admin' || $user->is_admin)) {
            return;
        }

        // Generate or get session ID for tracking
        $sessionId = $this->getOrCreateSessionId($request);

        // Detect page type and related IDs
        $pageType = $this->detectPageType($path);
        $vehicleId = null;
        $loueurId = null;

        // Try to extract vehicle/loueur from route
        if ($route = $request->route()) {
            $vehicleId = $route->parameter('vehicle')?->id ?? $route->parameter('vehicleId');
            $loueurId = $route->parameter('loueur')?->id ?? $route->parameter('loueurId');
        }

        // Get geolocation data
        $geoData = $this->geoService->getLocation($ip);

        // Get traffic source
        $referer = $request->header('referer', '');
        $trafficData = GeoLocationService::getTrafficSource($referer);

        // Check if this is a landing page (first visit in session)
        $isLanding = $this->isLandingPage($sessionId);

        // Get UTM parameters
        $utmSource = $request->get('utm_source');
        $utmMedium = $request->get('utm_medium');
        $utmCampaign = $request->get('utm_campaign');
        $utmTerm = $request->get('utm_term');
        $utmContent = $request->get('utm_content');

        // Override traffic source if UTM is present
        if ($utmSource) {
            $trafficData['source'] = $utmSource;
            $trafficData['medium'] = $utmMedium ?? 'campaign';
        }

        try {
            PageVisit::create([
                'url' => substr($request->fullUrl(), 0, 500),
                'page_type' => $pageType,
                'is_landing' => $isLanding,
                'is_bounce' => false, // Will be updated later if needed
                'vehicle_id' => $vehicleId,
                'loueur_id' => $loueurId,
                'ip_address' => $ip,
                'session_id' => $sessionId,
                'user_agent' => substr($userAgent, 0, 500),
                'referer' => substr($referer, 0, 500),
                'traffic_source' => $trafficData['source'],
                'traffic_medium' => $trafficData['medium'],
                'referrer_domain' => $trafficData['referrer_domain'],
                'utm_source' => $utmSource ? substr($utmSource, 0, 100) : null,
                'utm_medium' => $utmMedium ? substr($utmMedium, 0, 100) : null,
                'utm_campaign' => $utmCampaign ? substr($utmCampaign, 0, 100) : null,
                'utm_term' => $utmTerm ? substr($utmTerm, 0, 100) : null,
                'utm_content' => $utmContent ? substr($utmContent, 0, 100) : null,
                'country' => $geoData['country'],
                'country_code' => $geoData['country_code'],
                'city' => $geoData['city'],
                'region' => $geoData['region'],
                'region_code' => $geoData['region_code'],
                'latitude' => $geoData['latitude'],
                'longitude' => $geoData['longitude'],
                'timezone' => $geoData['timezone'],
                'isp' => $geoData['isp'] ? substr($geoData['isp'], 0, 100) : null,
                'device_type' => PageVisit::detectDeviceType($userAgent),
                'browser' => PageVisit::detectBrowser($userAgent),
                'os' => PageVisit::detectOS($userAgent),
                'is_bot' => false,
                'visited_at' => now(),
            ]);

            // Update real-time visitors cache
            $this->updateRealtimeVisitors($sessionId);

        } catch (\Exception $e) {
            // Silently fail - don't break the site for tracking errors
            \Log::warning('Page visit tracking failed: ' . $e->getMessage());
        }
    }

    protected function shouldSkipPath(string $path): bool
    {
        $skipPaths = [
            'admin', 'loueur', 'api', 'livewire', 'storage',
            '_debugbar', 'sanctum', 'telescope', 'horizon',
        ];

        foreach ($skipPaths as $skip) {
            if (str_starts_with($path, $skip)) {
                return true;
            }
        }

        // Skip file requests
        if (str_contains($path, '.') && !str_contains($path, 'location-voiture-')) {
            return true;
        }

        return false;
    }

    protected function isExcludedIp(string $ip): bool
    {
        $configExcluded = config('resadz.excluded_tracking_ips', []);
        return in_array($ip, $configExcluded);
    }

    protected function isBot(string $userAgent): bool
    {
        if (empty(trim($userAgent))) {
            return true;
        }

        $userAgentLower = strtolower($userAgent);

        foreach ($this->botPatterns as $pattern) {
            if (str_contains($userAgentLower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function getOrCreateSessionId(Request $request): string
    {
        $sessionKey = 'visitor_session_id';

        if ($request->session()->has($sessionKey)) {
            return $request->session()->get($sessionKey);
        }

        $sessionId = bin2hex(random_bytes(16));
        $request->session()->put($sessionKey, $sessionId);

        return $sessionId;
    }

    protected function isLandingPage(string $sessionId): bool
    {
        $cacheKey = "session_landing_{$sessionId}";

        if (Cache::has($cacheKey)) {
            return false;
        }

        // Mark this session as having a landing page
        Cache::put($cacheKey, true, 1800); // 30 minutes

        return true;
    }

    protected function updateRealtimeVisitors(string $sessionId): void
    {
        $cacheKey = "realtime_visitor_{$sessionId}";
        Cache::put($cacheKey, now(), 300); // 5 minutes TTL
    }

    protected function detectPageType(string $path): string
    {
        if ($path === '' || $path === '/') return 'home';
        if (str_starts_with($path, 'vehicule/') || str_starts_with($path, 'vehicles/')) return 'vehicle';
        if (str_starts_with($path, 'loueur/')) return 'loueur';
        if (str_starts_with($path, 'reservation') || str_starts_with($path, 'booking') || str_starts_with($path, 'reserver')) return 'booking';
        if (str_starts_with($path, 'location-voiture-')) return 'seo_wilaya';
        if (str_starts_with($path, 'comparer')) return 'compare';
        if (str_starts_with($path, 'avis')) return 'review';
        if ($path === 'vehicules' || $path === 'vehicles') return 'vehicles_list';
        if (str_starts_with($path, 'connexion') || str_starts_with($path, 'inscription')) return 'auth';
        if (str_starts_with($path, 'ma-reservation')) return 'client_booking';
        return 'other';
    }
}

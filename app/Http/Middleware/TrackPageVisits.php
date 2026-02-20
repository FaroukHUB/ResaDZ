<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisits
{
    /**
     * IPs to exclude from tracking (your own IPs)
     * Add your server IP, your personal IPs, etc.
     */
    protected array $excludedIps = [
        // Add your IPs here, e.g.:
        // '127.0.0.1',
        // '::1',
        // 'YOUR_IP_ADDRESS',
    ];

    /**
     * Bot user agent patterns to exclude
     */
    protected array $botPatterns = [
        'bot', 'crawl', 'spider', 'slurp', 'facebookexternalhit',
        'mediapartners', 'googlebot', 'bingbot', 'yandex', 'baidu',
        'duckduckbot', 'semrush', 'ahrefs', 'mj12bot', 'dotbot',
        'petalbot', 'uptimerobot', 'pingdom', 'curl', 'wget',
        'python-requests', 'go-http-client', 'headlesschrome',
        'phantomjs', 'selenium', 'lighthouse', 'pagespeed',
    ];

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
        if (str_starts_with($path, 'admin') ||
            str_starts_with($path, 'loueur') ||
            str_starts_with($path, 'api') ||
            str_starts_with($path, 'livewire') ||
            str_starts_with($path, 'storage') ||
            str_starts_with($path, '_debugbar') ||
            str_starts_with($path, 'sanctum') ||
            str_contains($path, '.')) {
            return;
        }

        // Skip if authenticated as admin
        $user = $request->user();
        if ($user && ($user->role === 'admin' || $user->is_admin)) {
            return;
        }

        // Detect page type and related IDs
        $pageType = $this->detectPageType($path);
        $vehicleId = null;
        $loueurId = null;

        // Try to extract vehicle/loueur from route
        if ($route = $request->route()) {
            $vehicleId = $route->parameter('vehicle')?->id ?? $route->parameter('vehicleId');
            $loueurId = $route->parameter('loueur')?->id ?? $route->parameter('loueurId');
        }

        try {
            PageVisit::create([
                'url' => $request->fullUrl(),
                'page_type' => $pageType,
                'vehicle_id' => $vehicleId,
                'loueur_id' => $loueurId,
                'ip_address' => $ip,
                'user_agent' => substr($userAgent, 0, 255),
                'referer' => substr($request->header('referer', ''), 0, 255),
                'device_type' => PageVisit::detectDeviceType($userAgent),
                'browser' => PageVisit::detectBrowser($userAgent),
                'is_bot' => false,
                'visited_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break the site for tracking errors
            \Log::warning('Page visit tracking failed: ' . $e->getMessage());
        }
    }

    protected function isExcludedIp(string $ip): bool
    {
        // Check config for excluded IPs
        $configExcluded = config('resadz.excluded_tracking_ips', []);
        $allExcluded = array_merge($this->excludedIps, $configExcluded);

        return in_array($ip, $allExcluded);
    }

    protected function isBot(string $userAgent): bool
    {
        $userAgentLower = strtolower($userAgent);

        foreach ($this->botPatterns as $pattern) {
            if (str_contains($userAgentLower, $pattern)) {
                return true;
            }
        }

        // Also check for empty user agent (often bots)
        if (empty(trim($userAgent))) {
            return true;
        }

        return false;
    }

    protected function detectPageType(string $path): string
    {
        if ($path === '' || $path === '/') return 'home';
        if (str_starts_with($path, 'vehicule/') || str_starts_with($path, 'vehicles/')) return 'vehicle';
        if (str_starts_with($path, 'loueur/')) return 'loueur';
        if (str_starts_with($path, 'reservation') || str_starts_with($path, 'booking')) return 'booking';
        if (str_starts_with($path, 'location-voiture-')) return 'seo_wilaya';
        if (str_starts_with($path, 'comparer')) return 'compare';
        if (str_starts_with($path, 'avis')) return 'review';
        if ($path === 'vehicules' || $path === 'vehicles') return 'vehicles_list';
        if (str_starts_with($path, 'connexion') || str_starts_with($path, 'inscription')) return 'auth';
        return 'other';
    }
}

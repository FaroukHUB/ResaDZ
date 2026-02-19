<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisits
{
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
        // Skip tracking for admin panels, API routes, and assets
        $path = $request->path();
        if (str_starts_with($path, 'admin') ||
            str_starts_with($path, 'loueur') ||
            str_starts_with($path, 'api') ||
            str_starts_with($path, 'livewire') ||
            str_starts_with($path, 'storage') ||
            str_contains($path, '.')) {
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

        $userAgent = $request->userAgent() ?? '';

        try {
            PageVisit::create([
                'url' => $request->fullUrl(),
                'page_type' => $pageType,
                'vehicle_id' => $vehicleId,
                'loueur_id' => $loueurId,
                'ip_address' => $request->ip(),
                'user_agent' => substr($userAgent, 0, 255),
                'referer' => substr($request->header('referer', ''), 0, 255),
                'device_type' => PageVisit::detectDeviceType($userAgent),
                'browser' => PageVisit::detectBrowser($userAgent),
                'visited_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break the site for tracking errors
            \Log::warning('Page visit tracking failed: ' . $e->getMessage());
        }
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
        return 'other';
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClickEvent;
use App\Models\PageVisit;
use App\Services\GeoLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TrackingController extends Controller
{
    protected GeoLocationService $geoService;

    public function __construct(GeoLocationService $geoService)
    {
        $this->geoService = $geoService;
    }

    /**
     * Track a click event (phone, WhatsApp, reserve, etc.).
     */
    public function trackClick(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_type' => 'required|string|max:50',
            'vehicle_id' => 'nullable|integer',
            'loueur_id' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ]);

        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? '';

        // Exclude bots and excluded IPs from click tracking
        if ($this->isBot($userAgent) || $this->isExcludedIp($ip)) {
            return response()->json(['success' => true]);
        }

        $geoData = $this->geoService->getLocation($ip);

        // Get session ID safely (API routes may not have session)
        $sessionId = $request->hasSession() ? $request->session()->get('visitor_session_id') : null;

        try {
            ClickEvent::create([
                'event_type' => $validated['event_type'],
                'vehicle_id' => $validated['vehicle_id'] ?? null,
                'loueur_id' => $validated['loueur_id'] ?? null,
                'page_url' => $request->header('referer'),
                'ip_address' => $ip,
                'session_id' => $sessionId,
                'user_agent' => substr($userAgent, 0, 500),
                'device_type' => PageVisit::detectDeviceType($userAgent),
                'country_code' => $geoData['country_code'],
                'city' => $geoData['city'],
                'metadata' => $validated['metadata'] ?? null,
                'clicked_at' => now(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Tracking failed'], 500);
        }
    }

    /**
     * Get real-time visitor count.
     */
    public function getRealtimeVisitors(): JsonResponse
    {
        // Count visitors with active sessions (within last 5 minutes)
        $pattern = 'realtime_visitor_*';
        $count = 0;

        // Use Redis scan if available, otherwise estimate from recent visits
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $keys = Cache::getStore()->getRedis()->keys(config('cache.prefix') . ':' . $pattern);
            $count = count($keys);
        } else {
            // Fallback: count unique sessions in last 5 minutes
            $count = PageVisit::where('visited_at', '>=', now()->subMinutes(5))
                ->distinct('session_id')
                ->count('session_id');
        }

        return response()->json([
            'count' => $count,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Heartbeat to keep session active for real-time tracking.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        // Get session ID safely (API routes may not have session)
        $sessionId = $request->hasSession() ? $request->session()->get('visitor_session_id') : null;

        if ($sessionId) {
            Cache::put("realtime_visitor_{$sessionId}", now(), 300);
        }

        return response()->json(['success' => true]);
    }

    protected function isBot(string $userAgent): bool
    {
        if (empty(trim($userAgent))) {
            return true;
        }

        $ua = strtolower($userAgent);
        $botPatterns = [
            'bot', 'crawl', 'spider', 'scraper', 'slurp', 'facebookexternalhit',
            'mediapartners', 'googlebot', 'bingbot', 'yandex', 'baidu',
            'duckduckbot', 'semrush', 'ahrefs', 'mj12bot', 'dotbot',
            'petalbot', 'uptimerobot', 'pingdom', 'curl', 'wget',
            'python-requests', 'go-http-client', 'headlesschrome',
            'phantomjs', 'selenium', 'lighthouse', 'pagespeed',
        ];

        foreach ($botPatterns as $pattern) {
            if (str_contains($ua, $pattern)) {
                return true;
            }
        }

        return false;
    }

    protected function isExcludedIp(string $ip): bool
    {
        $excluded = config('resadz.excluded_tracking_ips', []);
        return in_array($ip, $excluded);
    }
}

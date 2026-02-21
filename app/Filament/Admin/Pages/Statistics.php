<?php

namespace App\Filament\Admin\Pages;

use App\Models\Booking;
use App\Models\ClickEvent;
use App\Models\Loueur;
use App\Models\PageVisit;
use App\Models\Vehicle;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Statistics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Analytics';
    protected static ?string $navigationLabel = 'Statistiques';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.admin.pages.statistics';

    public string $period = 'month';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearOldData')
                ->label('Nettoyer anciennes données')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Nettoyer les données')
                ->modalDescription('Supprimer les visites de plus de 90 jours ?')
                ->action(function () {
                    $deleted = PageVisit::where('visited_at', '<', now()->subDays(90))->delete();
                    Notification::make()
                        ->title("{$deleted} visites supprimées")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getViewData(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // Current IP and excluded IPs
        $currentIp = request()->ip();
        $excludedIps = config('resadz.excluded_tracking_ips', []);

        // Top IPs
        $topIps = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->selectRaw('ip_address, COUNT(*) as visits, MIN(visited_at) as first_visit, MAX(visited_at) as last_visit')
            ->groupBy('ip_address')
            ->orderByDesc('visits')
            ->limit(20)
            ->get();

        // Total records
        $totalRecords = PageVisit::count();
        $oldestRecord = PageVisit::orderBy('visited_at')->first();

        // ===== BASIC STATS =====
        $todayVisits = PageVisit::whereDate('visited_at', $today)->count();
        $yesterdayVisits = PageVisit::whereDate('visited_at', Carbon::yesterday())->count();
        $weekVisits = PageVisit::whereBetween('visited_at', [$startOfWeek, now()])->count();
        $monthVisits = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])->count();
        $lastMonthVisits = PageVisit::whereBetween('visited_at', [$startOfLastMonth, $endOfLastMonth])->count();

        // Unique visitors
        $todayUnique = PageVisit::whereDate('visited_at', $today)->distinct('ip_address')->count('ip_address');
        $monthUnique = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])->distinct('ip_address')->count('ip_address');

        // ===== GEOGRAPHIC STATS =====
        $topCountries = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->whereNotNull('country')
            ->selectRaw('country, country_code, COUNT(*) as visits')
            ->groupBy('country', 'country_code')
            ->orderByDesc('visits')
            ->limit(15)
            ->get();

        $topCities = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->whereNotNull('city')
            ->selectRaw('city, country_code, COUNT(*) as visits')
            ->groupBy('city', 'country_code')
            ->orderByDesc('visits')
            ->limit(15)
            ->get();

        $topRegions = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->whereNotNull('region')
            ->selectRaw('region, country_code, COUNT(*) as visits')
            ->groupBy('region', 'country_code')
            ->orderByDesc('visits')
            ->limit(15)
            ->get();

        // ===== TRAFFIC SOURCES =====
        $trafficSources = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->selectRaw('traffic_source, traffic_medium, COUNT(*) as visits')
            ->groupBy('traffic_source', 'traffic_medium')
            ->orderByDesc('visits')
            ->limit(20)
            ->get();

        $trafficByMedium = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->selectRaw('traffic_medium, COUNT(*) as visits')
            ->groupBy('traffic_medium')
            ->orderByDesc('visits')
            ->get();

        // ===== UTM CAMPAIGNS =====
        $utmCampaigns = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->whereNotNull('utm_campaign')
            ->selectRaw('utm_source, utm_medium, utm_campaign, COUNT(*) as visits')
            ->groupBy('utm_source', 'utm_medium', 'utm_campaign')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        // ===== HOURLY STATS =====
        $hourlyStats = PageVisit::whereBetween('visited_at', [now()->subDays(7), now()])
            ->selectRaw('HOUR(visited_at) as hour, COUNT(*) as visits')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('visits', 'hour')
            ->toArray();

        // Fill missing hours
        for ($i = 0; $i < 24; $i++) {
            if (!isset($hourlyStats[$i])) {
                $hourlyStats[$i] = 0;
            }
        }
        ksort($hourlyStats);

        // ===== DAY OF WEEK STATS =====
        $dayOfWeekStats = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->selectRaw('DAYOFWEEK(visited_at) as day, COUNT(*) as visits')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->pluck('visits', 'day')
            ->toArray();

        $dayNames = [1 => 'Dimanche', 2 => 'Lundi', 3 => 'Mardi', 4 => 'Mercredi', 5 => 'Jeudi', 6 => 'Vendredi', 7 => 'Samedi'];

        // ===== DEVICES & BROWSERS & OS =====
        $deviceStats = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->selectRaw('device_type, COUNT(*) as visits')
            ->groupBy('device_type')
            ->orderByDesc('visits')
            ->get();

        $browserStats = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->selectRaw('browser, COUNT(*) as visits')
            ->groupBy('browser')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        $osStats = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->whereNotNull('os')
            ->selectRaw('os, COUNT(*) as visits')
            ->groupBy('os')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        // ===== TOP PAGES =====
        $topPages = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->selectRaw('page_type, COUNT(*) as visits')
            ->groupBy('page_type')
            ->orderByDesc('visits')
            ->get();

        // ===== TOP VEHICLES =====
        $topVehicles = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->whereNotNull('vehicle_id')
            ->selectRaw('vehicle_id, COUNT(*) as visits')
            ->groupBy('vehicle_id')
            ->orderByDesc('visits')
            ->limit(10)
            ->with('vehicle.brand')
            ->get();

        // ===== LANDING PAGES =====
        $landingPages = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->where('is_landing', true)
            ->selectRaw('page_type, COUNT(*) as visits')
            ->groupBy('page_type')
            ->orderByDesc('visits')
            ->get();

        // ===== BOUNCE RATE =====
        $totalSessions = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->whereNotNull('session_id')
            ->distinct('session_id')
            ->count('session_id');

        // Count sessions with only 1 page view (bounced)
        $bouncedSessionsResult = DB::select("
            SELECT COUNT(*) as count FROM (
                SELECT session_id
                FROM page_visits
                WHERE visited_at BETWEEN ? AND ?
                AND session_id IS NOT NULL
                GROUP BY session_id
                HAVING COUNT(*) = 1
            ) as bounced
        ", [$startOfMonth, now()]);
        $bouncedSessions = $bouncedSessionsResult[0]->count ?? 0;

        $bounceRate = $totalSessions > 0 ? round(($bouncedSessions / $totalSessions) * 100, 1) : 0;

        // ===== DAILY VISITS CHART =====
        $dailyVisits = PageVisit::whereBetween('visited_at', [now()->subDays(30), now()])
            ->selectRaw('DATE(visited_at) as date, COUNT(*) as visits')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ===== CLICK EVENTS =====
        $clickStats = [];
        if (\Schema::hasTable('click_events')) {
            $clickStats = ClickEvent::whereBetween('clicked_at', [$startOfMonth, now()])
                ->selectRaw('event_type, COUNT(*) as clicks')
                ->groupBy('event_type')
                ->orderByDesc('clicks')
                ->get();
        }

        // ===== CONVERSION FUNNEL =====
        $funnelHome = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->where('page_type', 'home')
            ->distinct('session_id')
            ->count('session_id');

        $funnelVehicleList = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->where('page_type', 'vehicles_list')
            ->distinct('session_id')
            ->count('session_id');

        $funnelVehicle = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->where('page_type', 'vehicle')
            ->distinct('session_id')
            ->count('session_id');

        $funnelBooking = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->where('page_type', 'booking')
            ->distinct('session_id')
            ->count('session_id');

        $funnelCompleted = Booking::whereBetween('created_at', [$startOfMonth, now()])->count();

        // ===== REAL-TIME VISITORS =====
        $realtimeVisitors = PageVisit::where('visited_at', '>=', now()->subMinutes(5))
            ->distinct('session_id')
            ->count('session_id');

        // ===== BUSINESS STATS =====
        $totalLoueurs = Loueur::count();
        $totalVehicles = Vehicle::where('is_active', true)->count();
        $monthBookings = Booking::whereBetween('created_at', [$startOfMonth, now()])->count();
        $monthRevenue = Booking::whereBetween('created_at', [$startOfMonth, now()])
            ->where('status', 'completed')
            ->sum('total_price');

        // ===== RECENT VISITS =====
        $recentVisits = PageVisit::with('vehicle')
            ->orderByDesc('visited_at')
            ->limit(50)
            ->get();

        return [
            // Basic stats
            'todayVisits' => $todayVisits,
            'yesterdayVisits' => $yesterdayVisits,
            'weekVisits' => $weekVisits,
            'monthVisits' => $monthVisits,
            'lastMonthVisits' => $lastMonthVisits,
            'todayUnique' => $todayUnique,
            'monthUnique' => $monthUnique,

            // Geographic
            'topCountries' => $topCountries,
            'topCities' => $topCities,
            'topRegions' => $topRegions,

            // Traffic sources
            'trafficSources' => $trafficSources,
            'trafficByMedium' => $trafficByMedium,
            'utmCampaigns' => $utmCampaigns,

            // Time-based
            'hourlyStats' => $hourlyStats,
            'dayOfWeekStats' => $dayOfWeekStats,
            'dayNames' => $dayNames,
            'dailyVisits' => $dailyVisits,

            // Technology
            'deviceStats' => $deviceStats,
            'browserStats' => $browserStats,
            'osStats' => $osStats,

            // Pages
            'topPages' => $topPages,
            'topVehicles' => $topVehicles,
            'landingPages' => $landingPages,

            // Behavior
            'bounceRate' => $bounceRate,
            'totalSessions' => $totalSessions,

            // Clicks
            'clickStats' => $clickStats,

            // Funnel
            'funnelHome' => $funnelHome,
            'funnelVehicleList' => $funnelVehicleList,
            'funnelVehicle' => $funnelVehicle,
            'funnelBooking' => $funnelBooking,
            'funnelCompleted' => $funnelCompleted,

            // Real-time
            'realtimeVisitors' => $realtimeVisitors,

            // Business
            'totalLoueurs' => $totalLoueurs,
            'totalVehicles' => $totalVehicles,
            'monthBookings' => $monthBookings,
            'monthRevenue' => $monthRevenue,

            // Recent
            'recentVisits' => $recentVisits,

            // IP tracking
            'currentIp' => $currentIp,
            'excludedIps' => $excludedIps,
            'topIps' => $topIps,
            'totalRecords' => $totalRecords,
            'oldestRecord' => $oldestRecord,
        ];
    }
}

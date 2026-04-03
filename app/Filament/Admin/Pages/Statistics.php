<?php

namespace App\Filament\Admin\Pages;

use App\Models\Booking;
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
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $navigationLabel = 'Statistiques';
    protected static ?int $navigationSort = 0;
    protected static string $view = 'filament.admin.pages.statistics';

    /**
     * Use file cache (not database) for stats - much faster on shared hosting
     */
    private static function fileCache(): \Illuminate\Contracts\Cache\Repository
    {
        return Cache::store('file');
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = static::fileCache()->remember('stats_badge', 60, fn () =>
                (int) DB::selectOne("
                    SELECT COUNT(DISTINCT session_id) as c
                    FROM page_visits WHERE visited_at >= ?
                ", [now()->subMinutes(5)->toDateTimeString()])->c
            );
            return $count > 0 ? $count . ' en ligne' : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

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
                    static::fileCache()->forget('admin_stats');
                    Notification::make()
                        ->title("{$deleted} visites supprimées")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getViewData(): array
    {
        $currentIp = request()->ip();
        $excludedIps = config('resadz.excluded_tracking_ips', []);

        // File cache 5 minutes - bypasses slow MySQL cache entirely
        $cached = static::fileCache()->remember('admin_stats', 300, function () {
            $today = Carbon::today()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();
            $startOfWeek = Carbon::now()->startOfWeek()->toDateTimeString();
            $startOfMonth = Carbon::now()->startOfMonth()->toDateTimeString();
            $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth()->toDateTimeString();
            $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth()->toDateTimeString();
            $now = now()->toDateTimeString();
            $sevenDaysAgo = now()->subDays(7)->toDateTimeString();
            $thirtyDaysAgo = now()->subDays(30)->toDateTimeString();

            // ===== BASIC STATS (1 query) =====
            $basicStats = DB::selectOne("
                SELECT
                    SUM(CASE WHEN DATE(visited_at) = ? THEN 1 ELSE 0 END) as today_visits,
                    SUM(CASE WHEN DATE(visited_at) = ? THEN 1 ELSE 0 END) as yesterday_visits,
                    SUM(CASE WHEN visited_at >= ? THEN 1 ELSE 0 END) as week_visits,
                    COUNT(*) as month_visits,
                    COUNT(DISTINCT CASE WHEN DATE(visited_at) = ? THEN ip_address END) as today_unique,
                    COUNT(DISTINCT ip_address) as month_unique
                FROM page_visits
                WHERE visited_at BETWEEN ? AND ?
            ", [$today, $yesterday, $startOfWeek, $today, $startOfMonth, $now]);

            $lastMonthVisits = (int) DB::selectOne("
                SELECT COUNT(*) as c FROM page_visits WHERE visited_at BETWEEN ? AND ?
            ", [$startOfLastMonth, $endOfLastMonth])->c;

            // ===== GEOGRAPHIC (3 queries) =====
            $topCountries = DB::select("
                SELECT country, country_code, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ? AND country IS NOT NULL
                GROUP BY country, country_code ORDER BY visits DESC LIMIT 15
            ", [$startOfMonth, $now]);

            $topCities = DB::select("
                SELECT city, country_code, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ? AND city IS NOT NULL
                GROUP BY city, country_code ORDER BY visits DESC LIMIT 15
            ", [$startOfMonth, $now]);

            $topRegions = DB::select("
                SELECT region, country_code, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ? AND region IS NOT NULL
                GROUP BY region, country_code ORDER BY visits DESC LIMIT 15
            ", [$startOfMonth, $now]);

            // ===== TRAFFIC (3 queries) =====
            $trafficSources = DB::select("
                SELECT traffic_source, traffic_medium, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ?
                GROUP BY traffic_source, traffic_medium ORDER BY visits DESC LIMIT 20
            ", [$startOfMonth, $now]);

            $trafficByMedium = DB::select("
                SELECT traffic_medium, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ?
                GROUP BY traffic_medium ORDER BY visits DESC
            ", [$startOfMonth, $now]);

            $utmCampaigns = DB::select("
                SELECT utm_source, utm_medium, utm_campaign, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ? AND utm_campaign IS NOT NULL
                GROUP BY utm_source, utm_medium, utm_campaign ORDER BY visits DESC LIMIT 10
            ", [$startOfMonth, $now]);

            // ===== TIME STATS (2 queries) =====
            $hourlyRaw = DB::select("
                SELECT HOUR(visited_at) as hour, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ?
                GROUP BY hour ORDER BY hour
            ", [$sevenDaysAgo, $now]);

            $hourlyStats = array_fill(0, 24, 0);
            foreach ($hourlyRaw as $row) {
                $hourlyStats[(int) $row->hour] = (int) $row->visits;
            }

            $dayOfWeekRaw = DB::select("
                SELECT DAYOFWEEK(visited_at) as day, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ?
                GROUP BY day ORDER BY day
            ", [$startOfMonth, $now]);

            $dayNames = [1 => 'Dimanche', 2 => 'Lundi', 3 => 'Mardi', 4 => 'Mercredi', 5 => 'Jeudi', 6 => 'Vendredi', 7 => 'Samedi'];
            $dayOfWeekStats = [];
            foreach ($dayOfWeekRaw as $row) {
                $dayOfWeekStats[(int) $row->day] = (int) $row->visits;
            }

            // ===== TECH STATS (3 queries) =====
            $deviceStats = DB::select("
                SELECT device_type, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ?
                GROUP BY device_type ORDER BY visits DESC
            ", [$startOfMonth, $now]);

            $browserStats = DB::select("
                SELECT browser, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ?
                GROUP BY browser ORDER BY visits DESC LIMIT 10
            ", [$startOfMonth, $now]);

            $osStats = DB::select("
                SELECT os, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ? AND os IS NOT NULL
                GROUP BY os ORDER BY visits DESC LIMIT 10
            ", [$startOfMonth, $now]);

            // ===== PAGES + VEHICLES + BOUNCE (4 queries) =====
            $topPages = DB::select("
                SELECT page_type, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ?
                GROUP BY page_type ORDER BY visits DESC
            ", [$startOfMonth, $now]);

            $landingPages = DB::select("
                SELECT page_type, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ? AND is_landing = 1
                GROUP BY page_type ORDER BY visits DESC
            ", [$startOfMonth, $now]);

            $topVehicles = DB::select("
                SELECT pv.vehicle_id, COUNT(*) as visits, v.full_name, v.image, b.name as brand_name
                FROM page_visits pv
                JOIN vehicles v ON pv.vehicle_id = v.id
                LEFT JOIN brands b ON v.brand_id = b.id
                WHERE pv.visited_at BETWEEN ? AND ? AND pv.vehicle_id IS NOT NULL
                GROUP BY pv.vehicle_id, v.full_name, v.image, b.name
                ORDER BY visits DESC LIMIT 10
            ", [$startOfMonth, $now]);

            $bounceData = DB::selectOne("
                SELECT
                    COUNT(DISTINCT session_id) as total_sessions,
                    (SELECT COUNT(*) FROM (
                        SELECT session_id FROM page_visits
                        WHERE visited_at BETWEEN ? AND ? AND session_id IS NOT NULL
                        GROUP BY session_id HAVING COUNT(*) = 1
                    ) b) as bounced
                FROM page_visits
                WHERE visited_at BETWEEN ? AND ? AND session_id IS NOT NULL
            ", [$startOfMonth, $now, $startOfMonth, $now]);

            $totalSessions = (int) ($bounceData->total_sessions ?? 0);
            $bounceRate = $totalSessions > 0 ? round((($bounceData->bounced ?? 0) / $totalSessions) * 100, 1) : 0;

            // ===== DAILY + CLICKS + FUNNEL + BUSINESS (4 queries) =====
            $dailyVisits = DB::select("
                SELECT DATE(visited_at) as date, COUNT(*) as visits
                FROM page_visits WHERE visited_at BETWEEN ? AND ?
                GROUP BY date ORDER BY date
            ", [$thirtyDaysAgo, $now]);

            $clickStats = [];
            try {
                $clickStats = DB::select("
                    SELECT event_type, COUNT(*) as clicks
                    FROM click_events WHERE clicked_at BETWEEN ? AND ?
                    GROUP BY event_type ORDER BY clicks DESC
                ", [$startOfMonth, $now]);
            } catch (\Exception $e) {}

            $funnel = DB::selectOne("
                SELECT
                    COUNT(DISTINCT CASE WHEN page_type = 'home' THEN session_id END) as home,
                    COUNT(DISTINCT CASE WHEN page_type = 'vehicles_list' THEN session_id END) as vehicle_list,
                    COUNT(DISTINCT CASE WHEN page_type = 'vehicle' THEN session_id END) as vehicle,
                    COUNT(DISTINCT CASE WHEN page_type = 'booking' THEN session_id END) as booking
                FROM page_visits WHERE visited_at BETWEEN ? AND ?
            ", [$startOfMonth, $now]);

            $business = DB::selectOne("
                SELECT
                    (SELECT COUNT(*) FROM loueurs) as total_loueurs,
                    (SELECT COUNT(*) FROM vehicles WHERE is_active = 1) as total_vehicles,
                    (SELECT COUNT(*) FROM bookings WHERE created_at BETWEEN ? AND ?) as month_bookings,
                    (SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE created_at BETWEEN ? AND ? AND status = 'completed') as month_revenue
            ", [$startOfMonth, $now, $startOfMonth, $now]);

            // ===== IPs + TOTAL + RECENT (3 queries) =====
            $topIps = DB::select("
                SELECT ip_address, COUNT(*) as visits, MIN(visited_at) as first_visit, MAX(visited_at) as last_visit
                FROM page_visits WHERE visited_at BETWEEN ? AND ?
                GROUP BY ip_address ORDER BY visits DESC LIMIT 20
            ", [$startOfMonth, $now]);

            $totalRecords = (int) DB::selectOne("SELECT COUNT(*) as c FROM page_visits")->c;

            $recentVisits = DB::select("
                SELECT visited_at, city, country, country_code, page_type,
                       device_type, browser, traffic_source, utm_campaign,
                       is_landing, ip_address
                FROM page_visits ORDER BY visited_at DESC LIMIT 30
            ");

            return [
                'todayVisits' => (int) ($basicStats->today_visits ?? 0),
                'yesterdayVisits' => (int) ($basicStats->yesterday_visits ?? 0),
                'weekVisits' => (int) ($basicStats->week_visits ?? 0),
                'monthVisits' => (int) ($basicStats->month_visits ?? 0),
                'lastMonthVisits' => $lastMonthVisits,
                'todayUnique' => (int) ($basicStats->today_unique ?? 0),
                'monthUnique' => (int) ($basicStats->month_unique ?? 0),
                'topCountries' => $topCountries,
                'topCities' => $topCities,
                'topRegions' => $topRegions,
                'trafficSources' => $trafficSources,
                'trafficByMedium' => $trafficByMedium,
                'utmCampaigns' => $utmCampaigns,
                'hourlyStats' => $hourlyStats,
                'dayOfWeekStats' => $dayOfWeekStats,
                'dayNames' => $dayNames,
                'dailyVisits' => $dailyVisits,
                'deviceStats' => $deviceStats,
                'browserStats' => $browserStats,
                'osStats' => $osStats,
                'topPages' => $topPages,
                'topVehicles' => $topVehicles,
                'landingPages' => $landingPages,
                'bounceRate' => $bounceRate,
                'totalSessions' => $totalSessions,
                'clickStats' => $clickStats,
                'funnelHome' => (int) ($funnel->home ?? 0),
                'funnelVehicleList' => (int) ($funnel->vehicle_list ?? 0),
                'funnelVehicle' => (int) ($funnel->vehicle ?? 0),
                'funnelBooking' => (int) ($funnel->booking ?? 0),
                'funnelCompleted' => (int) DB::table('bookings')->whereBetween('created_at', [Carbon::now()->startOfMonth(), now()])->count(),
                'totalLoueurs' => (int) ($business->total_loueurs ?? 0),
                'totalVehicles' => (int) ($business->total_vehicles ?? 0),
                'monthBookings' => (int) ($business->month_bookings ?? 0),
                'monthRevenue' => (float) ($business->month_revenue ?? 0),
                'recentVisits' => $recentVisits,
                'topIps' => $topIps,
                'totalRecords' => $totalRecords,
                'oldestRecord' => null,
            ];
        });

        $cached['realtimeVisitors'] = (int) DB::selectOne("
            SELECT COUNT(DISTINCT session_id) as c
            FROM page_visits WHERE visited_at >= ?
        ", [now()->subMinutes(5)->toDateTimeString()])->c;

        $cached['currentIp'] = $currentIp;
        $cached['excludedIps'] = $excludedIps;

        return $cached;
    }
}

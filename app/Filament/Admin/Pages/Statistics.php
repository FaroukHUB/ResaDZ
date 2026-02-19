<?php

namespace App\Filament\Admin\Pages;

use App\Models\PageVisit;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Loueur;
use Filament\Pages\Page;
use Carbon\Carbon;

class Statistics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?string $navigationLabel = 'Statistiques';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.admin.pages.statistics';

    public function getViewData(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // Today visits
        $todayVisits = PageVisit::whereDate('visited_at', $today)->count();
        $yesterdayVisits = PageVisit::whereDate('visited_at', $yesterday)->count();

        // Week visits
        $weekVisits = PageVisit::whereBetween('visited_at', [$startOfWeek, now()])->count();

        // Month visits
        $monthVisits = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])->count();
        $lastMonthVisits = PageVisit::whereBetween('visited_at', [$startOfLastMonth, $endOfLastMonth])->count();

        // Unique visitors (by IP)
        $todayUnique = PageVisit::whereDate('visited_at', $today)->distinct('ip_address')->count('ip_address');
        $monthUnique = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])->distinct('ip_address')->count('ip_address');

        // Top pages
        $topPages = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->selectRaw('page_type, COUNT(*) as visits')
            ->groupBy('page_type')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        // Top vehicles viewed
        $topVehicles = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->whereNotNull('vehicle_id')
            ->selectRaw('vehicle_id, COUNT(*) as visits')
            ->groupBy('vehicle_id')
            ->orderByDesc('visits')
            ->limit(10)
            ->with('vehicle')
            ->get();

        // Device breakdown
        $deviceStats = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->selectRaw('device_type, COUNT(*) as visits')
            ->groupBy('device_type')
            ->orderByDesc('visits')
            ->get();

        // Browser breakdown
        $browserStats = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->selectRaw('browser, COUNT(*) as visits')
            ->groupBy('browser')
            ->orderByDesc('visits')
            ->limit(5)
            ->get();

        // Daily visits for chart (last 30 days)
        $dailyVisits = PageVisit::whereBetween('visited_at', [now()->subDays(30), now()])
            ->selectRaw('DATE(visited_at) as date, COUNT(*) as visits')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Business stats
        $totalLoueurs = Loueur::count();
        $totalVehicles = Vehicle::where('is_active', true)->count();
        $monthBookings = Booking::whereBetween('created_at', [$startOfMonth, now()])->count();
        $monthRevenue = Booking::whereBetween('created_at', [$startOfMonth, now()])
            ->where('status', 'completed')
            ->sum('total_price');

        return [
            'todayVisits' => $todayVisits,
            'yesterdayVisits' => $yesterdayVisits,
            'weekVisits' => $weekVisits,
            'monthVisits' => $monthVisits,
            'lastMonthVisits' => $lastMonthVisits,
            'todayUnique' => $todayUnique,
            'monthUnique' => $monthUnique,
            'topPages' => $topPages,
            'topVehicles' => $topVehicles,
            'deviceStats' => $deviceStats,
            'browserStats' => $browserStats,
            'dailyVisits' => $dailyVisits,
            'totalLoueurs' => $totalLoueurs,
            'totalVehicles' => $totalVehicles,
            'monthBookings' => $monthBookings,
            'monthRevenue' => $monthRevenue,
        ];
    }
}

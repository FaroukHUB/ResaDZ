<?php

namespace App\Filament\Admin\Pages;

use App\Models\PageVisit;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Loueur;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class Statistics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?string $navigationLabel = 'Statistiques';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.admin.pages.statistics';

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
                        ->title("$deleted visites supprimées")
                        ->success()
                        ->send();
                }),
        ];
    }

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

        // ===== DETAILED IP STATS =====
        // Top IPs this month with details
        $topIps = PageVisit::whereBetween('visited_at', [$startOfMonth, now()])
            ->selectRaw('ip_address, COUNT(*) as visits, MAX(visited_at) as last_visit, MIN(visited_at) as first_visit')
            ->groupBy('ip_address')
            ->orderByDesc('visits')
            ->limit(20)
            ->get();

        // Recent visits with full details
        $recentVisits = PageVisit::with('vehicle')
            ->orderByDesc('visited_at')
            ->limit(50)
            ->get();

        // Excluded IPs from config
        $excludedIps = config('resadz.excluded_tracking_ips', []);

        // Your current IP (for reference)
        $currentIp = request()->ip();

        // Total data stored
        $totalRecords = PageVisit::count();
        $oldestRecord = PageVisit::orderBy('visited_at')->first();

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
            // New detailed data
            'topIps' => $topIps,
            'recentVisits' => $recentVisits,
            'excludedIps' => $excludedIps,
            'currentIp' => $currentIp,
            'totalRecords' => $totalRecords,
            'oldestRecord' => $oldestRecord,
        ];
    }
}

<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Booking;
use App\Models\Loueur;
use App\Models\PageVisit;
use App\Models\Vehicle;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Visits stats
        $visitsToday = PageVisit::whereDate('created_at', $today)->count();
        $visitsThisMonth = PageVisit::where('created_at', '>=', $thisMonth)->count();
        $visitsLastMonth = PageVisit::whereBetween('created_at', [$lastMonth, $thisMonth])->count();
        $visitsTrend = $visitsLastMonth > 0
            ? round((($visitsThisMonth - $visitsLastMonth) / $visitsLastMonth) * 100, 1)
            : 0;

        // Vehicles stats
        $totalVehicles = Vehicle::count();
        $activeVehicles = Vehicle::where('is_active', true)->where('status', 'available')->count();

        // Loueurs stats
        $totalLoueurs = Loueur::count();
        $activeLoueurs = Loueur::where('is_active', true)->where('is_suspended', false)->count();

        // Bookings stats
        $bookingsThisMonth = Booking::where('created_at', '>=', $thisMonth)->count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        // Leads stats
        $leadsThisMonth = Lead::where('created_at', '>=', $thisMonth)->count();

        return [
            Stat::make('Visites aujourd\'hui', number_format($visitsToday))
                ->description('Ce mois: ' . number_format($visitsThisMonth))
                ->descriptionIcon($visitsTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($visitsTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getVisitsChart()),

            Stat::make('Véhicules', number_format($totalVehicles))
                ->description($activeVehicles . ' actifs / disponibles')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make('Loueurs', number_format($totalLoueurs))
                ->description($activeLoueurs . ' actifs')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),

            Stat::make('Réservations', number_format($bookingsThisMonth))
                ->description($pendingBookings . ' en attente')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),

            Stat::make('Leads ce mois', number_format($leadsThisMonth))
                ->description('Inscriptions newsletter')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('success'),
        ];
    }

    protected function getVisitsChart(): array
    {
        // Last 7 days visits
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $data[] = PageVisit::whereDate('created_at', $date)->count();
        }
        return $data;
    }
}

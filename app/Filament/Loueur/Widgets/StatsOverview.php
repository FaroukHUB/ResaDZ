<?php

namespace App\Filament\Loueur\Widgets;

use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $loueur = Auth::user()->loueur;

        if (!$loueur) {
            return [];
        }

        $now = Carbon::now();

        // Véhicules actifs
        $activeVehicles = Vehicle::where('loueur_id', $loueur->id)
            ->where('is_active', true)
            ->count();

        // Réservations ce mois
        $monthlyBookings = Booking::where('loueur_id', $loueur->id)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        // Réservations mois dernier (pour comparaison)
        $lastMonthBookings = Booking::where('loueur_id', $loueur->id)
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereYear('created_at', $now->copy()->subMonth()->year)
            ->count();

        $bookingTrend = $lastMonthBookings > 0
            ? round(($monthlyBookings - $lastMonthBookings) / $lastMonthBookings * 100)
            : ($monthlyBookings > 0 ? 100 : 0);

        // Réservations en attente
        $pendingBookings = Booking::where('loueur_id', $loueur->id)
            ->where('status', 'pending')
            ->count();

        // Réservations actives (en cours)
        $activeBookings = Booking::where('loueur_id', $loueur->id)
            ->where('status', 'active')
            ->count();

        // Revenus ce mois
        $monthlyIncome = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'income')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        // Dépenses ce mois
        $monthlyExpenses = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        $netProfit = $monthlyIncome - $monthlyExpenses;

        // Taux d'occupation
        $totalVehicles = Vehicle::where('loueur_id', $loueur->id)->where('is_active', true)->count();
        $occupiedVehicles = Booking::where('loueur_id', $loueur->id)
            ->where('status', 'active')
            ->distinct('vehicle_id')
            ->count('vehicle_id');
        $occupancyRate = $totalVehicles > 0 ? round(($occupiedVehicles / $totalVehicles) * 100) : 0;

        return [
            Stat::make('Véhicules actifs', $activeVehicles)
                ->description($occupancyRate . '% en location')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
            Stat::make('Réservations ce mois', $monthlyBookings)
                ->description($pendingBookings . ' en attente | ' . ($bookingTrend >= 0 ? '+' : '') . $bookingTrend . '% vs mois dernier')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('En location', $activeBookings)
                ->description('Taux occupation : ' . $occupancyRate . '%')
                ->descriptionIcon('heroicon-m-key')
                ->color('success'),
            Stat::make('Bénéfice net du mois', number_format($netProfit, 0, ',', ' ') . ' DA')
                ->description(number_format($monthlyIncome, 0, ',', ' ') . ' DA revenus - ' . number_format($monthlyExpenses, 0, ',', ' ') . ' DA dépenses')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($netProfit >= 0 ? 'success' : 'danger'),
        ];
    }
}

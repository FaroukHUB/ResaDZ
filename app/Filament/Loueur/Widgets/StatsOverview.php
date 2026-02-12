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

        // Véhicules actifs
        $activeVehicles = Vehicle::where('loueur_id', $loueur->id)
            ->where('is_active', true)
            ->count();

        // Réservations ce mois
        $monthlyBookings = Booking::where('loueur_id', $loueur->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Revenus ce mois
        $monthlyIncome = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->where('status', 'completed')
            ->sum('amount');

        // Dépenses ce mois
        $monthlyExpenses = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->where('status', 'completed')
            ->sum('amount');

        // Réservations en attente
        $pendingBookings = Booking::where('loueur_id', $loueur->id)
            ->where('status', 'pending')
            ->count();

        // Réservations actives (en cours)
        $activeBookings = Booking::where('loueur_id', $loueur->id)
            ->where('status', 'active')
            ->count();

        return [
            Stat::make('Véhicules actifs', $activeVehicles)
                ->description('Dans votre flotte')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
            Stat::make('Réservations ce mois', $monthlyBookings)
                ->description($pendingBookings . ' en attente')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('En location', $activeBookings)
                ->description('Véhicules en cours')
                ->descriptionIcon('heroicon-m-key')
                ->color('success'),
            Stat::make('Revenus du mois', number_format($monthlyIncome, 0, ',', ' ') . ' DA')
                ->description('- ' . number_format($monthlyExpenses, 0, ',', ' ') . ' DA dépenses')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($monthlyIncome > $monthlyExpenses ? 'success' : 'warning'),
        ];
    }
}

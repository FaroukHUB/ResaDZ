<?php

namespace App\Filament\Chauffeur\Widgets;

use App\Models\DeliveryBooking;
use App\Models\TransferBooking;
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

        // Courses ce mois (transferts + livraisons)
        $monthlyTransfers = TransferBooking::where('loueur_id', $loueur->id)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $monthlyDeliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $totalCourses = $monthlyTransfers + $monthlyDeliveries;

        // Mois dernier pour comparaison
        $lastMonthTransfers = TransferBooking::where('loueur_id', $loueur->id)
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereYear('created_at', $now->copy()->subMonth()->year)
            ->count();

        $lastMonthDeliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->whereYear('created_at', $now->copy()->subMonth()->year)
            ->count();

        $lastMonthTotal = $lastMonthTransfers + $lastMonthDeliveries;

        $courseTrend = $lastMonthTotal > 0
            ? round(($totalCourses - $lastMonthTotal) / $lastMonthTotal * 100)
            : ($totalCourses > 0 ? 100 : 0);

        // Courses en attente
        $pendingTransfers = TransferBooking::where('loueur_id', $loueur->id)
            ->where('status', 'pending')
            ->count();

        $pendingDeliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->where('status', 'pending')
            ->count();

        $totalPending = $pendingTransfers + $pendingDeliveries;

        // Courses en cours
        $activeTransfers = TransferBooking::where('loueur_id', $loueur->id)
            ->where('status', 'confirmed')
            ->count();

        $activeDeliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->whereIn('status', ['confirmed', 'picked_up', 'in_transit'])
            ->count();

        $totalActive = $activeTransfers + $activeDeliveries;

        // CA ce mois (transferts + livraisons termines)
        $caTransfers = TransferBooking::where('loueur_id', $loueur->id)
            ->where('status', 'completed')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('price');

        $caDeliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->where('status', 'delivered')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('price');

        $totalCA = $caTransfers + $caDeliveries;

        // Commission due (non payee)
        $commissionTransfers = TransferBooking::where('loueur_id', $loueur->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('commission_paid', false)
            ->sum('commission_amount');

        $commissionDeliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->whereIn('status', ['confirmed', 'picked_up', 'in_transit', 'delivered'])
            ->where('commission_paid', false)
            ->sum('commission_amount');

        $totalCommissionDue = $commissionTransfers + $commissionDeliveries;

        // Revenus nets (CA - Commission)
        $netRevenue = $totalCA - $totalCommissionDue;

        return [
            Stat::make('Courses ce mois', $totalCourses)
                ->description($monthlyTransfers . ' transferts | ' . $monthlyDeliveries . ' livraisons')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('En attente', $totalPending)
                ->description($totalActive . ' en cours | ' . ($courseTrend >= 0 ? '+' : '') . $courseTrend . '% vs mois dernier')
                ->descriptionIcon('heroicon-m-clock')
                ->color($totalPending > 0 ? 'warning' : 'success'),

            Stat::make('CA ce mois', number_format($totalCA, 0, ',', ' ') . ' DA')
                ->description(number_format($caTransfers, 0, ',', ' ') . ' DA transferts | ' . number_format($caDeliveries, 0, ',', ' ') . ' DA livraisons')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Commission due (10%)', number_format($totalCommissionDue, 0, ',', ' ') . ' DA')
                ->description('Net apres commission: ' . number_format($netRevenue, 0, ',', ' ') . ' DA')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color($totalCommissionDue > 0 ? 'danger' : 'success'),
        ];
    }
}

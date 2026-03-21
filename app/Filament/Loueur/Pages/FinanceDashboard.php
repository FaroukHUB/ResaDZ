<?php

namespace App\Filament\Loueur\Pages;

use App\Models\Booking;
use App\Models\DeliveryBooking;
use App\Models\Transaction;
use App\Models\TransferBooking;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FinanceDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Finances';

    protected static ?string $navigationLabel = 'Tableau de bord';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.loueur.pages.finance-dashboard';

    public function getViewData(): array
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) {
            return [];
        }

        // Données différentes selon le type de compte
        if ($loueur->isTaxi()) {
            return $this->getTaxiFinanceData($loueur);
        }

        return $this->getLoueurFinanceData($loueur);
    }

    /**
     * Données financières pour les chauffeurs/taxis
     */
    protected function getTaxiFinanceData($loueur): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // --- CA TRANSFERTS ---
        $todayTransfers = TransferBooking::where('loueur_id', $loueur->id)
            ->where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('price');

        $weekTransfers = TransferBooking::where('loueur_id', $loueur->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfWeek, $today->endOfDay()])
            ->sum('price');

        $monthTransfers = TransferBooking::where('loueur_id', $loueur->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])
            ->sum('price');

        // --- CA LIVRAISONS ---
        $todayDeliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->where('status', 'delivered')
            ->whereDate('created_at', $today)
            ->sum('price');

        $weekDeliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$startOfWeek, $today->endOfDay()])
            ->sum('price');

        $monthDeliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])
            ->sum('price');

        // --- TOTAUX ---
        $todayIncome = $todayTransfers + $todayDeliveries;
        $weekIncome = $weekTransfers + $weekDeliveries;
        $monthIncome = $monthTransfers + $monthDeliveries;

        // --- DEPENSES (transactions) ---
        $todayExpenses = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'expense')
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        $weekExpenses = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfWeek, $today])
            ->sum('amount');

        $monthExpenses = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $today])
            ->sum('amount');

        // --- COMMISSION DUE ---
        $commissionTransfers = TransferBooking::where('loueur_id', $loueur->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('commission_paid', false)
            ->sum('commission_amount');

        $commissionDeliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->whereIn('status', ['confirmed', 'picked_up', 'in_transit', 'delivered'])
            ->where('commission_paid', false)
            ->sum('commission_amount');

        $totalCommissionDue = $commissionTransfers + $commissionDeliveries;

        // --- COURSES EN ATTENTE ---
        $pendingTransfersIncome = TransferBooking::where('loueur_id', $loueur->id)
            ->where('status', 'confirmed')
            ->sum('price');

        $pendingDeliveriesIncome = DeliveryBooking::where('loueur_id', $loueur->id)
            ->whereIn('status', ['confirmed', 'picked_up', 'in_transit'])
            ->sum('price');

        $pendingIncome = $pendingTransfersIncome + $pendingDeliveriesIncome;

        // --- TRANSACTIONS RECENTES ---
        $recentTransactions = Transaction::where('loueur_id', $loueur->id)
            ->with(['vehicle', 'booking', 'expenseCategory'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // --- COURSES RECENTES ---
        $recentTransfers = TransferBooking::where('loueur_id', $loueur->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentDeliveries = DeliveryBooking::where('loueur_id', $loueur->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // --- STATS COMPTEUR ---
        $monthTransfersCount = TransferBooking::where('loueur_id', $loueur->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $monthDeliveriesCount = DeliveryBooking::where('loueur_id', $loueur->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        return [
            'isTaxi' => true,
            // Totaux
            'todayIncome' => $todayIncome,
            'todayExpenses' => $todayExpenses,
            'todayNet' => $todayIncome - $todayExpenses,
            'weekIncome' => $weekIncome,
            'weekExpenses' => $weekExpenses,
            'weekNet' => $weekIncome - $weekExpenses,
            'monthIncome' => $monthIncome,
            'monthExpenses' => $monthExpenses,
            'monthNet' => $monthIncome - $monthExpenses,
            // Detail par type
            'todayTransfers' => $todayTransfers,
            'todayDeliveries' => $todayDeliveries,
            'weekTransfers' => $weekTransfers,
            'weekDeliveries' => $weekDeliveries,
            'monthTransfers' => $monthTransfers,
            'monthDeliveries' => $monthDeliveries,
            // Commission
            'commissionTransfers' => $commissionTransfers,
            'commissionDeliveries' => $commissionDeliveries,
            'totalCommissionDue' => $totalCommissionDue,
            // En attente
            'pendingIncome' => $pendingIncome,
            'pendingTransfersIncome' => $pendingTransfersIncome,
            'pendingDeliveriesIncome' => $pendingDeliveriesIncome,
            // Transactions et courses recentes
            'recentTransactions' => $recentTransactions,
            'recentTransfers' => $recentTransfers,
            'recentDeliveries' => $recentDeliveries,
            // Compteurs
            'monthTransfersCount' => $monthTransfersCount,
            'monthDeliveriesCount' => $monthDeliveriesCount,
        ];
    }

    /**
     * Données financières pour les loueurs de véhicules
     */
    protected function getLoueurFinanceData($loueur): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Today's totals
        $todayIncome = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'income')
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        $todayExpenses = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'expense')
            ->whereDate('transaction_date', $today)
            ->sum('amount');

        // Week totals
        $weekIncome = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$startOfWeek, $today])
            ->sum('amount');

        $weekExpenses = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfWeek, $today])
            ->sum('amount');

        // Month totals
        $monthIncome = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$startOfMonth, $today])
            ->sum('amount');

        $monthExpenses = Transaction::where('loueur_id', $loueur->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth, $today])
            ->sum('amount');

        // Recent transactions (last 10)
        $recentTransactions = Transaction::where('loueur_id', $loueur->id)
            ->with(['vehicle', 'booking', 'expenseCategory'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Pending bookings income (confirmed but not yet completed)
        $pendingIncome = Booking::where('loueur_id', $loueur->id)
            ->whereIn('status', ['confirmed', 'active'])
            ->sum('total_price');

        return [
            'isTaxi' => false,
            'todayIncome' => $todayIncome,
            'todayExpenses' => $todayExpenses,
            'todayNet' => $todayIncome - $todayExpenses,
            'weekIncome' => $weekIncome,
            'weekExpenses' => $weekExpenses,
            'weekNet' => $weekIncome - $weekExpenses,
            'monthIncome' => $monthIncome,
            'monthExpenses' => $monthExpenses,
            'monthNet' => $monthIncome - $monthExpenses,
            'recentTransactions' => $recentTransactions,
            'pendingIncome' => $pendingIncome,
        ];
    }
}

<?php

namespace App\Filament\Loueur\Pages;

use App\Models\Transaction;
use App\Models\Booking;
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

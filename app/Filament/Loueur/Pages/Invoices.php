<?php

namespace App\Filament\Loueur\Pages;

use App\Models\Invoice;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Invoices extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.loueur.pages.invoices';

    protected static ?string $navigationLabel = 'Mes factures';

    protected static ?string $title = 'Mes factures';

    protected static ?int $navigationSort = 16;

    public static function getNavigationBadge(): ?string
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) return null;

        $count = Invoice::where('loueur_id', $loueur->id)
            ->whereIn('status', ['sent', 'overdue'])
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function getLoueur()
    {
        return Auth::user()?->loueur;
    }

    public function getInvoices()
    {
        $loueur = $this->getLoueur();
        if (!$loueur) {
            return collect();
        }

        return Invoice::where('loueur_id', $loueur->id)
            ->with('items')
            ->orderByDesc('issue_date')
            ->get();
    }

    public function getTotalUnpaid(): float
    {
        $loueur = $this->getLoueur();
        if (!$loueur) {
            return 0;
        }

        return Invoice::where('loueur_id', $loueur->id)
            ->whereIn('status', ['sent', 'overdue'])
            ->sum('total');
    }

    public function getTotalPaid(): float
    {
        $loueur = $this->getLoueur();
        if (!$loueur) {
            return 0;
        }

        return Invoice::where('loueur_id', $loueur->id)
            ->where('status', 'paid')
            ->sum('total');
    }

    protected function getViewData(): array
    {
        return [
            'invoices' => $this->getInvoices(),
            'totalUnpaid' => $this->getTotalUnpaid(),
            'totalPaid' => $this->getTotalPaid(),
            'statuses' => Invoice::getStatuses(),
        ];
    }
}

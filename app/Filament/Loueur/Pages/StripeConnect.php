<?php

namespace App\Filament\Loueur\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class StripeConnect extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Finances';
    protected static ?string $navigationLabel = 'Paiement en ligne';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.loueur.pages.stripe-connect';

    public string $onlinePaymentMode = 'advance_only';

    public function mount(): void
    {
        $loueur = Auth::user()?->loueur;
        $this->onlinePaymentMode = $loueur?->getSetting('online_payment_mode', 'advance_only') ?? 'advance_only';
    }

    public function updatePaymentMode(): void
    {
        $loueur = Auth::user()?->loueur;
        if ($loueur) {
            $loueur->setSetting('online_payment_mode', $this->onlinePaymentMode, 'string');
            $this->dispatch('notify', message: 'Préférences de paiement enregistrées.');
        }
    }

    public function getViewData(): array
    {
        $loueur = Auth::user()?->loueur;

        return [
            'loueur' => $loueur,
            'isConnected' => $loueur?->stripe_onboarding_complete ?? false,
            'hasAccount' => !empty($loueur?->stripe_account_id),
        ];
    }
}

<?php

namespace App\Filament\Loueur\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class StripeConnect extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Finances';
    protected static ?string $navigationLabel = 'Paiement en ligne';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.loueur.pages.stripe-connect';

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

<?php

namespace App\Filament\Loueur\Pages;

use App\Models\Loueur;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class OnboardingChoice extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Choix du profil';

    protected static ?string $title = 'Bienvenue sur ResaDZ';

    protected static ?int $navigationSort = -2;

    protected static string $view = 'filament.loueur.pages.onboarding-choice';

    protected static ?string $slug = 'onboarding-choice';

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;

        return $loueur
            && !$loueur->hasCompletedOnboarding()
            && $loueur->onboarding_step === 0
            && empty($loueur->account_type);
    }

    public function mount(): void
    {
        $loueur = Auth::user()?->loueur;

        if (!$loueur) {
            $this->redirect(route('filament.loueur.pages.dashboard'));
            return;
        }

        // If account_type is already set, redirect to proper onboarding
        if (!empty($loueur->account_type)) {
            if ($loueur->account_type === 'taxi') {
                $this->redirect(route('filament.loueur.pages.onboarding-chauffeur'));
            } else {
                $this->redirect(route('filament.loueur.pages.onboarding'));
            }
            return;
        }

        // If onboarding already completed, redirect to dashboard
        if ($loueur->hasCompletedOnboarding()) {
            $this->redirect(route('filament.loueur.pages.dashboard'));
            return;
        }
    }

    public function chooseLoueur(): void
    {
        $loueur = Auth::user()->loueur;

        $loueur->update(['account_type' => 'loueur']);

        Notification::make()
            ->title('Bienvenue, loueur !')
            ->body('Configurons votre espace de location.')
            ->success()
            ->send();

        $this->redirect(route('filament.loueur.pages.onboarding'));
    }

    public function chooseChauffeur(): void
    {
        $loueur = Auth::user()->loueur;

        $loueur->update(['account_type' => 'taxi']);
        $loueur->setSetting('onboarding_type', 'chauffeur', 'string');

        Notification::make()
            ->title('Bienvenue, chauffeur !')
            ->body('Configurons votre espace de transport.')
            ->success()
            ->send();

        $this->redirect(route('filament.loueur.pages.onboarding-chauffeur'));
    }
}

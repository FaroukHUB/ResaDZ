<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureUserIsLoueur;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class LoueurPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('loueur')
            ->path('loueur')
            ->login()
            ->font('Cairo')
            ->colors([
                'primary' => Color::Amber,
                'danger' => Color::Red,
                'success' => Color::Green,
                'warning' => Color::Orange,
            ])
            ->brandName(\App\Models\Setting::get('company_name', 'ResaDZ') . ' - Espace Loueur')
            ->favicon('/favicon.ico')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Loueur/Resources'), for: 'App\\Filament\\Loueur\\Resources')
            ->discoverPages(in: app_path('Filament/Loueur/Pages'), for: 'App\\Filament\\Loueur\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Loueur/Widgets'), for: 'App\\Filament\\Loueur\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsLoueur::class, // Vérifie que c'est bien un loueur
            ])
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make('Catalogue')
                    ->label('Catalogue')
                    ->icon('heroicon-o-squares-2x2')
                    ->collapsible(),
                \Filament\Navigation\NavigationGroup::make('Réservations')
                    ->label('Réservations')
                    ->icon('heroicon-o-calendar-days')
                    ->collapsible(),
                \Filament\Navigation\NavigationGroup::make('Chauffeur')
                    ->label('Chauffeur')
                    ->icon('heroicon-o-truck')
                    ->collapsible(),
                \Filament\Navigation\NavigationGroup::make('Livraison')
                    ->label('Livraison')
                    ->icon('heroicon-o-cube')
                    ->collapsible(),
                \Filament\Navigation\NavigationGroup::make('Finances')
                    ->label('Finances')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible(),
                \Filament\Navigation\NavigationGroup::make('Marketing')
                    ->label('Marketing')
                    ->icon('heroicon-o-megaphone')
                    ->collapsible(),
                \Filament\Navigation\NavigationGroup::make('Configuration')
                    ->label('Configuration')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),
            ])
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn () => $this->renderOnboardingBanner()
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => $this->renderChauffeurTheme()
            )
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn () => $this->renderChauffeurBodyClass()
            );
    }

    protected function renderChauffeurTheme(): string
    {
        $user = Auth::user();
        if (!$user || !$user->loueur || $user->loueur->account_type !== 'taxi') {
            return '';
        }

        return '<link rel="stylesheet" href="/css/chauffeur-theme.css">';
    }

    protected function renderChauffeurBodyClass(): string
    {
        $user = Auth::user();
        if (!$user || !$user->loueur || $user->loueur->account_type !== 'taxi') {
            return '';
        }

        return '<script>document.body.classList.add("is-chauffeur");</script>';
    }

    protected function renderOnboardingBanner(): string
    {
        $user = Auth::user();

        if (!$user || !$user->loueur) {
            return '';
        }

        $loueur = $user->loueur;

        // Don't show on the onboarding page itself
        if (request()->routeIs('filament.loueur.pages.onboarding')) {
            return '';
        }

        if ($loueur->hasCompletedOnboarding()) {
            return '';
        }

        $progress = $loueur->getOnboardingProgress();
        $currentStep = $loueur->onboarding_step + 1;
        $totalSteps = count(\App\Models\Loueur::ONBOARDING_STEPS);

        return Blade::render('
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-3">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="font-semibold">Configuration incomplète</p>
                            <p class="text-sm text-amber-100">Finalisez votre profil pour recevoir des réservations ({{ $progress }}% terminé)</p>
                        </div>
                    </div>
                    <a href="{{ route(\'filament.loueur.pages.onboarding\') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white text-amber-600 font-semibold rounded-lg hover:bg-amber-50 transition-colors whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Continuer (étape {{ $currentStep }}/{{ $totalSteps }})
                    </a>
                </div>
            </div>
        ', [
            'progress' => $progress,
            'currentStep' => $currentStep,
            'totalSteps' => $totalSteps,
        ]);
    }
}

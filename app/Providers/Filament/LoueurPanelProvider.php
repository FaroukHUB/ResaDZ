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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
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
                'Catalogue',
                'Réservations',
                'Finances',
                'Configuration',
            ]);
    }
}

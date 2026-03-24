<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Widgets\QuickLinksWidget;
use App\Filament\Admin\Widgets\StatsOverviewWidget;
use App\Http\Middleware\EnsureUserIsAdmin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    use \App\Traits\HasDynamicFavicon;
    use \App\Traits\HasPwa;
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->font('Cairo')
            ->colors([
                'primary' => Color::Amber,
                'danger' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'info' => Color::Sky,
                'gray' => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                \Filament\Pages\Dashboard::class,
            ])
            ->widgets([
                StatsOverviewWidget::class,
                QuickLinksWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Gestion')
                    ->icon('heroicon-o-building-office-2')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Catalogue')
                    ->icon('heroicon-o-squares-2x2')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Marketing')
                    ->icon('heroicon-o-megaphone')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Contenu')
                    ->icon('heroicon-o-document-text')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('Paramètres')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->collapsed(),
            ])
            ->brandName(\App\Models\Setting::get('company_name', 'ResaDZ') . ' Admin')
            ->favicon($this->getFaviconUrl())
            ->sidebarCollapsibleOnDesktop()
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
                EnsureUserIsAdmin::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => $this->renderPwaHead()
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => $this->renderPwaScripts()
            )
            ->renderHook(
                PanelsRenderHook::CONTENT_START,
                fn () => $this->renderPwaInstallCard()
            );
    }
}

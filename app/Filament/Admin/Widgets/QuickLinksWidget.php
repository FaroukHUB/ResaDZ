<?php

namespace App\Filament\Admin\Widgets;

use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Loueur;
use App\Models\Vehicle;
use Filament\Widgets\Widget;

class QuickLinksWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.quick-links-widget';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function getCategories(): array
    {
        return [
            [
                'title' => 'Gestion',
                'color' => 'amber',
                'links' => [
                    [
                        'label' => 'Loueurs',
                        'description' => Loueur::count() . ' loueurs',
                        'url' => '/admin/loueurs',
                        'icon' => 'heroicon-o-building-storefront',
                        'badge' => Loueur::where('is_suspended', true)->count() ?: null,
                        'badgeColor' => 'danger',
                    ],
                    [
                        'label' => 'Revenus & Commissions',
                        'description' => 'Suivi financier',
                        'url' => '/admin/revenues',
                        'icon' => 'heroicon-o-currency-dollar',
                    ],
                ],
            ],
            [
                'title' => 'Catalogue',
                'color' => 'blue',
                'links' => [
                    [
                        'label' => 'Véhicules',
                        'description' => Vehicle::count() . ' véhicules',
                        'url' => '/admin/vehicles',
                        'icon' => 'heroicon-o-truck',
                        'badge' => Vehicle::where('is_active', false)->count() ?: null,
                        'badgeColor' => 'warning',
                    ],
                    [
                        'label' => 'Marques',
                        'description' => Brand::count() . ' marques',
                        'url' => '/admin/brands',
                        'icon' => 'heroicon-o-bookmark',
                    ],
                    [
                        'label' => 'Catégories',
                        'description' => Category::count() . ' catégories',
                        'url' => '/admin/categories',
                        'icon' => 'heroicon-o-tag',
                    ],
                    [
                        'label' => 'Sélection accueil',
                        'description' => 'Véhicules en vedette',
                        'url' => '/admin/home-selection',
                        'icon' => 'heroicon-o-star',
                    ],
                ],
            ],
            [
                'title' => 'Contenu',
                'color' => 'green',
                'links' => [
                    [
                        'label' => 'Actualités',
                        'description' => BlogPost::count() . ' articles',
                        'url' => '/admin/blog-posts',
                        'icon' => 'heroicon-o-newspaper',
                        'badge' => BlogPost::whereNull('published_at')->count() ?: null,
                        'badgeColor' => 'info',
                    ],
                ],
            ],
            [
                'title' => 'Analytics',
                'color' => 'purple',
                'links' => [
                    [
                        'label' => 'Statistiques',
                        'description' => 'Analyses détaillées',
                        'url' => '/admin/statistics',
                        'icon' => 'heroicon-o-chart-bar',
                    ],
                ],
            ],
            [
                'title' => 'Configuration',
                'color' => 'gray',
                'links' => [
                    [
                        'label' => 'Paramètres plateforme',
                        'description' => 'Configuration générale',
                        'url' => '/admin/platform-settings',
                        'icon' => 'heroicon-o-cog-8-tooth',
                    ],
                    [
                        'label' => 'Lead Capture',
                        'description' => 'Popups & formulaires',
                        'url' => '/admin/lead-capture-settings',
                        'icon' => 'heroicon-o-cursor-arrow-ripple',
                    ],
                ],
            ],
        ];
    }
}

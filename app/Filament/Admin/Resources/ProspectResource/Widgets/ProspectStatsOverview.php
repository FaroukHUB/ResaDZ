<?php

namespace App\Filament\Admin\Resources\ProspectResource\Widgets;

use App\Models\Prospect;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProspectStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total prospects', Prospect::count())
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('Contactés aujourd\'hui', Prospect::where('date_dernier_contact', now()->toDateString())->count())
                ->icon('heroicon-o-phone')
                ->color('info'),
            Stat::make('Intéressés', Prospect::where('statut', 'interesse')->count())
                ->icon('heroicon-o-star')
                ->color('warning'),
            Stat::make('Convertis', Prospect::where('statut', 'inscrit')->count())
                ->icon('heroicon-o-check-badge')
                ->color('success'),
            Stat::make('Relances du jour', Prospect::parRelance()->count())
                ->icon('heroicon-o-clock')
                ->color('danger'),
        ];
    }
}

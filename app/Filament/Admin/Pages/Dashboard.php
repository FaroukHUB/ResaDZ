<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\QuickLinksWidget;
use App\Filament\Admin\Widgets\StatsOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Tableau de bord';

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            QuickLinksWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }
}

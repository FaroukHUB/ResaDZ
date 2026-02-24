<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class QuickLinksWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static string $view = 'filament.admin.widgets.quick-links-widget';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';
}

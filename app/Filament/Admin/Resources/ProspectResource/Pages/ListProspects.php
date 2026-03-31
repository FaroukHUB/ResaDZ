<?php

namespace App\Filament\Admin\Resources\ProspectResource\Pages;

use App\Filament\Admin\Resources\ProspectResource;
use App\Filament\Admin\Resources\ProspectResource\Widgets\ProspectStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProspects extends ListRecords
{
    protected static string $resource = ProspectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un prospect'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProspectStatsOverview::class,
        ];
    }
}

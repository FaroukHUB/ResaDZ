<?php

namespace App\Filament\Loueur\Resources\TransferRouteResource\Pages;

use App\Filament\Loueur\Resources\TransferRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransferRoutes extends ListRecords
{
    protected static string $resource = TransferRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un trajet'),
        ];
    }
}

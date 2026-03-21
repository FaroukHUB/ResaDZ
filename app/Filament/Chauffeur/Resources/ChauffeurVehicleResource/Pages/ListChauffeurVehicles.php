<?php

namespace App\Filament\Chauffeur\Resources\ChauffeurVehicleResource\Pages;

use App\Filament\Chauffeur\Resources\ChauffeurVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChauffeurVehicles extends ListRecords
{
    protected static string $resource = ChauffeurVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un vehicule'),
        ];
    }
}

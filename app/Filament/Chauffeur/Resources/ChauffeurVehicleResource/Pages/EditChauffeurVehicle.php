<?php

namespace App\Filament\Chauffeur\Resources\ChauffeurVehicleResource\Pages;

use App\Filament\Chauffeur\Resources\ChauffeurVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChauffeurVehicle extends EditRecord
{
    protected static string $resource = ChauffeurVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

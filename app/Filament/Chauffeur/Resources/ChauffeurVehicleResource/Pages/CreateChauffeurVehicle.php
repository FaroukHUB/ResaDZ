<?php

namespace App\Filament\Chauffeur\Resources\ChauffeurVehicleResource\Pages;

use App\Filament\Chauffeur\Resources\ChauffeurVehicleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateChauffeurVehicle extends CreateRecord
{
    protected static string $resource = ChauffeurVehicleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['loueur_id'] = Auth::user()->loueur->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

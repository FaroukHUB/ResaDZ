<?php

namespace App\Filament\Loueur\Resources\VehicleResource\Pages;

use App\Filament\Loueur\Resources\VehicleResource;
use App\Services\VehicleImageProcessingService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Associer automatiquement le véhicule au loueur connecté
        $loueur = Auth::user()->loueur;

        if ($loueur) {
            $data['loueur_id'] = $loueur->id;
        }

        // Traiter la photo principale via l'API externe
        if (!empty($data['image'])) {
            $service = new VehicleImageProcessingService();
            $data['image'] = $service->processMainImage($data['image']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

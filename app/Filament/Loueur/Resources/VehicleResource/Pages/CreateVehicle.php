<?php

namespace App\Filament\Loueur\Resources\VehicleResource\Pages;

use App\Filament\Loueur\Resources\VehicleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $loueur = Auth::user()->loueur;

        if ($loueur) {
            $data['loueur_id'] = $loueur->id;
        }

        // Valeurs par défaut pour les champs NOT NULL
        $data['seats'] = $data['seats'] ?? 5;
        $data['doors'] = $data['doors'] ?? 5;
        $data['mileage'] = $data['mileage'] ?? 0;
        $data['luggage_capacity'] = $data['luggage_capacity'] ?? 0;
        $data['deposit_amount'] = $data['deposit_amount'] ?? 0;
        $data['deposit_amount_eur'] = $data['deposit_amount_eur'] ?? 0;
        $data['price_per_day_eur'] = $data['price_per_day_eur'] ?? 0;
        $data['max_rental_days'] = $data['max_rental_days'] ?? 30;
        $data['mileage_limit_per_day'] = $data['mileage_limit_per_day'] ?? 0;
        $data['image'] = $data['image'] ?? '';
        $data['gallery'] = $data['gallery'] ?? [];

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

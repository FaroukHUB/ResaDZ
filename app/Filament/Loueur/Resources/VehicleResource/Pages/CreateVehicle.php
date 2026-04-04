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
        $defaults = [
            'seats' => 5,
            'doors' => 5,
            'mileage' => 0,
            'luggage_capacity' => 0,
            'deposit_amount' => 0,
            'deposit_amount_eur' => 0,
            'price_per_day_eur' => 0,
            'max_rental_days' => 30,
            'mileage_limit_per_day' => 0,
            'image' => '',
            'gallery' => [],
            'transmission' => 'manual',
            'fuel_type' => 'essence',
            'year' => date('Y'),
            'color' => '',
            'status' => 'available',
            'is_active' => true,
            'min_rental_days' => 1,
            'has_air_conditioning' => true,
            'degressive_pricing' => [],
            'vehicle_badges' => [],
            'vehicle_options' => [],
            'fuel_return_fee' => 0,
            'wash_return_fee' => 0,
        ];

        foreach ($defaults as $key => $value) {
            $data[$key] = $data[$key] ?? $value;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

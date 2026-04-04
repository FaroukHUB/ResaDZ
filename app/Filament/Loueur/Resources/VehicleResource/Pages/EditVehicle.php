<?php

namespace App\Filament\Loueur\Resources\VehicleResource\Pages;

use App\Filament\Loueur\Resources\VehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicle extends EditRecord
{
    protected static string $resource = VehicleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
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
            'min_rental_days' => 1,
            'has_air_conditioning' => true,
            'degressive_pricing' => [],
            'vehicle_badges' => [],
            'vehicle_options' => [],
            'fuel_return_fee' => 0,
            'wash_return_fee' => 0,
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($data[$key]) || $data[$key] === null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Supprimer'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

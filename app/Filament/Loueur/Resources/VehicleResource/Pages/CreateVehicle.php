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

        if (!$loueur) {
            return $data;
        }

        $data['loueur_id'] = $loueur->id;

        // Frais de retour : pré-remplir depuis les defaults du loueur si non saisis
        if (($data['fuel_return_fee'] ?? null) === null || $data['fuel_return_fee'] === '') {
            $data['fuel_return_fee'] = $loueur->getSetting('fuel_return_fee', 0);
        }
        if (($data['wash_return_fee'] ?? null) === null || $data['wash_return_fee'] === '') {
            $data['wash_return_fee'] = $loueur->getSetting('wash_return_fee', 0);
        }

        // Badges : pré-remplir depuis les defaults du loueur si non cochés
        foreach (['badge_insurance', 'badge_delivery', 'badge_degressive', 'badge_airport', 'badge_km_unlimited'] as $badge) {
            if (!isset($data[$badge])) {
                $data[$badge] = $loueur->getSetting($badge, false);
            }
        }
        if (empty($data['custom_badges'])) {
            $data['custom_badges'] = $loueur->getSetting('custom_badges', []) ?: [];
        }

        // Options de location : pré-remplir depuis les defaults du loueur si vide
        if (empty($data['available_options'])) {
            $data['available_options'] = $loueur->getSetting('rental_options', []) ?: [];
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Loueur\Resources\DeliveryZoneResource\Pages;

use App\Filament\Loueur\Resources\DeliveryZoneResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDeliveryZone extends CreateRecord
{
    protected static string $resource = DeliveryZoneResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $loueur = Auth::user()->loueur;

        if ($loueur) {
            $data['loueur_id'] = $loueur->id;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

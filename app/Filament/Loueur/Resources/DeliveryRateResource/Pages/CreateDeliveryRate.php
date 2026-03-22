<?php

namespace App\Filament\Loueur\Resources\DeliveryRateResource\Pages;

use App\Filament\Loueur\Resources\DeliveryRateResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDeliveryRate extends CreateRecord
{
    protected static string $resource = DeliveryRateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['loueur_id'] = Auth::user()?->loueur?->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

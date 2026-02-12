<?php

namespace App\Filament\Loueur\Resources\DeliveryZoneResource\Pages;

use App\Filament\Loueur\Resources\DeliveryZoneResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryZone extends EditRecord
{
    protected static string $resource = DeliveryZoneResource::class;

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

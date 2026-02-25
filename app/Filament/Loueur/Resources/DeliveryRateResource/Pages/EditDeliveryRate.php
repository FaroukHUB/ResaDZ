<?php

namespace App\Filament\Loueur\Resources\DeliveryRateResource\Pages;

use App\Filament\Loueur\Resources\DeliveryRateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryRate extends EditRecord
{
    protected static string $resource = DeliveryRateResource::class;

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

<?php

namespace App\Filament\Chauffeur\Resources\DeliveryBookingResource\Pages;

use App\Filament\Chauffeur\Resources\DeliveryBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryBooking extends EditRecord
{
    protected static string $resource = DeliveryBookingResource::class;

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

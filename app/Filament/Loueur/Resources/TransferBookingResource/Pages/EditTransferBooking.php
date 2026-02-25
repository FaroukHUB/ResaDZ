<?php

namespace App\Filament\Loueur\Resources\TransferBookingResource\Pages;

use App\Filament\Loueur\Resources\TransferBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransferBooking extends EditRecord
{
    protected static string $resource = TransferBookingResource::class;

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

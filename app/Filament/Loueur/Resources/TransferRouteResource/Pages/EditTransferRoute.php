<?php

namespace App\Filament\Loueur\Resources\TransferRouteResource\Pages;

use App\Filament\Loueur\Resources\TransferRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransferRoute extends EditRecord
{
    protected static string $resource = TransferRouteResource::class;

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

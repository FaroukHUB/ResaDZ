<?php

namespace App\Filament\Loueur\Resources\TransferRouteResource\Pages;

use App\Filament\Loueur\Resources\TransferRouteResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTransferRoute extends CreateRecord
{
    protected static string $resource = TransferRouteResource::class;

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

<?php

namespace App\Filament\Loueur\Resources\DeliveryRateResource\Pages;

use App\Filament\Loueur\Resources\DeliveryRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryRates extends ListRecords
{
    protected static string $resource = DeliveryRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un tarif'),
        ];
    }
}

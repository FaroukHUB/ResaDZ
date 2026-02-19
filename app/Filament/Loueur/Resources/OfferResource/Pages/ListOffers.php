<?php

namespace App\Filament\Loueur\Resources\OfferResource\Pages;

use App\Filament\Loueur\Resources\OfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOffers extends ListRecords
{
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle offre'),
        ];
    }
}

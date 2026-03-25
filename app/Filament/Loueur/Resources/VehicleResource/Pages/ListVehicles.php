<?php

namespace App\Filament\Loueur\Resources\VehicleResource\Pages;

use App\Filament\Loueur\Resources\VehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un véhicule'),
        ];
    }

    protected function getHeaderContent(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.loueur.partials.vehicle-photo-tip');
    }
}

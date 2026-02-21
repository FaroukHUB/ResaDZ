<?php

namespace App\Filament\Admin\Resources\VehicleBoostResource\Pages;

use App\Filament\Admin\Resources\VehicleBoostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleBoosts extends ListRecords
{
    protected static string $resource = VehicleBoostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

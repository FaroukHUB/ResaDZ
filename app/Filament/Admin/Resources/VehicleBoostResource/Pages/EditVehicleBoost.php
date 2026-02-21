<?php

namespace App\Filament\Admin\Resources\VehicleBoostResource\Pages;

use App\Filament\Admin\Resources\VehicleBoostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleBoost extends EditRecord
{
    protected static string $resource = VehicleBoostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

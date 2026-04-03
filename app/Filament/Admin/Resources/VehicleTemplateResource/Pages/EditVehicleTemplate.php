<?php

namespace App\Filament\Admin\Resources\VehicleTemplateResource\Pages;

use App\Filament\Admin\Resources\VehicleTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleTemplate extends EditRecord
{
    protected static string $resource = VehicleTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\VehicleTemplateResource\Pages;

use App\Filament\Admin\Resources\VehicleTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleTemplates extends ListRecords
{
    protected static string $resource = VehicleTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

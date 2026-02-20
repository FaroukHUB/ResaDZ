<?php

namespace App\Filament\Admin\Resources\PopupResource\Pages;

use App\Filament\Admin\Resources\PopupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPopups extends ListRecords
{
    protected static string $resource = PopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\OptionResource\Pages;

use App\Filament\Admin\Resources\OptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOptions extends ManageRecords
{
    protected static string $resource = OptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

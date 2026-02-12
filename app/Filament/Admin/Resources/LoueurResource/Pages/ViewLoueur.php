<?php

namespace App\Filament\Admin\Resources\LoueurResource\Pages;

use App\Filament\Admin\Resources\LoueurResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLoueur extends ViewRecord
{
    protected static string $resource = LoueurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

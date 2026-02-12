<?php

namespace App\Filament\Admin\Resources\LoueurResource\Pages;

use App\Filament\Admin\Resources\LoueurResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoueurs extends ListRecords
{
    protected static string $resource = LoueurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouveau loueur'),
        ];
    }
}

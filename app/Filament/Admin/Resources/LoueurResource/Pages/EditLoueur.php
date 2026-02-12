<?php

namespace App\Filament\Admin\Resources\LoueurResource\Pages;

use App\Filament\Admin\Resources\LoueurResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoueur extends EditRecord
{
    protected static string $resource = LoueurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->label('Supprimer'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

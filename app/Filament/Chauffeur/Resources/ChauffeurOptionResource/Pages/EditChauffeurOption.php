<?php

namespace App\Filament\Chauffeur\Resources\ChauffeurOptionResource\Pages;

use App\Filament\Chauffeur\Resources\ChauffeurOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChauffeurOption extends EditRecord
{
    protected static string $resource = ChauffeurOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

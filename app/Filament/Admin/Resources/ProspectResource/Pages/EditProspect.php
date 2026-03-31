<?php

namespace App\Filament\Admin\Resources\ProspectResource\Pages;

use App\Filament\Admin\Resources\ProspectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProspect extends EditRecord
{
    protected static string $resource = ProspectResource::class;

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

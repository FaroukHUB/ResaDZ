<?php

namespace App\Filament\Admin\Resources\ProspectResource\Pages;

use App\Filament\Admin\Resources\ProspectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProspect extends CreateRecord
{
    protected static string $resource = ProspectResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Admin\Resources\PopupResource\Pages;

use App\Filament\Admin\Resources\PopupResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePopup extends CreateRecord
{
    protected static string $resource = PopupResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

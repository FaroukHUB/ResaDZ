<?php

namespace App\Filament\Admin\Resources\BoostPackageResource\Pages;

use App\Filament\Admin\Resources\BoostPackageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBoostPackage extends CreateRecord
{
    protected static string $resource = BoostPackageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

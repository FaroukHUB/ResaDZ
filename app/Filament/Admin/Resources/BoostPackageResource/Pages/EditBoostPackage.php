<?php

namespace App\Filament\Admin\Resources\BoostPackageResource\Pages;

use App\Filament\Admin\Resources\BoostPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBoostPackage extends EditRecord
{
    protected static string $resource = BoostPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

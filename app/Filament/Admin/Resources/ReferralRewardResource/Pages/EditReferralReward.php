<?php

namespace App\Filament\Admin\Resources\ReferralRewardResource\Pages;

use App\Filament\Admin\Resources\ReferralRewardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReferralReward extends EditRecord
{
    protected static string $resource = ReferralRewardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

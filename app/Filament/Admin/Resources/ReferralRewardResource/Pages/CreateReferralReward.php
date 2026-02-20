<?php

namespace App\Filament\Admin\Resources\ReferralRewardResource\Pages;

use App\Filament\Admin\Resources\ReferralRewardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReferralReward extends CreateRecord
{
    protected static string $resource = ReferralRewardResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Admin\Resources\ReferralRewardResource\Pages;

use App\Filament\Admin\Resources\ReferralRewardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReferralRewards extends ListRecords
{
    protected static string $resource = ReferralRewardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

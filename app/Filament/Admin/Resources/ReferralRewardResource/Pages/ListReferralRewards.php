<?php

namespace App\Filament\Admin\Resources\ReferralRewardResource\Pages;

use App\Filament\Admin\Resources\ReferralRewardResource;
use App\Models\Referral;
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

    protected function getHeaderWidgets(): array
    {
        return [
            \Filament\Widgets\StatsOverviewWidget::make([
                \Filament\Widgets\StatsOverviewWidget\Stat::make('Total parrainages', Referral::count()),
                \Filament\Widgets\StatsOverviewWidget\Stat::make('Récompensés', Referral::rewarded()->count()),
                \Filament\Widgets\StatsOverviewWidget\Stat::make('En attente', Referral::pending()->count()),
            ]),
        ];
    }
}

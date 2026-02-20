<?php

namespace App\Filament\Admin\Resources\SocialChannelResource\Pages;

use App\Filament\Admin\Resources\SocialChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSocialChannels extends ListRecords
{
    protected static string $resource = SocialChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

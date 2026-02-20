<?php

namespace App\Filament\Admin\Resources\SocialChannelResource\Pages;

use App\Filament\Admin\Resources\SocialChannelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSocialChannel extends CreateRecord
{
    protected static string $resource = SocialChannelResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

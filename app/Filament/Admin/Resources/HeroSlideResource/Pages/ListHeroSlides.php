<?php

namespace App\Filament\Admin\Resources\HeroSlideResource\Pages;

use App\Filament\Admin\Resources\HeroSlideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHeroSlides extends ListRecords
{
    protected static string $resource = HeroSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

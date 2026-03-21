<?php

namespace App\Filament\Chauffeur\Resources\ChauffeurOptionResource\Pages;

use App\Filament\Chauffeur\Resources\ChauffeurOptionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateChauffeurOption extends CreateRecord
{
    protected static string $resource = ChauffeurOptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['loueur_id'] = Auth::user()->loueur->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

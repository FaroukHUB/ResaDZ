<?php

namespace App\Filament\Admin\Resources\LoueurResource\Pages;

use App\Filament\Admin\Resources\LoueurResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateLoueur extends CreateRecord
{
    protected static string $resource = LoueurResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['company_name']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Mettre à jour le rôle de l'utilisateur en loueur
        $user = $this->record->user;
        if ($user && $user->role !== 'super_admin') {
            $user->update(['role' => 'loueur']);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

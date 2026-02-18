<?php

namespace App\Filament\Admin\Resources\LoueurResource\Pages;

use App\Filament\Admin\Resources\LoueurResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditLoueur extends EditRecord
{
    protected static string $resource = LoueurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->label('Supprimer'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Mettre à jour l'email de l'utilisateur si modifié
        if (!empty($data['user_email']) && $this->record->user) {
            $this->record->user->update([
                'email' => $data['user_email'],
            ]);
        }

        // Mettre à jour le mot de passe si fourni
        if (!empty($data['user_password']) && $this->record->user) {
            $this->record->user->update([
                'password' => Hash::make($data['user_password']),
            ]);
        }

        // Nettoyer les champs qui ne sont pas dans la table loueurs
        unset($data['user_email'], $data['user_password']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}

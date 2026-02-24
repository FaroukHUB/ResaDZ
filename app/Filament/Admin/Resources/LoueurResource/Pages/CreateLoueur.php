<?php

namespace App\Filament\Admin\Resources\LoueurResource\Pages;

use App\Filament\Admin\Resources\LoueurResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateLoueur extends CreateRecord
{
    protected static string $resource = LoueurResource::class;

    protected array $wilayasToSync = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Créer l'utilisateur avec l'email et mot de passe fournis
        $user = User::create([
            'name' => $data['company_name'],
            'email' => $data['user_email'],
            'password' => Hash::make($data['user_password']),
            'role' => 'loueur',
            'email_verified_at' => now(),
        ]);

        // Lier l'utilisateur au loueur
        $data['user_id'] = $user->id;

        // Générer le slug si vide
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['company_name']);
        }

        // Définir la période d'essai (1 mois par défaut si non définie)
        if (empty($data['trial_ends_at'])) {
            $trialDays = config('resadz.trial_days', 30);
            $data['trial_ends_at'] = now()->addDays($trialDays);
        }

        // Sauvegarder les wilayas pour synchronisation après création
        $this->wilayasToSync = $data['wilayas'] ?? [];

        // Nettoyer les champs qui ne sont pas dans la table loueurs
        unset($data['user_email'], $data['user_password'], $data['wilayas']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Synchroniser les wilayas
        if (!empty($this->wilayasToSync)) {
            $this->record->syncWilayas($this->wilayasToSync);
        }

        // Notification de succès avec les infos de connexion
        Notification::make()
            ->title('Loueur créé avec succès')
            ->body('Email: ' . $this->record->user->email . ' | Dashboard: ' . url('/loueur'))
            ->success()
            ->persistent()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}

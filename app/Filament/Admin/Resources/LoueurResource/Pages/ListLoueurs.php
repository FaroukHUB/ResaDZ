<?php

namespace App\Filament\Admin\Resources\LoueurResource\Pages;

use App\Filament\Admin\Resources\LoueurResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListLoueurs extends ListRecords
{
    protected static string $resource = LoueurResource::class;

    protected function getHeaderActions(): array
    {
        $orphanCount = User::where('role', 'loueur')
            ->whereDoesntHave('loueur')
            ->count();

        return [
            Actions\CreateAction::make()
                ->label('Nouveau loueur'),
            Actions\Action::make('purgeOrphans')
                ->label("Purger comptes orphelins ({$orphanCount})")
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Purger les comptes orphelins')
                ->modalDescription('Cela supprimera les comptes utilisateurs (rôle loueur) qui n\'ont plus de profil loueur associé. Ces emails seront libérés pour de nouvelles inscriptions.')
                ->modalSubmitActionLabel('Supprimer')
                ->visible($orphanCount > 0)
                ->action(function () {
                    $deleted = User::where('role', 'loueur')
                        ->whereDoesntHave('loueur')
                        ->delete();

                    Notification::make()
                        ->title("{$deleted} compte(s) orphelin(s) supprimé(s)")
                        ->success()
                        ->send();
                }),
        ];
    }
}

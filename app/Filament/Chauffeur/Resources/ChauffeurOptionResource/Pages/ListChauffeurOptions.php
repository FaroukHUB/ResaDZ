<?php

namespace App\Filament\Chauffeur\Resources\ChauffeurOptionResource\Pages;

use App\Filament\Chauffeur\Resources\ChauffeurOptionResource;
use App\Models\ChauffeurOption;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListChauffeurOptions extends ListRecords
{
    protected static string $resource = ChauffeurOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create_defaults')
                ->label('Options par defaut')
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->visible(fn () => ChauffeurOption::where('loueur_id', Auth::user()->loueur->id)->count() === 0)
                ->requiresConfirmation()
                ->modalHeading('Creer les options par defaut')
                ->modalDescription('Cela creera une liste d\'options courantes (WiFi, siege auto, etc.) que vous pourrez personnaliser ensuite.')
                ->action(function () {
                    ChauffeurOption::createDefaultsForChauffeur(Auth::user()->loueur->id);
                    Notification::make()
                        ->title('Options creees')
                        ->body('Les options par defaut ont ete ajoutees.')
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make()
                ->label('Ajouter une option'),
        ];
    }
}

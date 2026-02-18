<?php

namespace App\Filament\Loueur\Resources\BookingResource\Pages;

use App\Filament\Loueur\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadContract')
                ->label('Télécharger le contrat')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn () => route('contract.download', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('previewContract')
                ->label('Aperçu contrat')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => route('contract.preview', $this->record))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
        ];
    }
}

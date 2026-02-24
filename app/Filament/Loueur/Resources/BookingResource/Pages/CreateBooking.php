<?php

namespace App\Filament\Loueur\Resources\BookingResource\Pages;

use App\Filament\Loueur\Resources\BookingResource;
use App\Models\Vehicle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $loueur = Auth::user()->loueur;

        if ($loueur) {
            $data['loueur_id'] = $loueur->id;
        }

        // Calculer le nombre de jours si non fourni
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $start = \Carbon\Carbon::parse($data['start_date']);
            $end = \Carbon\Carbon::parse($data['end_date']);
            $data['total_days'] = $start->diffInDays($end) + 1;
        }

        // Vérifier la disponibilité du véhicule
        if (isset($data['vehicle_id']) && isset($data['start_date']) && isset($data['end_date'])) {
            $vehicle = Vehicle::find($data['vehicle_id']);
            if ($vehicle && !$vehicle->isAvailableForDates($data['start_date'], $data['end_date'])) {
                Notification::make()
                    ->title('Véhicule indisponible')
                    ->body('Ce véhicule est bloqué ou en maintenance sur les dates sélectionnées.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Loueur\Resources\BookingResource\Pages;

use App\Filament\Loueur\Resources\BookingResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

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

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

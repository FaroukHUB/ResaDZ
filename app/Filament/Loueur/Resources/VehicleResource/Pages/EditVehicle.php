<?php

namespace App\Filament\Loueur\Resources\VehicleResource\Pages;

use App\Filament\Loueur\Resources\VehicleResource;
use App\Services\VehicleImageProcessingService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicle extends EditRecord
{
    protected static string $resource = VehicleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Traiter la photo principale via l'API externe uniquement si elle a changé
        if (!empty($data['image']) && $data['image'] !== $this->record->image) {
            $service = new VehicleImageProcessingService();
            $data['image'] = $service->processMainImage($data['image']);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Supprimer'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

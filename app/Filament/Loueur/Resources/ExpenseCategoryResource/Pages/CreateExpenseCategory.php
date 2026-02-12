<?php

namespace App\Filament\Loueur\Resources\ExpenseCategoryResource\Pages;

use App\Filament\Loueur\Resources\ExpenseCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateExpenseCategory extends CreateRecord
{
    protected static string $resource = ExpenseCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['loueur_id'] = Auth::user()->loueur?->id;
        $data['is_default'] = false;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

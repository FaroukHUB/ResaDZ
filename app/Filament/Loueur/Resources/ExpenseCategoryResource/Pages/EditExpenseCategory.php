<?php

namespace App\Filament\Loueur\Resources\ExpenseCategoryResource\Pages;

use App\Filament\Loueur\Resources\ExpenseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpenseCategory extends EditRecord
{
    protected static string $resource = ExpenseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Supprimer')
                ->visible(fn () => !$this->record->is_default),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

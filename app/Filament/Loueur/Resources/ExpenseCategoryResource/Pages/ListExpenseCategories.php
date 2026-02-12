<?php

namespace App\Filament\Loueur\Resources\ExpenseCategoryResource\Pages;

use App\Filament\Loueur\Resources\ExpenseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpenseCategories extends ListRecords
{
    protected static string $resource = ExpenseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle catégorie'),
        ];
    }
}

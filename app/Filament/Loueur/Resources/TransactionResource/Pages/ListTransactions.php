<?php

namespace App\Filament\Loueur\Resources\TransactionResource\Pages;

use App\Filament\Loueur\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle transaction'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Toutes')
                ->icon('heroicon-o-queue-list'),
            'income' => Tab::make('Entrées')
                ->icon('heroicon-o-arrow-down-tray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'income')),
            'expense' => Tab::make('Sorties')
                ->icon('heroicon-o-arrow-up-tray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'expense')),
        ];
    }
}

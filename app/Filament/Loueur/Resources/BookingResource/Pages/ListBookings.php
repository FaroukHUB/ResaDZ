<?php

namespace App\Filament\Loueur\Resources\BookingResource\Pages;

use App\Filament\Loueur\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle réservation'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Toutes')
                ->icon('heroicon-o-queue-list'),
            'pending' => Tab::make('En attente')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'confirmed' => Tab::make('Confirmées')
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed')),
            'active' => Tab::make('En cours')
                ->icon('heroicon-o-play')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active')),
            'completed' => Tab::make('Terminées')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\ReviewResource\Pages;

use App\Filament\Admin\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous')
                ->badge(fn () => $this->getModel()::count()),

            'pending' => Tab::make('En attente')
                ->badge(fn () => $this->getModel()::where('is_approved', false)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_approved', false)),

            'flagged' => Tab::make('Signalés')
                ->badge(fn () => $this->getModel()::where('is_flagged', true)->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_flagged', true)),

            'positive' => Tab::make('Positifs (4-5⭐)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rating_overall', '>=', 4)),

            'negative' => Tab::make('Négatifs (1-2⭐)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rating_overall', '<=', 2)),
        ];
    }
}

<?php

namespace App\Filament\Loueur\Widgets;

use App\Models\Vehicle;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TopVehicles extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Performance des véhicules';

    public function table(Table $table): Table
    {
        $loueur = Auth::user()->loueur;

        return $table
            ->query(
                Vehicle::query()
                    ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id))
                    ->where('is_active', true)
                    ->withCount(['bookings as total_bookings'])
                    ->withCount(['bookings as active_bookings' => function (Builder $query) {
                        $query->where('status', 'active');
                    }])
                    ->withSum(['bookings as total_revenue' => function (Builder $query) {
                        $query->whereIn('status', ['active', 'completed']);
                    }], 'total_price')
                    ->orderByDesc('total_bookings')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Véhicule')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'reserved',
                        'danger' => 'maintenance',
                        'gray' => 'unavailable',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'available' => 'Disponible',
                        'reserved' => 'Réservé',
                        'maintenance' => 'Maintenance',
                        'unavailable' => 'Indisponible',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('total_bookings')
                    ->label('Locations')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Revenus générés')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->sortable()
                    ->color('success'),
                Tables\Columns\TextColumn::make('price_per_day')
                    ->label('Prix/jour')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' DA'),
            ])
            ->paginated(false);
    }
}

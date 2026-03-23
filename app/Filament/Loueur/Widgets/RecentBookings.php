<?php

namespace App\Filament\Loueur\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RecentBookings extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Réservations récentes';

    public function table(Table $table): Table
    {
        $loueur = Auth::user()->loueur;

        return $table
            ->query(
                Booking::query()
                    ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id))
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Réf.')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-document-text'),
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->icon('heroicon-o-user'),
                Tables\Columns\TextColumn::make('vehicle.full_name')
                    ->label('Véhicule')
                    ->icon('heroicon-o-truck')
                    ->limit(25),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->icon('heroicon-o-calendar'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->formatStateUsing(fn ($record) => $record->getFormattedTotal())
                    ->weight('bold')
                    ->color('success'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'success' => 'active',
                        'gray' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'active' => 'En cours',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (Booking $record): string => route('filament.loueur.resources.bookings.view', $record)),
            ])
            ->striped()
            ->paginated(false);
    }
}

<?php

namespace App\Filament\Chauffeur\Widgets;

use App\Models\DeliveryBooking;
use App\Models\TransferBooking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RecentCourses extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Courses a venir';

    public function table(Table $table): Table
    {
        $loueur = Auth::user()->loueur;

        return $table
            ->query(
                TransferBooking::query()
                    ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id))
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->whereDate('transfer_date', '>=', today())
                    ->orderBy('transfer_date')
                    ->orderBy('transfer_time')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref.')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmee',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('departure')
                    ->label('Trajet')
                    ->formatStateUsing(fn ($record) => $record->departure . ' → ' . $record->destination)
                    ->limit(40),

                Tables\Columns\TextColumn::make('transfer_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->description(fn ($record) => $record->transfer_time),

                Tables\Columns\TextColumn::make('passengers')
                    ->label('Pax')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->color('success'),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client'),
            ])
            ->paginated(false)
            ->emptyStateHeading('Aucune course a venir')
            ->emptyStateDescription('Les prochaines courses apparaitront ici.')
            ->emptyStateIcon('heroicon-o-calendar');
    }
}

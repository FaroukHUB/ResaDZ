<?php

namespace App\Filament\Loueur\Resources;

use App\Filament\Loueur\Resources\TransferBookingResource\Pages;
use App\Models\TransferBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TransferBookingResource extends Resource
{
    protected static ?string $model = TransferBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Chauffeur';

    protected static ?string $navigationLabel = 'Réservations Transfert';

    protected static ?string $modelLabel = 'Réservation';

    protected static ?string $pluralModelLabel = 'Réservations Transfert';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $loueur = Auth::user()->loueur;

        return parent::getEloquentQuery()
            ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id));
    }

    public static function getNavigationBadge(): ?string
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) return null;

        $count = TransferBooking::where('loueur_id', $loueur->id)
            ->where('status', 'pending')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails du trajet')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->label('Référence')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'confirmed' => 'Confirmée',
                                'completed' => 'Terminée',
                                'cancelled' => 'Annulée',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('departure')
                            ->label('Départ')
                            ->required(),

                        Forms\Components\TextInput::make('destination')
                            ->label('Destination')
                            ->required(),

                        Forms\Components\DatePicker::make('transfer_date')
                            ->label('Date')
                            ->required(),

                        Forms\Components\TextInput::make('transfer_time')
                            ->label('Heure')
                            ->required(),

                        Forms\Components\TextInput::make('passengers')
                            ->label('Passagers')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('luggage_count')
                            ->label('Bagages')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('price')
                            ->label('Prix total (DA)')
                            ->numeric()
                            ->suffix('DA'),

                        Forms\Components\TextInput::make('commission_amount')
                            ->label('Commission ResaDZ (10%)')
                            ->disabled()
                            ->dehydrated(false)
                            ->suffix('DA')
                            ->helperText('Commission prélevée sur cette course'),

                        Forms\Components\Select::make('vehicle_type')
                            ->label('Type de véhicule')
                            ->options([
                                'berline' => 'Berline',
                                'suv' => 'SUV',
                                'van' => 'Van',
                                'minibus' => 'Minibus',
                            ]),
                    ]),

                Forms\Components\Section::make('Client')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label('Nom')
                            ->required(),

                        Forms\Components\TextInput::make('client_phone')
                            ->label('Téléphone')
                            ->required(),

                        Forms\Components\TextInput::make('client_email')
                            ->label('Email'),

                        Forms\Components\Textarea::make('client_notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Annulation')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (Forms\Get $get) => $get('status') === 'cancelled')
                    ->schema([
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label('Raison de l\'annulation'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Réf.')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-document-text'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'confirmed' => 'heroicon-o-check-circle',
                        'completed' => 'heroicon-o-flag',
                        'cancelled' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->description(fn ($record) => $record->client_phone),

                Tables\Columns\TextColumn::make('departure')
                    ->label('Trajet')
                    ->searchable()
                    ->icon('heroicon-o-map-pin')
                    ->description(fn ($record) => '→ ' . $record->destination)
                    ->limit(30),

                Tables\Columns\TextColumn::make('transfer_date')
                    ->label('Date & heure')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->description(fn ($record) => $record->transfer_time),

                Tables\Columns\TextColumn::make('passengers')
                    ->label('Pass.')
                    ->alignCenter()
                    ->icon('heroicon-o-user-group')
                    ->description(fn ($record) => ($record->luggage_count ?? 0) . ' bag.'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->description(fn ($record) => '-' . number_format($record->commission_amount ?? 0, 0, ',', ' ') . ' comm.'),

                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net')
                    ->formatStateUsing(fn ($record) => number_format($record->net_amount ?? 0, 0, ',', ' ') . ' DA')
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('commission_paid')
                    ->label('Comm.')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Accepter')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->button()
                    ->visible(fn (TransferBooking $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmer cette course ?')
                    ->modalDescription('Le client sera notifié que vous avez accepté sa demande.')
                    ->action(fn (TransferBooking $record) => $record->update([
                        'status' => 'confirmed',
                        'confirmed_at' => now(),
                    ])),

                Tables\Actions\Action::make('complete')
                    ->label('Terminer')
                    ->icon('heroicon-o-flag')
                    ->color('primary')
                    ->button()
                    ->visible(fn (TransferBooking $record) => $record->status === 'confirmed')
                    ->requiresConfirmation()
                    ->modalHeading('Course terminée ?')
                    ->modalDescription('Confirmez que la course est terminée.')
                    ->action(fn (TransferBooking $record) => $record->update([
                        'status' => 'completed',
                    ])),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferBookings::route('/'),
            'edit' => Pages\EditTransferBooking::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) return false;

        // Les taxis utilisent /chauffeur, ici on affiche seulement pour les loueurs qui proposent des transferts
        return !$loueur->isTaxi() && $loueur->offers_transfer;
    }
}

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

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Transferts';

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

                        Forms\Components\TextInput::make('price')
                            ->label('Prix (DA)')
                            ->numeric()
                            ->suffix('DA'),

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
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('departure')
                    ->label('Départ')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('destination')
                    ->label('Destination')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('transfer_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transfer_time')
                    ->label('Heure'),

                Tables\Columns\TextColumn::make('passengers')
                    ->label('Passagers')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('DZD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable(),

                Tables\Columns\TextColumn::make('client_phone')
                    ->label('Tél.')
                    ->searchable(),

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
                    ->label('Confirmer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (TransferBooking $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn (TransferBooking $record) => $record->update([
                        'status' => 'confirmed',
                        'confirmed_at' => now(),
                    ])),

                Tables\Actions\Action::make('complete')
                    ->label('Terminer')
                    ->icon('heroicon-o-flag')
                    ->color('info')
                    ->visible(fn (TransferBooking $record) => $record->status === 'confirmed')
                    ->requiresConfirmation()
                    ->action(fn (TransferBooking $record) => $record->update([
                        'status' => 'completed',
                    ])),

                Tables\Actions\EditAction::make(),
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

        return $loueur->isTaxi() || $loueur->offers_transfer;
    }
}

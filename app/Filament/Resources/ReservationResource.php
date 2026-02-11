<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Réservations';

    protected static ?string $navigationLabel = 'Réservations';

    protected static ?string $modelLabel = 'Réservation';

    protected static ?string $pluralModelLabel = 'Réservations';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations client')
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->label('Référence')
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit'),

                        Forms\Components\TextInput::make('client_name')
                            ->label('Nom du client')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('client_phone')
                            ->label('Téléphone')
                            ->required()
                            ->tel()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('client_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('client_address')
                            ->label('Adresse')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Véhicule & Dates')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Véhicule')
                            ->relationship('vehicle', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Date de début')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),

                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Heure de début')
                                    ->seconds(false),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Date de fin')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->afterOrEqual('start_date'),

                                Forms\Components\TimePicker::make('end_time')
                                    ->label('Heure de fin')
                                    ->seconds(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Lieu')
                    ->schema([
                        Forms\Components\TextInput::make('pickup_location')
                            ->label('Lieu de prise en charge')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('return_location')
                            ->label('Lieu de retour')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('flight_number')
                            ->label('Numéro de vol')
                            ->maxLength(50)
                            ->helperText('Si prise en charge à l\'aéroport'),
                    ])->columns(3),

                Forms\Components\Section::make('Tarification')
                    ->schema([
                        Forms\Components\TextInput::make('base_price')
                            ->label('Prix de base')
                            ->required()
                            ->numeric()
                            ->suffix('DA'),

                        Forms\Components\TextInput::make('options_price')
                            ->label('Options')
                            ->numeric()
                            ->default(0)
                            ->suffix('DA'),

                        Forms\Components\TextInput::make('discount')
                            ->label('Remise')
                            ->numeric()
                            ->default(0)
                            ->suffix('DA'),

                        Forms\Components\TextInput::make('total_price')
                            ->label('Total')
                            ->required()
                            ->numeric()
                            ->suffix('DA'),

                        Forms\Components\Select::make('currency')
                            ->label('Devise')
                            ->options([
                                'DZD' => 'Dinar (DZD)',
                                'EUR' => 'Euro (EUR)',
                            ])
                            ->default('DZD'),
                    ])->columns(5),

                Forms\Components\Section::make('État & Notes')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'confirmed' => 'Confirmée',
                                'in_progress' => 'En cours',
                                'completed' => 'Terminée',
                                'cancelled' => 'Annulée',
                            ])
                            ->default('pending')
                            ->required(),

                        Forms\Components\Select::make('source')
                            ->label('Source')
                            ->options([
                                'website' => 'Site web',
                                'phone' => 'Téléphone',
                                'admin' => 'Admin',
                                'whatsapp' => 'WhatsApp',
                            ])
                            ->default('admin'),

                        Forms\Components\Textarea::make('client_notes')
                            ->label('Notes du client')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Notes admin (privées)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
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
                    ->copyable(),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client_phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('vehicle.full_name')
                    ->label('Véhicule')
                    ->sortable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Du')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Au')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('DZD', locale: 'fr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'in_progress' => 'info',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'in_progress' => 'En cours',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                    }),

                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                        'in_progress' => 'En cours',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                    ]),

                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Véhicule')
                    ->relationship('vehicle', 'full_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('start_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Du'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Au'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->where('start_date', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->where('start_date', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmer')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Reservation $record) => $record->status === 'pending')
                    ->action(fn (Reservation $record) => $record->update(['status' => 'confirmed'])),

                Tables\Actions\Action::make('cancel')
                    ->label('Annuler')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Reservation $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->requiresConfirmation()
                    ->action(fn (Reservation $record) => $record->update(['status' => 'cancelled'])),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}

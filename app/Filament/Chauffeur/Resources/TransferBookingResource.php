<?php

namespace App\Filament\Chauffeur\Resources;

use App\Filament\Chauffeur\Resources\TransferBookingResource\Pages;
use App\Models\ChauffeurVehicle;
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

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Courses';

    protected static ?string $navigationLabel = 'Mes Transferts';

    protected static ?string $modelLabel = 'Transfert';

    protected static ?string $pluralModelLabel = 'Transferts';

    protected static ?int $navigationSort = 1;

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
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        $loueur = Auth::user()->loueur;

        return $form
            ->schema([
                Forms\Components\Section::make('Details du trajet')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->label('Reference')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'confirmed' => 'Confirmee',
                                'completed' => 'Terminee',
                                'cancelled' => 'Annulee',
                            ])
                            ->required(),

                        Forms\Components\Select::make('chauffeur_vehicle_id')
                            ->label('Vehicule')
                            ->options(fn () => ChauffeurVehicle::where('loueur_id', $loueur->id)
                                ->active()
                                ->get()
                                ->pluck('full_name', 'id'))
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('departure')
                            ->label('Depart')
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
                            ->helperText('Commission prelevee sur cette course'),

                        Forms\Components\Select::make('vehicle_type')
                            ->label('Type de vehicule')
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
                            ->label('Telephone')
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
                    ->label('Ref.')
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
                        'confirmed' => 'Confirmee',
                        'completed' => 'Terminee',
                        'cancelled' => 'Annulee',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('chauffeurVehicle.full_name')
                    ->label('Vehicule')
                    ->placeholder('Non assigne')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('departure')
                    ->label('Depart')
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
                    ->label('Pax')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->sortable(),

                Tables\Columns\TextColumn::make('commission_amount')
                    ->label('Commission')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->color('danger')
                    ->description('10%'),

                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net')
                    ->formatStateUsing(fn ($record) => number_format($record->net_amount ?? 0, 0, ',', ' ') . ' DA')
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('commission_paid')
                    ->label('Paye')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable(),

                Tables\Columns\TextColumn::make('client_phone')
                    ->label('Tel.')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creee le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmee',
                        'completed' => 'Terminee',
                        'cancelled' => 'Annulee',
                    ]),

                Tables\Filters\Filter::make('today')
                    ->label('Aujourd\'hui')
                    ->query(fn (Builder $query) => $query->whereDate('transfer_date', today())),

                Tables\Filters\Filter::make('this_week')
                    ->label('Cette semaine')
                    ->query(fn (Builder $query) => $query->whereBetween('transfer_date', [now()->startOfWeek(), now()->endOfWeek()])),
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
            ->defaultSort('transfer_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferBookings::route('/'),
            'edit' => Pages\EditTransferBooking::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Loueur\Resources;

use App\Filament\Loueur\Resources\TransferRouteResource\Pages;
use App\Models\TransferRoute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TransferRouteResource extends Resource
{
    protected static ?string $model = TransferRoute::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Transferts';

    protected static ?string $navigationLabel = 'Mes Trajets';

    protected static ?string $modelLabel = 'Trajet';

    protected static ?string $pluralModelLabel = 'Trajets';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $loueur = Auth::user()->loueur;

        return parent::getEloquentQuery()
            ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Trajet')
                    ->description('Définissez le trajet et le tarif')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('departure')
                            ->label('Lieu de départ')
                            ->placeholder('Ex: Aéroport Alger, Gare Oran...')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('destination')
                            ->label('Destination')
                            ->placeholder('Ex: Hôtel El Aurassi, Centre-ville...')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('price')
                            ->label('Prix (DA)')
                            ->numeric()
                            ->required()
                            ->suffix('DA')
                            ->minValue(0),

                        Forms\Components\Select::make('vehicle_type')
                            ->label('Type de véhicule')
                            ->options([
                                'berline' => 'Berline',
                                'suv' => 'SUV',
                                'van' => 'Van',
                                'minibus' => 'Minibus',
                            ])
                            ->default('berline')
                            ->required(),

                        Forms\Components\TextInput::make('max_passengers')
                            ->label('Passagers max')
                            ->numeric()
                            ->default(4)
                            ->minValue(1)
                            ->maxValue(20)
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),

                        Forms\Components\Toggle::make('round_trip')
                            ->label('Aller-retour disponible')
                            ->reactive()
                            ->default(false),

                        Forms\Components\TextInput::make('round_trip_price')
                            ->label('Prix aller-retour (DA)')
                            ->numeric()
                            ->suffix('DA')
                            ->minValue(0)
                            ->visible(fn (Forms\Get $get) => $get('round_trip')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('departure')
                    ->label('Départ')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('destination')
                    ->label('Destination')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('DZD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label('Véhicule')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'berline' => 'Berline',
                        'suv' => 'SUV',
                        'van' => 'Van',
                        'minibus' => 'Minibus',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('max_passengers')
                    ->label('Places')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('round_trip')
                    ->label('A/R')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_type')
                    ->label('Type de véhicule')
                    ->options([
                        'berline' => 'Berline',
                        'suv' => 'SUV',
                        'van' => 'Van',
                        'minibus' => 'Minibus',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('departure');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferRoutes::route('/'),
            'create' => Pages\CreateTransferRoute::route('/create'),
            'edit' => Pages\EditTransferRoute::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) return false;

        return $loueur->isTaxi() || $loueur->offers_transfer;
    }
}

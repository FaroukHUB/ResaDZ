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

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Chauffeur';

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
                Forms\Components\Placeholder::make('transfer_help')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString('
                        <div class="p-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 rounded-lg text-sm text-purple-800 dark:text-purple-200">
                            <strong>🚗 Service de transfert</strong> — Ces trajets sont proposés aux clients sur la page de transfert. Ils peuvent réserver un trajet avec chauffeur pour leurs déplacements (aéroport, gare, hôtel...).
                        </div>
                    ')),
                Forms\Components\Section::make('Trajet')
                    ->description('Définissez le trajet, le tarif et les conditions')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('departure')
                            ->label('Lieu de départ')
                            ->placeholder('Ex: Aéroport Alger, Gare Oran...')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Point de prise en charge du client'),

                        Forms\Components\TextInput::make('destination')
                            ->label('Destination')
                            ->placeholder('Ex: Hôtel El Aurassi, Centre-ville...')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Où déposez-vous le client'),

                        Forms\Components\TextInput::make('price')
                            ->label('Prix (DA)')
                            ->numeric()
                            ->required()
                            ->suffix('DA')
                            ->minValue(0)
                            ->helperText('Prix affiché au client pour ce trajet'),

                        Forms\Components\Select::make('vehicle_type')
                            ->label('Type de véhicule')
                            ->options([
                                'berline' => 'Berline',
                                'suv' => 'SUV',
                                'van' => 'Van',
                                'minibus' => 'Minibus',
                            ])
                            ->default('berline')
                            ->required()
                            ->helperText('Type de véhicule utilisé pour ce trajet'),

                        Forms\Components\TextInput::make('max_passengers')
                            ->label('Passagers max')
                            ->numeric()
                            ->default(4)
                            ->minValue(1)
                            ->maxValue(20)
                            ->required()
                            ->helperText('Nombre maximum de passagers acceptés'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true)
                            ->helperText('Désactivez pour masquer ce trajet'),

                        Forms\Components\Toggle::make('round_trip')
                            ->label('Aller-retour disponible')
                            ->reactive()
                            ->default(false)
                            ->helperText('Proposer aussi l\'aller-retour'),

                        Forms\Components\TextInput::make('round_trip_price')
                            ->label('Prix aller-retour (DA)')
                            ->numeric()
                            ->suffix('DA')
                            ->minValue(0)
                            ->visible(fn (Forms\Get $get) => $get('round_trip'))
                            ->helperText('Prix total A/R (généralement -10 à -20% vs 2x aller simple)'),
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
                    ->sortable()
                    ->icon('heroicon-o-map-pin')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('destination')
                    ->label('Destination')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-flag')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label('Véhicule')
                    ->badge()
                    ->color('primary')
                    ->icon(fn (string $state): string => match ($state) {
                        'berline' => 'heroicon-o-truck',
                        'suv' => 'heroicon-o-truck',
                        'van' => 'heroicon-o-truck',
                        'minibus' => 'heroicon-o-truck',
                        default => 'heroicon-o-truck',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'berline' => 'Berline',
                        'suv' => 'SUV',
                        'van' => 'Van',
                        'minibus' => 'Minibus',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('max_passengers')
                    ->label('Places')
                    ->alignCenter()
                    ->icon('heroicon-o-user-group'),

                Tables\Columns\TextColumn::make('round_trip')
                    ->label('A/R')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'A/R dispo' : 'Simple')
                    ->color(fn ($state) => $state ? 'primary' : 'gray')
                    ->icon(fn ($state) => $state ? 'heroicon-o-arrow-path' : 'heroicon-o-arrow-right'),

                Tables\Columns\TextColumn::make('round_trip_price')
                    ->label('Prix A/R')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', ' ') . ' DA' : '-')
                    ->badge()
                    ->color(fn ($state) => $state ? 'info' : 'gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
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
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
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

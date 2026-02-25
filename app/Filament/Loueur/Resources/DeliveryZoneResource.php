<?php

namespace App\Filament\Loueur\Resources;

use App\Filament\Loueur\Resources\DeliveryZoneResource\Pages;
use App\Models\DeliveryZone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DeliveryZoneResource extends Resource
{
    protected static ?string $model = DeliveryZone::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $navigationLabel = 'Zones de livraison';

    protected static ?string $modelLabel = 'Zone de livraison';

    protected static ?string $pluralModelLabel = 'Zones de livraison';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;
        return $loueur && $loueur->isLoueur();
    }

    public static function getEloquentQuery(): Builder
    {
        $loueur = Auth::user()->loueur;

        return parent::getEloquentQuery()
            ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id))
            ->orderBy('sort_order');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de la zone')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom de la zone')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Alger Centre, Aéroport Houari Boumediene'),
                                Forms\Components\Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'city' => 'Ville',
                                        'airport' => 'Aéroport',
                                        'station' => 'Gare',
                                        'hotel' => 'Hôtel',
                                        'custom' => 'Personnalisé',
                                    ])
                                    ->default('city'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('city')
                                    ->label('Ville')
                                    ->placeholder('Ex: Alger'),
                                Forms\Components\TextInput::make('wilaya')
                                    ->label('Wilaya')
                                    ->placeholder('Ex: Alger'),
                            ]),
                    ]),
                Forms\Components\Section::make('Tarification')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('delivery_fee')
                                    ->label('Frais de livraison')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('0 = Gratuit'),
                                Forms\Components\TextInput::make('return_fee')
                                    ->label('Frais de retour')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('0 = Gratuit'),
                                Forms\Components\Select::make('currency')
                                    ->label('Devise')
                                    ->options([
                                        'DZD' => 'Dinar (DA)',
                                        'EUR' => 'Euro (€)',
                                    ])
                                    ->default('DZD'),
                            ]),
                    ]),
                Forms\Components\Section::make('Disponibilité')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Zone active')
                                    ->default(true),
                                Forms\Components\Toggle::make('delivery_available')
                                    ->label('Livraison disponible')
                                    ->default(true),
                                Forms\Components\Toggle::make('return_available')
                                    ->label('Retour disponible')
                                    ->default(true),
                            ]),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre d\'affichage')
                            ->numeric()
                            ->default(0)
                            ->helperText('Les zones avec un ordre plus petit apparaissent en premier'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Zone')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'city',
                        'warning' => 'airport',
                        'info' => 'station',
                        'success' => 'hotel',
                        'gray' => 'custom',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'city' => 'Ville',
                        'airport' => 'Aéroport',
                        'station' => 'Gare',
                        'hotel' => 'Hôtel',
                        'custom' => 'Autre',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ville')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('delivery_fee')
                    ->label('Livraison')
                    ->formatStateUsing(fn ($record) => $record->getFormattedDeliveryFee()),
                Tables\Columns\TextColumn::make('return_fee')
                    ->label('Retour')
                    ->formatStateUsing(fn ($record) => $record->return_fee == 0 ? 'Gratuit' : number_format($record->return_fee, 0, ',', ' ') . ' ' . ($record->currency === 'EUR' ? '€' : 'DA')),
                Tables\Columns\IconColumn::make('delivery_available')
                    ->label('Livr.')
                    ->boolean(),
                Tables\Columns\IconColumn::make('return_available')
                    ->label('Ret.')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'city' => 'Ville',
                        'airport' => 'Aéroport',
                        'station' => 'Gare',
                        'hotel' => 'Hôtel',
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
            ->reorderable('sort_order');
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
            'index' => Pages\ListDeliveryZones::route('/'),
            'create' => Pages\CreateDeliveryZone::route('/create'),
            'edit' => Pages\EditDeliveryZone::route('/{record}/edit'),
        ];
    }
}

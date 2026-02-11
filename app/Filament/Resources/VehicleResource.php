<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use App\Models\Brand;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Véhicules';

    protected static ?string $modelLabel = 'Véhicule';

    protected static ?string $pluralModelLabel = 'Véhicules';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Véhicule')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informations générales')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('brand_id')
                                            ->label('Marque')
                                            ->relationship('brand', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nom')
                                                    ->required(),
                                            ]),

                                        Forms\Components\Select::make('category_id')
                                            ->label('Catégorie')
                                            ->relationship('category', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        Forms\Components\TextInput::make('model')
                                            ->label('Modèle')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state) {
                                                $brand = Brand::find($get('brand_id'));
                                                $brandName = $brand ? $brand->name : '';
                                                $fullName = trim($brandName . ' ' . $state);
                                                $set('full_name', $fullName);
                                                $set('slug', Str::slug($fullName));
                                            }),

                                        Forms\Components\TextInput::make('full_name')
                                            ->label('Nom complet')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),

                                        Forms\Components\TextInput::make('year')
                                            ->label('Année')
                                            ->maxLength(10),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Tarification')
                            ->icon('heroicon-o-currency-euro')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('price_per_day')
                                            ->label('Prix / jour (DA)')
                                            ->required()
                                            ->numeric()
                                            ->suffix('DA'),

                                        Forms\Components\TextInput::make('price_per_day_eur')
                                            ->label('Prix / jour (EUR)')
                                            ->numeric()
                                            ->suffix('EUR'),

                                        Forms\Components\TextInput::make('price_per_week')
                                            ->label('Prix / semaine (DA)')
                                            ->numeric()
                                            ->suffix('DA'),

                                        Forms\Components\TextInput::make('price_per_month')
                                            ->label('Prix / mois (DA)')
                                            ->numeric()
                                            ->suffix('DA'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Caractéristiques')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('transmission')
                                            ->label('Transmission')
                                            ->options([
                                                'automatic' => 'Automatique',
                                                'manual' => 'Manuelle',
                                            ])
                                            ->default('automatic'),

                                        Forms\Components\Select::make('fuel_type')
                                            ->label('Carburant')
                                            ->options([
                                                'diesel' => 'Diesel',
                                                'essence' => 'Essence',
                                                'hybrid' => 'Hybride',
                                                'electric' => 'Électrique',
                                            ])
                                            ->default('diesel'),

                                        Forms\Components\TextInput::make('seats')
                                            ->label('Places')
                                            ->numeric()
                                            ->default(5)
                                            ->minValue(1)
                                            ->maxValue(15),

                                        Forms\Components\TextInput::make('doors')
                                            ->label('Portes')
                                            ->numeric()
                                            ->default(5)
                                            ->minValue(2)
                                            ->maxValue(6),

                                        Forms\Components\TextInput::make('luggage_capacity')
                                            ->label('Bagages')
                                            ->numeric()
                                            ->helperText('Nombre de valises'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Images')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Image principale')
                                    ->image()
                                    ->required()
                                    ->directory('vehicles')
                                    ->visibility('public')
                                    ->imageEditor(),

                                Forms\Components\FileUpload::make('gallery')
                                    ->label('Galerie')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->directory('vehicles/gallery')
                                    ->visibility('public'),
                            ]),

                        Forms\Components\Tabs\Tab::make('État & Options')
                            ->icon('heroicon-o-check-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->label('État')
                                            ->options([
                                                'available' => 'Disponible',
                                                'reserved' => 'Réservé',
                                                'maintenance' => 'En maintenance',
                                                'unavailable' => 'Indisponible',
                                            ])
                                            ->default('available')
                                            ->required(),

                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Ordre d\'affichage')
                                            ->numeric()
                                            ->default(0),

                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Mis en avant')
                                            ->helperText('Affiché en priorité sur le site'),

                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Actif')
                                            ->default(true),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Titre SEO')
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Description SEO')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Photo')
                    ->square(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Véhicule')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marque')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_per_day')
                    ->label('Prix/jour')
                    ->money('DZD', locale: 'fr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('État')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'reserved' => 'info',
                        'maintenance' => 'warning',
                        'unavailable' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Disponible',
                        'reserved' => 'Réservé',
                        'maintenance' => 'Maintenance',
                        'unavailable' => 'Indisponible',
                    }),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Vedette')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Marque')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('État')
                    ->options([
                        'available' => 'Disponible',
                        'reserved' => 'Réservé',
                        'maintenance' => 'Maintenance',
                        'unavailable' => 'Indisponible',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Mis en avant'),
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
            ->defaultSort('sort_order');
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}

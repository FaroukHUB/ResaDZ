<?php

namespace App\Filament\Loueur\Resources;

use App\Filament\Loueur\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use App\Models\Brand;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Mes Véhicules';

    protected static ?string $modelLabel = 'Véhicule';

    protected static ?string $pluralModelLabel = 'Véhicules';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;
        return $loueur && $loueur->isLoueur();
    }

    // Filtrer pour n'afficher que les véhicules du loueur connecté
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
                Forms\Components\Tabs::make('Véhicule')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informations')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('brand_id')
                                            ->label('Marque')
                                            ->options(Brand::pluck('name', 'id'))
                                            ->searchable()
                                            ->required(),
                                        Forms\Components\Select::make('category_id')
                                            ->label('Catégorie')
                                            ->options(Category::pluck('name', 'id'))
                                            ->required(),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('model')
                                            ->label('Modèle')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('full_name')
                                            ->label('Nom complet')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('Ex: VW Tiguan 2024 Noir'),
                                    ]),
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('year')
                                            ->label('Année')
                                            ->numeric()
                                            ->minValue(2000)
                                            ->maxValue(date('Y') + 1),
                                        Forms\Components\Select::make('transmission')
                                            ->label('Transmission')
                                            ->options([
                                                'manual' => 'Manuelle',
                                                'automatic' => 'Automatique',
                                            ]),
                                        Forms\Components\Select::make('fuel_type')
                                            ->label('Carburant')
                                            ->options([
                                                'essence' => 'Essence',
                                                'diesel' => 'Diesel',
                                                'hybrid' => 'Hybride',
                                                'electric' => 'Électrique',
                                            ]),
                                        Forms\Components\TextInput::make('mileage')
                                            ->label('Kilométrage')
                                            ->numeric()
                                            ->suffix('km'),
                                    ]),
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('seats')
                                            ->label('Places')
                                            ->numeric()
                                            ->minValue(2)
                                            ->maxValue(9),
                                        Forms\Components\TextInput::make('doors')
                                            ->label('Portes')
                                            ->numeric()
                                            ->minValue(2)
                                            ->maxValue(5),
                                        Forms\Components\TextInput::make('color')
                                            ->label('Couleur'),
                                        Forms\Components\TextInput::make('luggage_capacity')
                                            ->label('Bagages')
                                            ->numeric()
                                            ->suffix('valises'),
                                    ]),
                                Forms\Components\Toggle::make('has_air_conditioning')
                                    ->label('Climatisation')
                                    ->default(true)
                                    ->helperText('Ce véhicule dispose de la climatisation'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Tarification')
                            ->icon('heroicon-o-currency-euro')
                            ->schema([
                                Forms\Components\Section::make('Prix de base')
                                    ->description('Le prix affiché au client inclura automatiquement les frais de service (+250 DA/jour)')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('price_per_day')
                                                    ->label('Votre prix / jour (DA)')
                                                    ->numeric()
                                                    ->required()
                                                    ->suffix('DA')
                                                    ->helperText('Ce que vous recevez')
                                                    ->live(onBlur: true),
                                                Forms\Components\TextInput::make('price_per_day_eur')
                                                    ->label('Votre prix / jour (EUR)')
                                                    ->numeric()
                                                    ->suffix('€')
                                                    ->helperText('Pour clients diaspora'),
                                            ]),
                                        Forms\Components\Placeholder::make('client_price_info')
                                            ->label('')
                                            ->content(fn ($record) => $record && $record->price_per_day
                                                ? '💡 Prix affiché au client : ' . number_format($record->price_per_day + 250, 0, ',', ' ') . ' DA/jour (votre prix + 250 DA de frais de service)'
                                                : '💡 Le prix affiché au client sera votre prix + 250 DA/jour de frais de service'),
                                    ]),
                                Forms\Components\Section::make('Prix dégressifs')
                                    ->description('Proposez des réductions pour les locations longue durée')
                                    ->schema([
                                        Forms\Components\Repeater::make('degressive_pricing')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('from_days')
                                                            ->label('À partir de (jours)')
                                                            ->numeric()
                                                            ->required()
                                                            ->minValue(2),
                                                        Forms\Components\TextInput::make('price_per_day')
                                                            ->label('Prix / jour (DA)')
                                                            ->numeric()
                                                            ->required()
                                                            ->suffix('DA'),
                                                        Forms\Components\TextInput::make('price_per_day_eur')
                                                            ->label('Prix / jour (EUR)')
                                                            ->numeric()
                                                            ->suffix('€'),
                                                    ]),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('Ajouter un palier')
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                isset($state['from_days']) && isset($state['price_per_day'])
                                                    ? "À partir de {$state['from_days']} jours : {$state['price_per_day']} DA/jour (client: " . ((int)$state['price_per_day'] + 250) . " DA)"
                                                    : null
                                            ),
                                    ]),
                                Forms\Components\Section::make('Caution')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('deposit_amount')
                                                    ->label('Montant caution')
                                                    ->numeric()
                                                    ->suffix('DA'),
                                                Forms\Components\Select::make('deposit_currency')
                                                    ->label('Devise caution')
                                                    ->options([
                                                        'DZD' => 'Dinar (DA)',
                                                        'EUR' => 'Euro (€)',
                                                    ])
                                                    ->default('DZD'),
                                            ]),
                                    ]),
                                Forms\Components\Section::make('Limites')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('min_rental_days')
                                                    ->label('Min jours')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->minValue(1),
                                                Forms\Components\TextInput::make('max_rental_days')
                                                    ->label('Max jours')
                                                    ->numeric()
                                                    ->helperText('Vide = illimité'),
                                                Forms\Components\TextInput::make('mileage_limit_per_day')
                                                    ->label('Km/jour max')
                                                    ->numeric()
                                                    ->suffix('km')
                                                    ->helperText('Vide = illimité'),
                                            ]),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Photos')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Photo principale')
                                    ->image()
                                    ->directory('vehicles')
                                    ->visibility('public'),
                                Forms\Components\FileUpload::make('gallery')
                                    ->label('Galerie')
                                    ->image()
                                    ->multiple()
                                    ->directory('vehicles/gallery')
                                    ->visibility('public')
                                    ->reorderable(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Statut')
                            ->icon('heroicon-o-check-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->label('Statut')
                                            ->options([
                                                'available' => 'Disponible',
                                                'rented' => 'En location',
                                                'maintenance' => 'En maintenance',
                                                'unavailable' => 'Indisponible',
                                            ])
                                            ->default('available')
                                            ->required(),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Actif sur le site')
                                            ->default(true),
                                    ]),
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Mettre en avant')
                                    ->helperText('Affiché sur la page d\'accueil dans sa catégorie'),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\DatePicker::make('available_from')
                                            ->label('Disponible à partir de'),
                                        Forms\Components\DatePicker::make('available_until')
                                            ->label('Disponible jusqu\'au'),
                                    ]),
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
                    ->circular(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Véhicule')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marque')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->badge(),
                Tables\Columns\TextColumn::make('price_per_day')
                    ->label('Prix/jour')
                    ->formatStateUsing(fn ($state) => number_format($state + 250, 0, ',', ' ') . ' DA')
                    ->description(fn ($record) => 'Vous: ' . number_format($record->price_per_day, 0, ',', ' ') . ' DA')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'available',
                        'warning' => 'rented',
                        'danger' => 'maintenance',
                        'gray' => 'unavailable',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'available' => 'Disponible',
                        'rented' => 'En location',
                        'maintenance' => 'Maintenance',
                        'unavailable' => 'Indisponible',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'available' => 'Disponible',
                        'rented' => 'En location',
                        'maintenance' => 'Maintenance',
                        'unavailable' => 'Indisponible',
                    ]),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggleStatus')
                    ->label('Changer statut')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Nouveau statut')
                            ->options([
                                'available' => 'Disponible',
                                'rented' => 'En location',
                                'maintenance' => 'Maintenance',
                                'unavailable' => 'Indisponible',
                            ])
                            ->required(),
                    ])
                    ->action(function (Vehicle $record, array $data) {
                        $record->update(['status' => $data['status']]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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

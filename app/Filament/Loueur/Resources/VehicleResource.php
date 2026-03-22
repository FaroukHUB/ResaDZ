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
                // Section 1: Informations du véhicule
                Forms\Components\Section::make('Informations du véhicule')
                    ->icon('heroicon-o-truck')
                    ->description('Ces informations sont affichées sur la fiche de votre véhicule côté client. Plus c\'est complet, plus les clients auront confiance.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('brand_id')
                                    ->label('Marque')
                                    ->options(Brand::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->helperText('La marque apparaît dans les filtres de recherche'),
                                Forms\Components\Select::make('category_id')
                                    ->label('Catégorie')
                                    ->options(Category::pluck('name', 'id'))
                                    ->required()
                                    ->helperText('Citadine, SUV, Berline... Les clients filtrent par catégorie'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('model')
                                    ->label('Modèle')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Ex: Clio, Golf, Tucson...'),
                                Forms\Components\TextInput::make('full_name')
                                    ->label('Nom complet')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Titre affiché sur la carte du véhicule. Ex: VW Tiguan 2024 Noir'),
                            ]),
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('year')
                                    ->label('Année')
                                    ->numeric()
                                    ->minValue(2000)
                                    ->maxValue(date('Y') + 1)
                                    ->helperText('Année de mise en circulation'),
                                Forms\Components\Select::make('transmission')
                                    ->label('Transmission')
                                    ->options([
                                        'manual' => 'Manuelle',
                                        'automatic' => 'Automatique',
                                    ])
                                    ->helperText('Filtre très utilisé par les clients'),
                                Forms\Components\Select::make('fuel_type')
                                    ->label('Carburant')
                                    ->options([
                                        'essence' => 'Essence',
                                        'diesel' => 'Diesel',
                                        'hybrid' => 'Hybride',
                                        'electric' => 'Électrique',
                                    ])
                                    ->helperText('Affiché sur la fiche véhicule'),
                                Forms\Components\TextInput::make('color')
                                    ->label('Couleur')
                                    ->helperText('Aide les clients à identifier le véhicule'),
                            ]),
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('seats')
                                    ->label('Places')
                                    ->numeric()
                                    ->minValue(2)
                                    ->maxValue(9)
                                    ->helperText('Nombre de passagers max'),
                                Forms\Components\TextInput::make('doors')
                                    ->label('Portes')
                                    ->numeric()
                                    ->minValue(2)
                                    ->maxValue(5)
                                    ->helperText('2, 3, 4 ou 5 portes'),
                                Forms\Components\TextInput::make('mileage')
                                    ->label('Kilométrage')
                                    ->numeric()
                                    ->suffix('km')
                                    ->helperText('Kilométrage actuel du véhicule'),
                                Forms\Components\TextInput::make('luggage_capacity')
                                    ->label('Bagages')
                                    ->numeric()
                                    ->suffix('valises')
                                    ->helperText('Capacité du coffre en nombre de valises'),
                            ]),
                        Forms\Components\Toggle::make('has_air_conditioning')
                            ->label('Climatisation')
                            ->default(true)
                            ->helperText('Cochez si le véhicule est climatisé — très recherché en été'),
                    ]),

                // Section 2: Tarification
                Forms\Components\Section::make('Tarification')
                    ->icon('heroicon-o-currency-euro')
                    ->description('Définissez vos tarifs. Le prix affiché aux clients inclut la commission ResaDZ.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price_per_day')
                                    ->label('Votre prix / jour (DA)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('DA')
                                    ->live(onBlur: true)
                                    ->helperText('Prix affiché aux clients algériens — le plus important !'),
                                Forms\Components\TextInput::make('price_per_day_eur')
                                    ->label('Votre prix / jour (EUR)')
                                    ->numeric()
                                    ->suffix('€')
                                    ->helperText('Optionnel — pour la diaspora qui réserve depuis l\'étranger'),
                            ]),
                        // Commission info box - always visible
                        Forms\Components\Placeholder::make('commission_info')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-blue-900 dark:text-blue-100">Commission ResaDZ</p>
                                            <p class="text-sm text-blue-800 dark:text-blue-200 mt-1">
                                                ResaDZ prélève <strong>150 DA/jour</strong> (ou <strong>1€/jour</strong> pour les prix en EUR) sur chaque réservation.
                                            </p>
                                            <p class="text-xs text-blue-600 dark:text-blue-300 mt-2">
                                                <strong>Pourquoi ?</strong> Cette commission couvre : visibilité sur la plateforme, gestion des réservations, support client 7j/7, paiements sécurisés et protection de vos données.
                                            </p>
                                            <p class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                                <strong>Exemple :</strong> Vous fixez 5 000 DA/jour → Vous recevez 4 850 DA/jour après commission.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            ')),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('deposit_amount')
                                    ->label('Caution en DA')
                                    ->numeric()
                                    ->suffix('DA')
                                    ->helperText('Montant bloqué pendant la location — remboursé si pas de dégâts'),
                                Forms\Components\TextInput::make('deposit_amount_eur')
                                    ->label('Caution en EUR')
                                    ->numeric()
                                    ->suffix('€')
                                    ->helperText('Pour les paiements en devise étrangère'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('min_rental_days')
                                    ->label('Durée min')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->suffix('jours')
                                    ->helperText('Les clients ne pourront pas réserver moins'),
                                Forms\Components\TextInput::make('max_rental_days')
                                    ->label('Durée max')
                                    ->numeric()
                                    ->suffix('jours')
                                    ->placeholder('Illimité')
                                    ->helperText('Laissez vide pour aucune limite'),
                                Forms\Components\TextInput::make('mileage_limit_per_day')
                                    ->label('Km/jour max')
                                    ->numeric()
                                    ->suffix('km')
                                    ->placeholder('Illimité')
                                    ->helperText('Limite affichée au client. Laissez vide = km illimités'),
                            ]),
                    ]),

                // Section 3: Prix dégressifs (optionnel)
                Forms\Components\Section::make('Prix dégressifs (optionnel)')
                    ->icon('heroicon-o-arrow-trending-down')
                    ->description('Attirez plus de clients avec des réductions longue durée ! Les prix dégressifs sont affichés sur votre fiche véhicule et incitent les clients à réserver plus longtemps.')
                    ->collapsed()
                    ->collapsible()
                    ->schema([
                        Forms\Components\Placeholder::make('degressive_help')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg text-sm">
                                    <p class="font-semibold text-amber-800 dark:text-amber-200">💡 Exemple de prix dégressifs :</p>
                                    <ul class="mt-2 text-amber-700 dark:text-amber-300 space-y-1">
                                        <li>• Prix de base : 5 000 DA/jour</li>
                                        <li>• À partir de 7 jours : 4 500 DA/jour (-10%)</li>
                                        <li>• À partir de 14 jours : 4 000 DA/jour (-20%)</li>
                                        <li>• À partir de 30 jours : 3 500 DA/jour (-30%)</li>
                                    </ul>
                                </div>
                            ')),
                        Forms\Components\Repeater::make('degressive_pricing')
                            ->label('')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('from_days')
                                            ->label('À partir de')
                                            ->numeric()
                                            ->required()
                                            ->minValue(2)
                                            ->suffix('jours')
                                            ->helperText('Nombre minimum de jours pour ce tarif'),
                                        Forms\Components\TextInput::make('price_per_day')
                                            ->label('Prix / jour (DA)')
                                            ->numeric()
                                            ->required()
                                            ->suffix('DA')
                                            ->helperText('Prix réduit par jour'),
                                        Forms\Components\TextInput::make('price_per_day_eur')
                                            ->label('Prix / jour (EUR)')
                                            ->numeric()
                                            ->suffix('€')
                                            ->helperText('Optionnel'),
                                    ]),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un palier')
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['from_days']) && isset($state['price_per_day'])
                                    ? "À partir de {$state['from_days']} jours : {$state['price_per_day']} DA/jour"
                                    : null
                            ),
                    ]),

                // Section 4: Photos
                Forms\Components\Section::make('Photos')
                    ->icon('heroicon-o-photo')
                    ->description('Des photos de qualité augmentent vos réservations de 60% ! Prenez des photos en journée, véhicule propre, sous plusieurs angles.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Placeholder::make('photo_tips')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg text-sm">
                                    <p class="font-semibold text-green-800 dark:text-green-200">📸 Conseils pour de bonnes photos :</p>
                                    <ul class="mt-2 text-green-700 dark:text-green-300 grid grid-cols-2 gap-1">
                                        <li>• Extérieur 3/4 avant (la plus importante)</li>
                                        <li>• Extérieur 3/4 arrière</li>
                                        <li>• Intérieur (tableau de bord)</li>
                                        <li>• Sièges arrière</li>
                                        <li>• Coffre ouvert</li>
                                        <li>• Compteur kilométrique</li>
                                    </ul>
                                </div>
                            ')),
                        Forms\Components\FileUpload::make('image')
                            ->label('Photo principale')
                            ->image()
                            ->directory('vehicles')
                            ->visibility('public')
                            ->helperText('Photo affichée en premier sur les cartes de recherche — choisissez la meilleure !'),
                        Forms\Components\FileUpload::make('gallery')
                            ->label('Galerie (optionnel)')
                            ->image()
                            ->multiple()
                            ->directory('vehicles/gallery')
                            ->visibility('public')
                            ->reorderable()
                            ->helperText('Glissez pour réorganiser. Plus vous en mettez, plus les clients auront confiance.'),
                    ]),

                // Section 5: Disponibilité
                Forms\Components\Section::make('Disponibilité')
                    ->icon('heroicon-o-calendar')
                    ->description('Contrôlez quand et si votre véhicule apparaît dans les résultats de recherche.')
                    ->collapsible()
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
                                    ->required()
                                    ->helperText('Statut affiché aux clients sur la fiche véhicule'),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Visible sur le site')
                                    ->default(true)
                                    ->helperText('Désactivez pour masquer le véhicule des recherches sans le supprimer'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('available_from')
                                    ->label('Disponible à partir de')
                                    ->helperText('Laissez vide si disponible immédiatement'),
                                Forms\Components\DatePicker::make('available_until')
                                    ->label('Disponible jusqu\'au')
                                    ->helperText('Laissez vide si pas de date de fin'),
                            ]),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Mettre en avant sur la page d\'accueil')
                            ->helperText('Les véhicules mis en avant apparaissent dans la section "Sélection" de la page d\'accueil'),
                    ]),
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
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' DA')
                    ->description(fn ($record) => 'Net: ' . number_format($record->price_per_day - 150, 0, ',', ' ') . ' DA')
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

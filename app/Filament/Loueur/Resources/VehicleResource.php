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
                    ->iconColor('primary')
                    ->description('Ces informations sont affichées sur la fiche de votre véhicule côté client. Plus c\'est complet, plus les clients auront confiance.')
                    ->collapsible()
                    ->extraAttributes(['style' => 'border-left: 4px solid #f59e0b;'])
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
                                    ->required()
                                    ->minValue(2000)
                                    ->maxValue(date('Y') + 1)
                                    ->helperText('Année de mise en circulation'),
                                Forms\Components\Select::make('transmission')
                                    ->label('Transmission')
                                    ->required()
                                    ->options([
                                        'manual' => 'Manuelle',
                                        'automatic' => 'Automatique',
                                    ])
                                    ->helperText('Filtre très utilisé par les clients'),
                                Forms\Components\Select::make('fuel_type')
                                    ->label('Carburant')
                                    ->required()
                                    ->options([
                                        'essence' => 'Essence',
                                        'diesel' => 'Diesel',
                                        'hybrid' => 'Hybride',
                                        'electric' => 'Électrique',
                                    ])
                                    ->helperText('Affiché sur la fiche véhicule'),
                                Forms\Components\Select::make('color')
                                    ->label('Couleur')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->options([
                                        'noir' => 'Noir', 'blanc' => 'Blanc', 'gris' => 'Gris',
                                        'rouge' => 'Rouge', 'bleu' => 'Bleu', 'vert' => 'Vert',
                                        'beige' => 'Beige', 'marron' => 'Marron', 'orange' => 'Orange', 'jaune' => 'Jaune',
                                    ])
                                    ->searchable()
                                    ->helperText('Important pour le visuel studio automatique'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('seats')
                                    ->label('Places')
                                    ->numeric()
                                    ->required()
                                    ->minValue(2)
                                    ->maxValue(9)
                                    ->helperText('Nombre de passagers max'),
                                Forms\Components\TextInput::make('doors')
                                    ->label('Portes')
                                    ->numeric()
                                    ->required()
                                    ->minValue(2)
                                    ->maxValue(5)
                                    ->helperText('2, 3, 4 ou 5 portes'),
                                Forms\Components\TextInput::make('luggage_capacity')
                                    ->label('Bagages')
                                    ->numeric()
                                    ->required()
                                    ->suffix('valises')
                                    ->helperText('Capacité du coffre en nombre de valises'),
                            ]),
                    ]),

                // Section 1b: Équipements & Caractéristiques
                Forms\Components\Section::make('Équipements & Caractéristiques')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->iconColor('info')
                    ->description('Cochez les équipements présents dans le véhicule. Plus il y en a, plus les clients seront convaincus.')
                    ->collapsible()
                    ->extraAttributes(['style' => 'border-left: 4px solid #60a5fa;'])
                    ->schema([
                        Forms\Components\Toggle::make('has_air_conditioning')
                            ->label('❄️ Climatisation')
                            ->default(true),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('features.onboard_computer')
                                    ->label('🖥️ Ordinateur de bord'),
                                Forms\Components\Toggle::make('features.carplay')
                                    ->label('📱 CarPlay / Android Auto'),
                                Forms\Components\Toggle::make('features.cruise_control')
                                    ->label('🚀 Régulateur de vitesse'),
                                Forms\Components\Toggle::make('features.bluetooth')
                                    ->label('🔵 Bluetooth'),
                                Forms\Components\Toggle::make('features.rear_camera')
                                    ->label('📷 Caméra de recul'),
                                Forms\Components\Toggle::make('features.parking_sensors')
                                    ->label('📡 Radar de stationnement'),
                                Forms\Components\Toggle::make('features.gps')
                                    ->label('🗺️ GPS intégré'),
                                Forms\Components\Toggle::make('features.heated_seats')
                                    ->label('🔥 Sièges chauffants'),
                                Forms\Components\Toggle::make('features.sunroof')
                                    ->label('☀️ Toit ouvrant'),
                                Forms\Components\Toggle::make('features.electric_windows')
                                    ->label('🪟 Vitres électriques'),
                                Forms\Components\Toggle::make('features.central_locking')
                                    ->label('🔒 Verrouillage centralisé'),
                                Forms\Components\Toggle::make('features.dashcam')
                                    ->label('🎥 Dashcam'),
                                Forms\Components\Toggle::make('features.usb_port')
                                    ->label('🔌 Port USB'),
                                Forms\Components\Toggle::make('features.start_stop')
                                    ->label('⏯️ Start & Stop'),
                                Forms\Components\Toggle::make('features.keyless_entry')
                                    ->label('🔑 Démarrage sans clé'),
                                Forms\Components\Toggle::make('features.abs_esp')
                                    ->label('🛡️ ABS / ESP'),
                                Forms\Components\Toggle::make('features.led_lights')
                                    ->label('💡 Phares LED'),
                            ]),
                    ]),

                // Section 2: Tarification
                Forms\Components\Section::make('Tarification')
                    ->icon('heroicon-o-currency-euro')
                    ->iconColor('success')
                    ->description('Définissez vos tarifs. Le prix affiché aux clients inclut la commission ResaDZ.')
                    ->collapsible()
                    ->extraAttributes(['style' => 'border-left: 4px solid #34d399;'])
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price_per_day')
                                    ->label('Votre prix / jour (DA)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('DA')
                                    ->live(onBlur: true)
                                    ->helperText(new \Illuminate\Support\HtmlString('Prix affiché aux clients algériens — le plus important !<br><em class="text-xs text-slate-500">💡 Astuce : ajoutez 500 DA à votre tarif habituel pour couvrir la commission ResaDZ et maximiser vos revenus nets.</em>')),
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
                                                ResaDZ prélève une commission <strong>dégressive</strong> selon la durée de location :
                                            </p>
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-200 dark:bg-blue-800 text-blue-900 dark:text-blue-100">1 à 10 jours → 8%</span>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-200 dark:bg-blue-800 text-blue-900 dark:text-blue-100">+10 jours → 6%</span>
                                            </div>
                                            <div class="mt-3 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-lg">
                                                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">💡 Astuce : ajoutez 500 DA à votre tarif habituel pour couvrir la commission ResaDZ et maximiser vos revenus nets.</p>
                                                <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-1">
                                                    <strong>Exemple :</strong> Vous visez 6 000 DA nets ? Affichez <strong>6 500 DA</strong>. Ça couvre la commission et vous gardez 100% de vos revenus cibles.
                                                </p>
                                            </div>
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
                    ->iconColor('warning')
                    ->description('Attirez plus de clients avec des réductions longue durée ! Les prix dégressifs sont affichés sur votre fiche véhicule et incitent les clients à réserver plus longtemps.')
                                        ->collapsible()
                    ->extraAttributes(['style' => 'border-left: 4px solid #fbbf24;'])
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

                // Section 3bis: Tarifs saisonniers
                Forms\Components\Section::make('Tarifs saisonniers')
                    ->icon('heroicon-o-sun')
                    ->iconColor('warning')
                    ->description('Définissez des suppléments pour les périodes de forte demande.')
                                        ->collapsible()
                    ->extraAttributes(['style' => 'border-left: 4px solid #fb923c;'])
                    ->schema([
                        Forms\Components\Placeholder::make('seasonal_help')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-2xl">
                                    <div class="flex items-start gap-3">
                                        <span class="text-2xl flex-shrink-0">💡</span>
                                        <div>
                                            <p class="font-bold text-amber-900 dark:text-amber-100">Augmentez votre tarif pendant les périodes de forte demande</p>
                                            <p class="text-sm text-amber-800 dark:text-amber-200 mt-1">
                                                Été, vacances scolaires… Vos clients verront clairement le supplément avant de réserver.
                                                Le prix affiché sera automatiquement majoré pendant ces périodes.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            ')),
                        Forms\Components\Repeater::make('seasonalRates')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nom de la période')
                                            ->required()
                                            ->placeholder('Ex: Été 2026, Vacances scolaires')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('supplement_amount')
                                            ->label('Supplément / jour')
                                            ->numeric()
                                            ->required()
                                            ->suffix('DA')
                                            ->placeholder('Ex: 1000')
                                            ->helperText('+1000 DA = prix affiché automatiquement majoré pendant cette période'),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\DatePicker::make('start_date')
                                            ->label('Date de début')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y'),
                                        Forms\Components\DatePicker::make('end_date')
                                            ->label('Date de fin')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->after('start_date'),
                                    ]),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Période active')
                                    ->default(true)
                                    ->helperText('Désactivez pour suspendre cette période sans la supprimer'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une période')
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['name']) && isset($state['supplement_amount'])
                                    ? "{$state['name']} — +{$state['supplement_amount']} DA/jour"
                                    : null
                            )
                            ->collapsible(),
                    ]),

                // Section 4: Photos
                Forms\Components\Section::make('Photos')
                    ->icon('heroicon-o-photo')
                    ->iconColor('info')
                    ->description('Des photos de qualité augmentent vos réservations de 60% ! Prenez des photos en journée, véhicule propre, sous plusieurs angles.')
                    ->collapsible()
                    ->extraAttributes(['style' => 'border-left: 4px solid #38bdf8;'])
                    ->schema([
                        Forms\Components\Placeholder::make('photo_tips')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-2xl">
                                    <div class="flex items-start gap-3">
                                        <span class="text-2xl flex-shrink-0">📸</span>
                                        <div>
                                            <p class="font-bold text-amber-900 dark:text-amber-100">Photo principale — fond blanc ou noir obligatoire</p>
                                            <p class="text-sm text-amber-800 dark:text-amber-200 mt-1">
                                                C\'est l\'image qui représente votre véhicule sur toute la plateforme.
                                                Prenez-la dans un endroit bien éclairé, fond uni blanc ou noir.
                                                Les autres photos : intérieur, détails, angles — montrez tout !
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            ')),
                        Forms\Components\Placeholder::make('template_preview')
                            ->label('')
                            ->visible(function (Forms\Get $get, $record): bool {
                                $brandId = $get('brand_id');
                                $model = $get('model');
                                $color = $get('color');
                                if (!$brandId || !$model || !$color) {
                                    // En edit, checker via le record
                                    if ($record && !$record->image) {
                                        try { return $record->display_image !== null; } catch (\Exception $e) { return false; }
                                    }
                                    return false;
                                }
                                try {
                                    return \App\Models\VehicleTemplate::where('brand_id', $brandId)
                                        ->whereRaw('LOWER(model_name) = ?', [strtolower($model)])
                                        ->where('color', $color)
                                        ->where('is_active', true)
                                        ->exists();
                                } catch (\Exception $e) {
                                    return false;
                                }
                            })
                            ->content(function (Forms\Get $get, $record): \Illuminate\Support\HtmlString {
                                $brandId = $get('brand_id');
                                $model = $get('model');
                                $color = $get('color');
                                $template = null;
                                try {
                                    if ($brandId && $model && $color) {
                                        $template = \App\Models\VehicleTemplate::where('brand_id', $brandId)
                                            ->whereRaw('LOWER(model_name) = ?', [strtolower($model)])
                                            ->where('color', $color)
                                            ->where('is_active', true)
                                            ->first();
                                    }
                                    if (!$template && $record && !$record->image && $record->display_image) {
                                        $imgUrl = asset('storage/' . $record->display_image);
                                    } elseif ($template) {
                                        $imgUrl = asset('storage/' . $template->image_path);
                                    } else {
                                        return new \Illuminate\Support\HtmlString('');
                                    }
                                } catch (\Exception $e) {
                                    return new \Illuminate\Support\HtmlString('');
                                }
                                return new \Illuminate\Support\HtmlString("
                                    <div class='p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-2xl'>
                                        <div class='flex items-start gap-4'>
                                            <img src='{$imgUrl}' alt='Visuel studio' class='w-48 rounded-xl border border-green-300 shadow-sm'>
                                            <div>
                                                <p class='font-bold text-green-900 dark:text-green-100'>Visuel studio ResaDZ disponible !</p>
                                                <p class='text-sm text-green-800 dark:text-green-200 mt-1'>
                                                    Ce visuel sera affiché automatiquement si vous ne mettez pas de photo.
                                                </p>
                                                <p class='text-xs text-green-600 dark:text-green-300 mt-2'>
                                                    Vous pouvez aussi uploader votre propre photo — elle sera prioritaire.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ");
                            }),
                        Forms\Components\FileUpload::make('image')
                            ->label('Photo principale')
                            ->image()
                            ->directory('vehicles')
                            ->visibility('public')
                            ->helperText('Laissez vide si un visuel studio ResaDZ est disponible pour votre véhicule.'),
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
                    ->iconColor('gray')
                    ->description('Contrôlez quand et si votre véhicule apparaît dans les résultats de recherche.')
                    ->collapsible()
                    ->extraAttributes(['style' => 'border-left: 4px solid #9ca3af;'])
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
                    ]),

                // Section 6: Badges
                Forms\Components\Section::make('Badges du véhicule')
                    ->icon('heroicon-o-check-badge')
                    ->iconColor('success')
                    ->description('Les badges apparaissent sur la carte du véhicule et rassurent les clients. Cochez ceux qui s\'appliquent à ce véhicule.')
                    ->collapsible()
                    ->extraAttributes(['style' => 'border-left: 4px solid #4ade80;'])
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('vehicle_badges.badge_insurance')
                                    ->label('Assurance incluse')
                                    ->helperText('Le véhicule est assuré tous risques'),
                                Forms\Components\Toggle::make('vehicle_badges.badge_delivery')
                                    ->label('Livraison offerte')
                                    ->helperText('Vous livrez le véhicule gratuitement'),
                                Forms\Components\Toggle::make('vehicle_badges.badge_degressive')
                                    ->label('Prix dégressif selon la durée')
                                    ->helperText('Tarifs réduits pour les longues durées'),
                                Forms\Components\Toggle::make('vehicle_badges.badge_airport')
                                    ->label('Livraison aéroport')
                                    ->helperText('Vous livrez à l\'aéroport'),
                                Forms\Components\Toggle::make('vehicle_badges.badge_km_unlimited')
                                    ->label('Kilométrage illimité')
                                    ->helperText('Pas de limite de kilomètres'),
                            ]),
                        Forms\Components\Repeater::make('vehicle_badges.custom_badges')
                            ->label('Badges personnalisés')
                            ->schema([
                                Forms\Components\TextInput::make('text')
                                    ->label('Texte du badge')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('Ex: Wifi inclus, Siège bébé offert...'),
                            ])
                            ->defaultItems(0)
                            ->maxItems(3)
                            ->addActionLabel('Ajouter un badge personnalisé'),
                    ]),

                // Section 7: Options de location
                Forms\Components\Section::make('Options de location (facultatif)')
                    ->icon('heroicon-o-squares-plus')
                    ->iconColor('primary')
                    ->description('Proposez des options payantes ou gratuites aux clients. Ex: GPS, siège bébé, conducteur additionnel...')
                                        ->extraAttributes(['style' => 'border-left: 4px solid #a78bfa;'])
                    ->collapsible()
                    ->schema([
                        Forms\Components\Repeater::make('vehicle_options')
                            ->label('')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nom de l\'option')
                                            ->required()
                                            ->maxLength(100)
                                            ->placeholder('Ex: GPS, Siège bébé, Conducteur additionnel...'),
                                        Forms\Components\TextInput::make('price')
                                            ->label('Prix')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->default(0)
                                            ->helperText('Mettez 0 pour une option gratuite'),
                                        Forms\Components\Select::make('per')
                                            ->label('Facturation')
                                            ->options([
                                                'day' => 'Par jour',
                                                'booking' => 'Par location',
                                            ])
                                            ->default('day'),
                                    ]),
                                Forms\Components\TextInput::make('description')
                                    ->label('Description courte (optionnel)')
                                    ->maxLength(255)
                                    ->placeholder('Ex: Système de navigation GPS intégré'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une option')
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['name'])
                                    ? $state['name'] . (isset($state['price']) && $state['price'] > 0 ? ' — ' . number_format($state['price'], 0, ',', ' ') . ' DA' : ' — OFFERT')
                                    : null
                            )
                            ->collapsible(),
                    ]),

                // Section 8: Frais de retour
                Forms\Components\Section::make('Frais de retour')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->iconColor('danger')
                    ->description('Frais facturés au client si le véhicule est rendu sans le plein ou sans lavage. Mettez 0 pour désactiver.')
                    ->collapsible()
                    ->extraAttributes(['style' => 'border-left: 4px solid #f87171;'])
                    ->schema([
                        Forms\Components\Placeholder::make('return_fees_info')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl">
                                    <div class="flex items-start gap-3">
                                        <span class="text-xl flex-shrink-0">⛽</span>
                                        <div>
                                            <p class="font-semibold text-red-900 dark:text-red-100">Ces frais apparaissent comme options cochables sur la page de réservation</p>
                                            <p class="text-sm text-red-800 dark:text-red-200 mt-1">Le client peut choisir de payer ces frais à l\'avance s\'il sait qu\'il ne rendra pas le véhicule avec le plein ou lavé.</p>
                                        </div>
                                    </div>
                                </div>
                            ')),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('fuel_return_fee')
                                    ->label('Frais retour sans plein')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('DA')
                                    ->helperText('0 = option désactivée'),
                                Forms\Components\TextInput::make('wash_return_fee')
                                    ->label('Frais retour sans lavage')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('DA')
                                    ->helperText('0 = option désactivée'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('display_image')
                    ->label('Photo')
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->display_image),
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
                Tables\Actions\Action::make('duplicate')
                    ->label('Dupliquer')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Dupliquer ce véhicule')
                    ->modalDescription('Un nouveau véhicule sera créé avec les mêmes informations (sans les photos). Vous pourrez ensuite le modifier.')
                    ->action(function (Vehicle $record) {
                        $loueur = \Illuminate\Support\Facades\Auth::user()->loueur;
                        $newVehicle = $record->replicate([
                            'slug', 'image', 'gallery', 'is_featured', 'is_in_selection',
                        ]);
                        $newVehicle->slug = \Illuminate\Support\Str::slug($record->full_name) . '-' . \Illuminate\Support\Str::random(4);
                        $newVehicle->full_name = $record->full_name . ' (copie)';
                        $newVehicle->loueur_id = $loueur->id;
                        $newVehicle->status = 'unavailable';
                        $newVehicle->is_active = false;
                        $newVehicle->save();

                        \Filament\Notifications\Notification::make()
                            ->title('Véhicule dupliqué')
                            ->body('Modifiez le nom, le prix et ajoutez les photos.')
                            ->success()
                            ->send();

                        return redirect(VehicleResource::getUrl('edit', ['record' => $newVehicle]));
                    }),
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

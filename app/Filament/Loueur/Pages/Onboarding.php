<?php

namespace App\Filament\Loueur\Pages;

use App\Models\DeliveryZone;
use App\Models\Loueur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Onboarding extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $navigationLabel = 'Configuration';

    protected static ?string $title = 'Configurez votre espace';

    protected static ?int $navigationSort = -1;

    protected static string $view = 'filament.loueur.pages.onboarding';

    public ?array $data = [];
    public int $currentStep = 0;
    public int $totalSteps = 8;

    // CGU checkboxes (not persisted via form, handled in blade/wire)
    public bool $cguAccepted = false;
    public bool $contratAccepted = false;

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;

        return $loueur
            && !$loueur->hasCompletedOnboarding()
            && !empty($loueur->account_type)
            && $loueur->account_type === 'loueur';
    }

    public function mount(): void
    {
        $loueur = Auth::user()?->loueur;

        if (!$loueur) {
            $this->redirect(route('filament.loueur.pages.dashboard'));
            return;
        }

        // If no account_type, redirect to choice page
        if (empty($loueur->account_type)) {
            $this->redirect(route('filament.loueur.pages.onboarding-choice'));
            return;
        }

        // If taxi, redirect to chauffeur onboarding
        if ($loueur->account_type === 'taxi') {
            $this->redirect(route('filament.loueur.pages.onboarding-chauffeur'));
            return;
        }

        // If already completed, redirect to dashboard
        if ($loueur->hasCompletedOnboarding()) {
            $this->redirect(route('filament.loueur.pages.dashboard'));
            return;
        }

        // Set current step from saved progress (0-based)
        $this->currentStep = min($loueur->onboarding_step, $this->totalSteps - 1);

        // Check if CGU already accepted
        $this->cguAccepted = !empty($loueur->getSetting('cgu_accepted_at'));
        $this->contratAccepted = !empty($loueur->getSetting('contrat_loueur_accepted_at'));

        // Load existing data
        $this->form->fill($this->loadStepData($loueur));
    }

    protected function loadStepData(Loueur $loueur): array
    {
        return [
            // Step 1: Profile
            'company_name' => $loueur->company_name,
            'description' => $loueur->description,
            'phone' => $loueur->phone,
            'whatsapp' => $loueur->whatsapp,
            'email_contact' => $loueur->email_contact,
            'address' => $loueur->address,
            'city' => $loueur->city,
            'wilaya' => $loueur->wilaya,
            // Chauffeur toggle in step 1
            'offers_transfer' => (bool) $loueur->offers_transfer,
            'offers_delivery' => (bool) $loueur->offers_delivery,
            'offers_city_rides' => (bool) $loueur->getSetting('offers_city_rides', false),

            // Step 2: Zones
            'delivery_zones' => $loueur->deliveryZones()->orderBy('sort_order')->get()->map(fn ($zone) => [
                'id' => $zone->id,
                'name' => $zone->name,
                'type' => $zone->type,
                'city' => $zone->city,
                'delivery_fee' => $zone->delivery_fee,
                'is_active' => $zone->is_active,
            ])->toArray(),

            // Step 3: Reservations
            'advance_percentage' => $loueur->getSetting('advance_percentage', 30),
            'advance_payment_methods' => $loueur->getSetting('advance_payment_methods', []),
            'paypal_email' => $loueur->paypal_email,
            'cancellation_deadline_hours' => $loueur->getSetting('cancellation_deadline_hours', 48),
            'auto_confirm_bookings' => $loueur->getSetting('auto_confirm_bookings', false),
            'require_documents' => $loueur->getSetting('require_documents', true),

            // Step 4: Options
            'return_margin_hours' => $loueur->getSetting('return_margin_hours', 2),
            'fuel_return_fee' => $loueur->getSetting('fuel_return_fee', 0),
            'wash_return_fee' => $loueur->getSetting('wash_return_fee', 0),
            'rental_options' => $loueur->getSetting('rental_options', []),

            // Step 5: Conditions (predefined toggles)
            'cond_no_smoking' => $loueur->getSetting('cond_no_smoking', false),
            'cond_km_limit' => $loueur->getSetting('cond_km_limit', false),
            'cond_km_limit_value' => $loueur->getSetting('cond_km_limit_value', '250'),
            'cond_km_extra_fee' => $loueur->getSetting('cond_km_extra_fee', '15'),
            'cond_min_age' => $loueur->getSetting('cond_min_age', false),
            'cond_min_age_value' => $loueur->getSetting('cond_min_age_value', '21'),
            'cond_min_license_years' => $loueur->getSetting('cond_min_license_years', false),
            'cond_min_license_years_value' => $loueur->getSetting('cond_min_license_years_value', '2'),
            'cond_caution' => $loueur->getSetting('cond_caution', false),
            'cond_caution_value' => $loueur->getSetting('cond_caution_value', '50000'),
            'cond_fuel_full' => $loueur->getSetting('cond_fuel_full', false),
            'cond_no_pets' => $loueur->getSetting('cond_no_pets', false),
            'cond_no_offroad' => $loueur->getSetting('cond_no_offroad', false),
            'cond_algeria_only' => $loueur->getSetting('cond_algeria_only', false),
            'cond_clean_return' => $loueur->getSetting('cond_clean_return', false),
            'cond_id_required' => $loueur->getSetting('cond_id_required', false),
            'cond_no_subletting' => $loueur->getSetting('cond_no_subletting', false),
            'conditions_pdf' => $loueur->getSetting('conditions_pdf', null) ?: null,
            'rental_conditions' => $loueur->getSetting('rental_conditions', []) ?: [],

            // Step 6: Badges
            'badge_insurance' => $loueur->getSetting('badge_insurance', false),
            'badge_delivery' => $loueur->getSetting('badge_delivery', false),
            'badge_degressive' => $loueur->getSetting('badge_degressive', false),
            'badge_airport' => $loueur->getSetting('badge_airport', false),
            'badge_km_unlimited' => $loueur->getSetting('badge_km_unlimited', false),
            'custom_badges' => $loueur->getSetting('custom_badges', []),

            // Step 7: Notifications
            'notify_push' => $loueur->getSetting('notify_push', true),
            'notify_whatsapp' => $loueur->getSetting('notify_whatsapp', true),
            'notify_email' => $loueur->getSetting('notify_email', true),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getStepSchema())
            ->statePath('data');
    }

    protected function getStepSchema(): array
    {
        return match ($this->currentStep) {
            0 => $this->getCguSchema(),
            1 => $this->getProfileSchema(),
            2 => $this->getZonesSchema(),
            3 => $this->getReservationsSchema(),
            4 => $this->getOptionsSchema(),
            5 => $this->getConditionsSchema(),
            6 => $this->getBadgesSchema(),
            7 => $this->getNotificationsSchema(),
            default => [],
        };
    }

    // ─── STEP 0: CGU ────────────────────────────────────────────────
    protected function getCguSchema(): array
    {
        // CGU step is handled entirely in Blade (scrollable contract, checkboxes)
        // We return an empty schema — the blade view handles this step's UI
        return [];
    }

    // ─── STEP 1: PROFILE ────────────────────────────────────────────
    protected function getProfileSchema(): array
    {
        return [
            Forms\Components\Section::make('Informations de votre agence')
                ->description('Ces informations seront visibles par vos clients. Remplissez-les soigneusement pour inspirer confiance.')
                ->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->label('Nom de l\'agence')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ex: Auto Location Alger'),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->maxLength(1000)
                        ->placeholder('Présentez votre agence en quelques phrases...'),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('phone')
                                ->label('Téléphone')
                                ->tel()
                                ->required()
                                ->placeholder('0XXX XX XX XX'),
                            Forms\Components\TextInput::make('whatsapp')
                                ->label('WhatsApp')
                                ->tel()
                                ->placeholder('0XXX XX XX XX'),
                        ]),
                    Forms\Components\TextInput::make('email_contact')
                        ->label('Email de contact')
                        ->email()
                        ->placeholder('contact@monagence.com'),
                    Forms\Components\TextInput::make('address')
                        ->label('Adresse')
                        ->maxLength(255)
                        ->placeholder('Rue, quartier...'),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('city')
                                ->label('Ville')
                                ->maxLength(100),
                            Forms\Components\Select::make('wilaya')
                                ->label('Wilaya')
                                ->options(config('resadz.wilayas', []))
                                ->searchable()
                                ->required(),
                        ]),
                ]),

            // ── Chauffeur toggle section ──
            Forms\Components\Section::make('Services de chauffeur')
                ->description('En plus de la location, vous pouvez proposer des services de transport.')
                ->schema([
                    Forms\Components\Toggle::make('offers_transfer')
                        ->label('Je propose aussi mes services de chauffeur 🧑‍✈️')
                        ->helperText('Activez pour proposer des transferts et courses en plus de la location')
                        ->live()
                        ->afterStateUpdated(fn (Forms\Set $set, bool $state) => $state ? null : $set('offers_delivery', false)),
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\Checkbox::make('offers_airport_transfer')
                                ->label('Transferts aéroport/gare')
                                ->default(false),
                            Forms\Components\Checkbox::make('offers_city_rides')
                                ->label('Courses en ville')
                                ->default(false),
                            Forms\Components\Checkbox::make('offers_delivery')
                                ->label('Livraisons')
                                ->default(false),
                        ])
                        ->visible(fn (Forms\Get $get) => (bool) $get('offers_transfer')),
                ]),
        ];
    }

    // ─── STEP 2: ZONES ─────────────────────────────────────────────
    protected function getZonesSchema(): array
    {
        return [
            Forms\Components\Section::make('Zones de livraison')
                ->description('Définissez les zones où vous pouvez livrer et récupérer les véhicules. Ajoutez au moins une zone active pour recevoir des réservations.')
                ->schema([
                    Forms\Components\Repeater::make('delivery_zones')
                        ->label('')
                        ->schema([
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Nom de la zone')
                                        ->required()
                                        ->placeholder('Ex: Alger Centre'),
                                    Forms\Components\Select::make('type')
                                        ->label('Type')
                                        ->options([
                                            'city' => 'Ville',
                                            'airport' => 'Aéroport',
                                            'station' => 'Gare',
                                            'hotel' => 'Hôtel',
                                        ])
                                        ->default('city'),
                                    Forms\Components\TextInput::make('delivery_fee')
                                        ->label('Frais de livraison (DA)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->default(0)
                                        ->helperText('0 = Gratuit'),
                                ]),
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('city')
                                        ->label('Ville')
                                        ->placeholder('Ex: Alger'),
                                    Forms\Components\Toggle::make('is_active')
                                        ->label('Zone active')
                                        ->default(true)
                                        ->inline(false),
                                ]),
                        ])
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Nouvelle zone')
                        ->collapsible()
                        ->cloneable()
                        ->defaultItems(0)
                        ->addActionLabel('Ajouter une zone de livraison')
                        ->reorderableWithButtons(),
                ]),
        ];
    }

    // ─── STEP 3: RESERVATIONS + PAYPAL ──────────────────────────────
    protected function getReservationsSchema(): array
    {
        return [
            Forms\Components\Section::make('Paramètres de réservation')
                ->description('Configurez comment vos clients peuvent réserver et payer. L\'acompte sécurise la réservation et vous protège contre les annulations.')
                ->schema([
                    Forms\Components\TextInput::make('advance_percentage')
                        ->label('Pourcentage d\'acompte')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('%')
                        ->helperText('Pourcentage du montant total à payer à l\'avance (0 = pas d\'acompte)'),
                    Forms\Components\Repeater::make('advance_payment_methods')
                        ->label('Méthodes de paiement de l\'acompte')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('method')
                                        ->label('Méthode')
                                        ->options([
                                            'cash' => 'Espèces',
                                            'cib' => 'CIB',
                                            'dahabia' => 'Dahabia',
                                            'baridimob' => 'BaridiMob',
                                            'paypal' => 'PayPal',
                                            'bank_transfer' => 'Virement bancaire',
                                        ])
                                        ->required()
                                        ->live(),
                                    Forms\Components\TextInput::make('timer_hours')
                                        ->label('Délai (heures)')
                                        ->numeric()
                                        ->default(24)
                                        ->helperText('Temps accordé pour payer'),
                                ]),
                            // PayPal email — visible only when method = paypal
                            Forms\Components\TextInput::make('paypal_email_inline')
                                ->label('Votre email PayPal')
                                ->email()
                                ->placeholder('exemple@paypal.com')
                                ->helperText('Vos clients pourront payer leur acompte directement sur votre compte PayPal. Assurez-vous que cet email est bien lié à votre compte PayPal actif.')
                                ->visible(fn (Forms\Get $get) => ($get('method') ?? '') === 'paypal')
                                ->required(fn (Forms\Get $get) => ($get('method') ?? '') === 'paypal'),
                        ])
                        ->collapsible()
                        ->defaultItems(0)
                        ->addActionLabel('Ajouter une méthode'),
                    Forms\Components\TextInput::make('cancellation_deadline_hours')
                        ->label('Délai d\'annulation gratuite')
                        ->numeric()
                        ->suffix('heures')
                        ->helperText('Combien d\'heures avant le début de la location le client peut annuler sans frais'),
                    Forms\Components\Toggle::make('auto_confirm_bookings')
                        ->label('Confirmer automatiquement les réservations')
                        ->helperText('Si activé, les réservations sont confirmées sans votre validation manuelle'),
                    Forms\Components\Toggle::make('require_documents')
                        ->label('Demander les documents (CNI, permis)')
                        ->helperText('Demander au client de télécharger ses documents avant la prise en charge'),
                ]),
        ];
    }

    // ─── STEP 4: OPTIONS ────────────────────────────────────────────
    protected function getOptionsSchema(): array
    {
        return [
            Forms\Components\Section::make('Options et services')
                ->description('Proposez des options supplémentaires à vos clients (siège bébé, GPS, conducteur additionnel...). Vous pouvez les offrir gratuitement ou les facturer.')
                ->schema([
                    Forms\Components\TextInput::make('return_margin_hours')
                        ->label('Marge horaire pour le retour')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(12)
                        ->suffix('heures')
                        ->helperText('0 = retour à l\'heure exacte, sinon temps accordé après l\'heure prévue'),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('fuel_return_fee')
                                ->label('Frais retour sans plein')
                                ->numeric()
                                ->minValue(0)
                                ->suffix('DA')
                                ->helperText('0 = option désactivée'),
                            Forms\Components\TextInput::make('wash_return_fee')
                                ->label('Frais retour sans lavage')
                                ->numeric()
                                ->minValue(0)
                                ->suffix('DA')
                                ->helperText('0 = option désactivée'),
                        ]),
                    Forms\Components\Repeater::make('rental_options')
                        ->label('Options de location')
                        ->schema([
                            Forms\Components\Grid::make(4)
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Nom')
                                        ->required()
                                        ->placeholder('Ex: Siège bébé'),
                                    Forms\Components\TextInput::make('price')
                                        ->label('Prix')
                                        ->numeric()
                                        ->minValue(0)
                                        ->suffix('DA')
                                        ->placeholder('0 = offert'),
                                    Forms\Components\Select::make('per')
                                        ->label('Facturation')
                                        ->options([
                                            'day' => 'Par jour',
                                            'booking' => 'Par location',
                                        ])
                                        ->default('day'),
                                    Forms\Components\TextInput::make('quantity')
                                        ->label('Quantité')
                                        ->numeric()
                                        ->minValue(1)
                                        ->default(1),
                                ]),
                            Forms\Components\Textarea::make('description')
                                ->label('Description')
                                ->rows(1)
                                ->placeholder('Description courte de l\'option'),
                        ])
                        ->collapsible()
                        ->defaultItems(0)
                        ->addActionLabel('Ajouter une option'),
                ]),
        ];
    }

    // ─── STEP 5: CONDITIONS ─────────────────────────────────────────
    protected function getConditionsSchema(): array
    {
        return [
            Forms\Components\Section::make('Conditions de location')
                ->description('Cochez les conditions qui s\'appliquent à votre agence. Elles seront affichées aux clients lors de la réservation.')
                ->schema([
                    // ── Conducteur ──
                    Forms\Components\Fieldset::make('Conducteur')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Toggle::make('cond_min_age')
                                    ->label('Âge minimum requis')
                                    ->live(),
                                Forms\Components\TextInput::make('cond_min_age_value')
                                    ->label('Âge minimum')
                                    ->numeric()
                                    ->suffix('ans')
                                    ->default(21)
                                    ->visible(fn (Forms\Get $get) => (bool) $get('cond_min_age')),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Toggle::make('cond_min_license_years')
                                    ->label('Ancienneté permis requise')
                                    ->live(),
                                Forms\Components\TextInput::make('cond_min_license_years_value')
                                    ->label('Permis depuis')
                                    ->numeric()
                                    ->suffix('ans')
                                    ->default(2)
                                    ->visible(fn (Forms\Get $get) => (bool) $get('cond_min_license_years')),
                            ]),
                            Forms\Components\Toggle::make('cond_id_required')
                                ->label('Pièce d\'identité obligatoire (CNI ou passeport)'),
                        ]),

                    // ── Véhicule ──
                    Forms\Components\Fieldset::make('Véhicule')
                        ->schema([
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\Toggle::make('cond_km_limit')
                                    ->label('Limite de kilométrage journalier')
                                    ->live()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('cond_km_limit_value')
                                    ->label('Km/jour max')
                                    ->numeric()
                                    ->suffix('km')
                                    ->default(250)
                                    ->visible(fn (Forms\Get $get) => (bool) $get('cond_km_limit'))
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('cond_km_extra_fee')
                                    ->label('Supplément par km')
                                    ->numeric()
                                    ->suffix('DA/km')
                                    ->default(15)
                                    ->visible(fn (Forms\Get $get) => (bool) $get('cond_km_limit'))
                                    ->columnSpan(1),
                            ]),
                            Forms\Components\Toggle::make('cond_fuel_full')
                                ->label('Véhicule rendu avec le plein de carburant'),
                            Forms\Components\Toggle::make('cond_clean_return')
                                ->label('Véhicule rendu propre (intérieur et extérieur)'),
                            Forms\Components\Toggle::make('cond_no_offroad')
                                ->label('Interdit de rouler hors route / pistes'),
                        ]),

                    // ── Caution & Finances ──
                    Forms\Components\Fieldset::make('Caution')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Toggle::make('cond_caution')
                                    ->label('Caution exigée à la prise du véhicule')
                                    ->live(),
                                Forms\Components\TextInput::make('cond_caution_value')
                                    ->label('Montant de la caution')
                                    ->numeric()
                                    ->suffix('DA')
                                    ->default(50000)
                                    ->visible(fn (Forms\Get $get) => (bool) $get('cond_caution')),
                            ]),
                        ]),

                    // ── Règles générales ──
                    Forms\Components\Fieldset::make('Règles générales')
                        ->schema([
                            Forms\Components\Toggle::make('cond_no_smoking')
                                ->label('Interdit de fumer dans le véhicule'),
                            Forms\Components\Toggle::make('cond_no_pets')
                                ->label('Animaux non autorisés dans le véhicule'),
                            Forms\Components\Toggle::make('cond_algeria_only')
                                ->label('Circulation autorisée uniquement en Algérie'),
                            Forms\Components\Toggle::make('cond_no_subletting')
                                ->label('Sous-location interdite (seul le locataire peut conduire)'),
                        ]),
                ]),

            // ── Conditions personnalisées + PDF ──
            Forms\Components\Section::make('Conditions supplémentaires')
                ->description('Ajoutez des conditions spécifiques ou uploadez votre document PDF.')
                ->collapsed()
                ->schema([
                    Forms\Components\FileUpload::make('conditions_pdf')
                        ->label('Document PDF des conditions générales')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(5120)
                        ->disk('public')
                        ->directory('conditions-pdf')
                        ->downloadable()
                        ->openable()
                        ->previewable(false)
                        ->helperText('Optionnel : téléchargez vos CGV au format PDF'),
                    Forms\Components\Repeater::make('rental_conditions')
                        ->label('Conditions personnalisées')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Titre')
                                ->required()
                                ->placeholder('Ex: Chaînes à neige obligatoires en hiver'),
                            Forms\Components\Textarea::make('description')
                                ->label('Description')
                                ->rows(2)
                                ->required()
                                ->placeholder('Détaillez cette condition...'),
                        ])
                        ->collapsible()
                        ->defaultItems(0)
                        ->addActionLabel('Ajouter une condition personnalisée'),
                ]),
        ];
    }

    // ─── STEP 6: BADGES ────────────────────────────────────────────
    protected function getBadgesSchema(): array
    {
        return [
            Forms\Components\Section::make('Badges et avantages')
                ->description('Les badges s\'affichent sur vos véhicules et attirent l\'attention des clients. Activez uniquement les badges qui correspondent vraiment à vos services.')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('badge_insurance')
                                ->label('Assurance incluse')
                                ->helperText('L\'assurance est comprise dans le prix'),
                            Forms\Components\Toggle::make('badge_delivery')
                                ->label('Livraison offerte')
                                ->helperText('Livraison gratuite dans certaines zones'),
                            Forms\Components\Toggle::make('badge_degressive')
                                ->label('Prix dégressif')
                                ->helperText('Réduction pour les longues durées'),
                            Forms\Components\Toggle::make('badge_airport')
                                ->label('Livraison aéroport')
                                ->helperText('Livraison disponible à l\'aéroport'),
                            Forms\Components\Toggle::make('badge_km_unlimited')
                                ->label('Kilométrage illimité')
                                ->helperText('Pas de limite de kilomètres'),
                        ]),
                    Forms\Components\Repeater::make('custom_badges')
                        ->label('Badges personnalisés')
                        ->schema([
                            Forms\Components\TextInput::make('text')
                                ->label('Texte du badge')
                                ->maxLength(50)
                                ->placeholder('Ex: Service 24h/24'),
                        ])
                        ->collapsible()
                        ->maxItems(3)
                        ->defaultItems(0)
                        ->addActionLabel('Ajouter un badge'),
                ]),
        ];
    }

    // ─── STEP 7: NOTIFICATIONS ──────────────────────────────────────
    protected function getNotificationsSchema(): array
    {
        return [
            Forms\Components\Section::make('Notifications')
                ->description('Choisissez comment vous souhaitez être alerté lors d\'une nouvelle réservation. Nous vous recommandons d\'activer au moins une méthode.')
                ->schema([
                    Forms\Components\Toggle::make('notify_email')
                        ->label('Notifications par email')
                        ->helperText('Recevez un email à chaque nouvelle réservation'),
                    Forms\Components\Toggle::make('notify_push')
                        ->label('Notifications push (navigateur)')
                        ->helperText('Notifications instantanées sur votre ordinateur/mobile'),
                    Forms\Components\Toggle::make('notify_whatsapp')
                        ->label('Notifications WhatsApp')
                        ->helperText('Recevez un message WhatsApp (si configuré)'),
                ]),
        ];
    }

    // ─── STEP INFO ──────────────────────────────────────────────────
    public function getStepInfo(): array
    {
        return [
            0 => [
                'title' => 'CGU & Contrat',
                'description' => 'Conditions d\'utilisation',
                'icon' => 'heroicon-o-shield-check',
            ],
            1 => [
                'title' => 'Profil',
                'description' => 'Présentez votre agence',
                'icon' => 'heroicon-o-building-storefront',
            ],
            2 => [
                'title' => 'Zones',
                'description' => 'Où livrez-vous ?',
                'icon' => 'heroicon-o-map-pin',
            ],
            3 => [
                'title' => 'Paiements',
                'description' => 'Acompte et méthodes',
                'icon' => 'heroicon-o-credit-card',
            ],
            4 => [
                'title' => 'Options',
                'description' => 'Extras et services',
                'icon' => 'heroicon-o-sparkles',
            ],
            5 => [
                'title' => 'Conditions',
                'description' => 'Règles de location',
                'icon' => 'heroicon-o-document-text',
            ],
            6 => [
                'title' => 'Badges',
                'description' => 'Vos avantages',
                'icon' => 'heroicon-o-trophy',
            ],
            7 => [
                'title' => 'Notifications',
                'description' => 'Restez connecté',
                'icon' => 'heroicon-o-bell',
            ],
        ];
    }

    // ─── NAVIGATION ─────────────────────────────────────────────────
    public function previousStep()
    {
        if ($this->currentStep > 0) {
            $loueur = Auth::user()->loueur;
            $loueur->update(['onboarding_step' => max(0, $this->currentStep - 1)]);
        }

        return redirect()->route('filament.loueur.pages.onboarding');
    }

    public function acceptCguAndContinue(): void
    {
        if (!$this->cguAccepted || !$this->contratAccepted) {
            Notification::make()
                ->title('Validation requise')
                ->body('Vous devez accepter les CGU et le Contrat de Partenariat pour continuer.')
                ->danger()
                ->send();
            return;
        }

        $loueur = Auth::user()->loueur;

        $loueur->setSetting('cgu_accepted_at', now()->toISOString(), 'string');
        $loueur->setSetting('cgu_version', '1.0', 'string');
        $loueur->setSetting('contrat_loueur_accepted_at', now()->toISOString(), 'string');

        $loueur->update(['onboarding_step' => 1]);

        $this->redirect(route('filament.loueur.pages.onboarding'));
    }

    protected function validateCurrentStep(Loueur $loueur, array $data): ?string
    {
        return match ($this->currentStep) {
            1 => $this->validateProfileStep($data),
            2 => $this->validateZonesStep($loueur),
            3 => $this->validateReservationsStep($data),
            default => null,
        };
    }

    protected function validateProfileStep(array $data): ?string
    {
        if (empty($data['company_name'])) {
            return 'Le nom de l\'agence est obligatoire.';
        }
        if (empty($data['phone'])) {
            return 'Le numéro de téléphone est obligatoire.';
        }
        if (empty($data['wilaya'])) {
            return 'La wilaya est obligatoire.';
        }
        return null;
    }

    protected function validateZonesStep(Loueur $loueur): ?string
    {
        $zones = $this->data['delivery_zones'] ?? [];
        $activeZones = collect($zones)->where('is_active', true)->count();

        if (empty($zones)) {
            return 'Vous devez créer au moins une zone de livraison.';
        }

        if ($activeZones === 0) {
            return 'Vous devez avoir au moins une zone de livraison active.';
        }

        return null;
    }

    protected function saveDeliveryZones(Loueur $loueur, array $zones): void
    {
        $existingIds = [];
        $sortOrder = 0;

        foreach ($zones as $zoneData) {
            if (empty($zoneData['name'])) {
                continue;
            }

            $sortOrder++;
            $deliveryFee = is_numeric($zoneData['delivery_fee'] ?? null) ? $zoneData['delivery_fee'] : 0;

            $zoneAttributes = [
                'loueur_id' => $loueur->id,
                'name' => $zoneData['name'],
                'type' => !empty($zoneData['type']) ? $zoneData['type'] : 'city',
                'city' => $zoneData['city'] ?? null,
                'wilaya' => $loueur->wilaya ?? null,
                'delivery_fee' => $deliveryFee,
                'return_fee' => $deliveryFee,
                'currency' => 'DZD',
                'is_active' => (bool) ($zoneData['is_active'] ?? true),
                'delivery_available' => true,
                'return_available' => true,
                'sort_order' => $sortOrder,
            ];

            if (!empty($zoneData['id'])) {
                $zone = DeliveryZone::where('id', $zoneData['id'])
                    ->where('loueur_id', $loueur->id)
                    ->first();

                if ($zone) {
                    $zone->update($zoneAttributes);
                    $existingIds[] = $zone->id;
                } else {
                    unset($zoneAttributes['loueur_id']);
                    $zone = $loueur->deliveryZones()->create($zoneAttributes);
                    $existingIds[] = $zone->id;
                }
            } else {
                unset($zoneAttributes['loueur_id']);
                $zone = $loueur->deliveryZones()->create($zoneAttributes);
                $existingIds[] = $zone->id;
            }
        }

        $loueur->deliveryZones()
            ->whereNotIn('id', $existingIds)
            ->delete();
    }

    protected function validateReservationsStep(array $data): ?string
    {
        $advancePercentage = $data['advance_percentage'] ?? 0;
        $paymentMethods = $data['advance_payment_methods'] ?? [];

        if ($advancePercentage > 0 && empty($paymentMethods)) {
            return 'Vous avez défini un acompte de ' . $advancePercentage . '%. Veuillez ajouter au moins une méthode de paiement.';
        }

        // Validate PayPal email if PayPal is selected
        foreach ($paymentMethods as $pm) {
            if (($pm['method'] ?? '') === 'paypal') {
                $email = $pm['paypal_email_inline'] ?? '';
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return 'Veuillez renseigner un email PayPal valide pour la méthode PayPal.';
                }
            }
        }

        return null;
    }

    public function nextStep()
    {
        // Step 0 is handled by acceptCguAndContinue()
        if ($this->currentStep === 0) {
            $this->acceptCguAndContinue();
            return;
        }

        $this->form->validate();

        $loueur = Auth::user()->loueur;
        $data = $this->data;

        $validationError = $this->validateCurrentStep($loueur, $data);
        if ($validationError) {
            Notification::make()
                ->title('Validation requise')
                ->body($validationError)
                ->danger()
                ->send();
            return;
        }

        // Save current step data
        switch ($this->currentStep) {
            case 1:
                $updateData = [
                    'company_name' => $data['company_name'] ?? $loueur->company_name,
                    'description' => $data['description'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'whatsapp' => $data['whatsapp'] ?? null,
                    'email_contact' => $data['email_contact'] ?? null,
                    'address' => $data['address'] ?? null,
                    'city' => $data['city'] ?? null,
                    'wilaya' => $data['wilaya'] ?? null,
                    'offers_transfer' => (bool) ($data['offers_transfer'] ?? false),
                    'offers_delivery' => (bool) ($data['offers_delivery'] ?? false),
                ];
                $loueur->update($updateData);
                $loueur->setSetting('offers_city_rides', (bool) ($data['offers_city_rides'] ?? false), 'boolean');
                break;

            case 2:
                $this->saveDeliveryZones($loueur, $data['delivery_zones'] ?? []);
                break;

            case 3:
                $loueur->setSetting('advance_percentage', $data['advance_percentage'] ?? 0, 'integer');
                $loueur->setSetting('advance_payment_methods', $data['advance_payment_methods'] ?? [], 'json');
                $loueur->setSetting('cancellation_deadline_hours', $data['cancellation_deadline_hours'] ?? 48, 'integer');
                $loueur->setSetting('auto_confirm_bookings', $data['auto_confirm_bookings'] ?? false, 'boolean');
                $loueur->setSetting('require_documents', $data['require_documents'] ?? true, 'boolean');

                // Extract PayPal email from payment methods and save to loueur
                $paypalEmail = null;
                foreach ($data['advance_payment_methods'] ?? [] as $pm) {
                    if (($pm['method'] ?? '') === 'paypal' && !empty($pm['paypal_email_inline'])) {
                        $paypalEmail = $pm['paypal_email_inline'];
                        break;
                    }
                }
                $loueur->update(['paypal_email' => $paypalEmail]);
                break;

            case 4:
                $loueur->setSetting('return_margin_hours', $data['return_margin_hours'] ?? 2, 'integer');
                $loueur->setSetting('fuel_return_fee', $data['fuel_return_fee'] ?? 0, 'decimal');
                $loueur->setSetting('wash_return_fee', $data['wash_return_fee'] ?? 0, 'decimal');
                $loueur->setSetting('rental_options', $data['rental_options'] ?? [], 'json');
                break;

            case 5:
                // Predefined conditions
                $predefinedKeys = [
                    'cond_no_smoking', 'cond_km_limit', 'cond_min_age',
                    'cond_min_license_years', 'cond_caution', 'cond_fuel_full',
                    'cond_no_pets', 'cond_no_offroad', 'cond_algeria_only',
                    'cond_clean_return', 'cond_id_required', 'cond_no_subletting',
                ];
                foreach ($predefinedKeys as $key) {
                    $loueur->setSetting($key, (bool) ($data[$key] ?? false), 'boolean');
                }
                $loueur->setSetting('cond_km_limit_value', $data['cond_km_limit_value'] ?? '250', 'string');
                $loueur->setSetting('cond_km_extra_fee', $data['cond_km_extra_fee'] ?? '15', 'string');
                $loueur->setSetting('cond_min_age_value', $data['cond_min_age_value'] ?? '21', 'string');
                $loueur->setSetting('cond_min_license_years_value', $data['cond_min_license_years_value'] ?? '2', 'string');
                $loueur->setSetting('cond_caution_value', $data['cond_caution_value'] ?? '50000', 'string');

                // PDF + custom conditions
                $conditionsPdf = $data['conditions_pdf'] ?? null;
                if (is_array($conditionsPdf)) {
                    $conditionsPdf = !empty($conditionsPdf) ? reset($conditionsPdf) : null;
                }
                $loueur->setSetting('conditions_pdf', $conditionsPdf ?: null, 'string');
                $loueur->setSetting('rental_conditions', $data['rental_conditions'] ?? [], 'json');
                break;

            case 6:
                $loueur->setSetting('badge_insurance', $data['badge_insurance'] ?? false, 'boolean');
                $loueur->setSetting('badge_delivery', $data['badge_delivery'] ?? false, 'boolean');
                $loueur->setSetting('badge_degressive', $data['badge_degressive'] ?? false, 'boolean');
                $loueur->setSetting('badge_airport', $data['badge_airport'] ?? false, 'boolean');
                $loueur->setSetting('badge_km_unlimited', $data['badge_km_unlimited'] ?? false, 'boolean');
                $loueur->setSetting('custom_badges', $data['custom_badges'] ?? [], 'json');
                break;

            case 7:
                $loueur->setSetting('notify_push', $data['notify_push'] ?? true, 'boolean');
                $loueur->setSetting('notify_whatsapp', $data['notify_whatsapp'] ?? true, 'boolean');
                $loueur->setSetting('notify_email', $data['notify_email'] ?? true, 'boolean');
                break;
        }

        // Update progress
        if ($this->currentStep < $this->totalSteps - 1) {
            $loueur->update(['onboarding_step' => $this->currentStep + 1]);
        }

        return redirect()->route('filament.loueur.pages.onboarding');
    }

    public function completeOnboarding()
    {
        $this->form->validate();

        $loueur = Auth::user()->loueur;
        $data = $this->data;

        // Save notifications settings
        $loueur->setSetting('notify_push', $data['notify_push'] ?? true, 'boolean');
        $loueur->setSetting('notify_whatsapp', $data['notify_whatsapp'] ?? true, 'boolean');
        $loueur->setSetting('notify_email', $data['notify_email'] ?? true, 'boolean');

        $loueur->completeOnboarding();

        Notification::make()
            ->title('Configuration terminée !')
            ->body('Votre espace est maintenant prêt. Vous pouvez ajouter vos véhicules.')
            ->success()
            ->send();

        return redirect()->route('filament.loueur.pages.dashboard');
    }

    public function skipOnboarding()
    {
        $loueur = Auth::user()->loueur;
        $loueur->completeOnboarding();

        Notification::make()
            ->title('Configuration ignorée')
            ->body('Vous pourrez configurer votre agence plus tard dans les paramètres.')
            ->warning()
            ->send();

        return redirect()->route('filament.loueur.pages.dashboard');
    }
}

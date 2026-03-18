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
    public int $currentStep = 1;
    public int $totalSteps = 7;

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;
        return $loueur && !$loueur->hasCompletedOnboarding();
    }

    public function mount(): void
    {
        $loueur = Auth::user()->loueur;

        if (!$loueur) {
            redirect()->route('filament.loueur.pages.dashboard');
            return;
        }

        // Set current step from saved progress
        $this->currentStep = max(1, min($loueur->onboarding_step + 1, $this->totalSteps));

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
            'cancellation_deadline_hours' => $loueur->getSetting('cancellation_deadline_hours', 48),
            'auto_confirm_bookings' => $loueur->getSetting('auto_confirm_bookings', false),
            'require_documents' => $loueur->getSetting('require_documents', true),

            // Step 4: Options
            'return_margin_hours' => $loueur->getSetting('return_margin_hours', 2),
            'fuel_return_fee' => $loueur->getSetting('fuel_return_fee', 0),
            'wash_return_fee' => $loueur->getSetting('wash_return_fee', 0),
            'rental_options' => $loueur->getSetting('rental_options', []),

            // Step 5: Conditions
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
        ];
    }

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
                                        ->required(),
                                    Forms\Components\TextInput::make('timer_hours')
                                        ->label('Délai (heures)')
                                        ->numeric()
                                        ->default(24)
                                        ->helperText('Temps accordé pour payer'),
                                ]),
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

    protected function getOptionsSchema(): array
    {
        return [
            Forms\Components\Section::make('Options et services')
                ->description('Proposez des options supplémentaires à vos clients (siège bébé, GPS, conducteur additionnel...). Vous pouvez les offrir gratuitement ou les facturer.')
                ->schema([
                    Forms\Components\TextInput::make('return_margin_hours')
                        ->label('Marge horaire pour le retour')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(12)
                        ->suffix('heures')
                        ->helperText('Temps accordé après l\'heure de prise en charge pour le retour'),
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

    protected function getConditionsSchema(): array
    {
        return [
            Forms\Components\Section::make('Conditions de location')
                ->description('Informez vos clients des conditions importantes : âge minimum, kilométrage, documents requis... Ces informations s\'afficheront lors de la réservation.')
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
                        ->label('Conditions affichées')
                        ->schema([
                            Forms\Components\Select::make('title')
                                ->label('Type')
                                ->options([
                                    'Âge minimum' => 'Âge minimum',
                                    'Permis de conduire' => 'Permis de conduire',
                                    'Kilométrage' => 'Kilométrage',
                                    'Carburant' => 'Carburant',
                                    'Caution' => 'Caution',
                                    'Assurance' => 'Assurance',
                                    'Autre' => 'Autre (personnalisé)',
                                ])
                                ->required()
                                ->live(),
                            Forms\Components\TextInput::make('custom_title')
                                ->label('Titre personnalisé')
                                ->visible(fn (Forms\Get $get) => $get('title') === 'Autre')
                                ->required(fn (Forms\Get $get) => $get('title') === 'Autre'),
                            Forms\Components\Textarea::make('description')
                                ->label('Description')
                                ->rows(2)
                                ->required()
                                ->placeholder('Ex: Le conducteur doit avoir au moins 21 ans'),
                        ])
                        ->collapsible()
                        ->defaultItems(0)
                        ->addActionLabel('Ajouter une condition'),
                ]),
        ];
    }

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

    public function getStepInfo(): array
    {
        return [
            1 => [
                'title' => 'Profil de l\'agence',
                'description' => 'Présentez votre agence aux clients',
                'icon' => 'heroicon-o-building-storefront',
            ],
            2 => [
                'title' => 'Zones de livraison',
                'description' => 'Où pouvez-vous livrer ?',
                'icon' => 'heroicon-o-map-pin',
            ],
            3 => [
                'title' => 'Réservations',
                'description' => 'Acompte et paiements',
                'icon' => 'heroicon-o-calendar-days',
            ],
            4 => [
                'title' => 'Options',
                'description' => 'Services supplémentaires',
                'icon' => 'heroicon-o-squares-plus',
            ],
            5 => [
                'title' => 'Conditions',
                'description' => 'Règles de location',
                'icon' => 'heroicon-o-document-text',
            ],
            6 => [
                'title' => 'Badges',
                'description' => 'Vos avantages',
                'icon' => 'heroicon-o-star',
            ],
            7 => [
                'title' => 'Notifications',
                'description' => 'Comment être alerté',
                'icon' => 'heroicon-o-bell',
            ],
        ];
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $loueur = Auth::user()->loueur;
            $loueur->update(['onboarding_step' => $this->currentStep - 2]);
        }

        return redirect()->route('filament.loueur.pages.onboarding');
    }

    /**
     * Validate the current step before proceeding
     */
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
            $sortOrder++;

            $zoneAttributes = [
                'loueur_id' => $loueur->id,
                'name' => $zoneData['name'],
                'type' => $zoneData['type'] ?? 'city',
                'city' => $zoneData['city'] ?? null,
                'wilaya' => $loueur->wilaya,
                'delivery_fee' => $zoneData['delivery_fee'] ?? 0,
                'return_fee' => $zoneData['delivery_fee'] ?? 0,
                'currency' => 'DZD',
                'is_active' => $zoneData['is_active'] ?? true,
                'delivery_available' => true,
                'return_available' => true,
                'sort_order' => $sortOrder,
            ];

            if (!empty($zoneData['id'])) {
                // Update existing zone
                $zone = DeliveryZone::where('id', $zoneData['id'])
                    ->where('loueur_id', $loueur->id)
                    ->first();

                if ($zone) {
                    $zone->update($zoneAttributes);
                    $existingIds[] = $zone->id;
                }
            } else {
                // Create new zone
                $zone = DeliveryZone::create($zoneAttributes);
                $existingIds[] = $zone->id;
            }
        }

        // Delete zones that were removed
        $loueur->deliveryZones()
            ->whereNotIn('id', $existingIds)
            ->delete();
    }

    protected function validateReservationsStep(array $data): ?string
    {
        $advancePercentage = $data['advance_percentage'] ?? 0;
        $paymentMethods = $data['advance_payment_methods'] ?? [];

        // If advance payment is required, at least one payment method must be configured
        if ($advancePercentage > 0 && empty($paymentMethods)) {
            return 'Vous avez défini un acompte de ' . $advancePercentage . '%. Veuillez ajouter au moins une méthode de paiement.';
        }

        return null;
    }

    public function nextStep()
    {
        // Validate the current step form
        $this->form->validate();

        $loueur = Auth::user()->loueur;
        $data = $this->data;

        // Additional validation per step
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
                $loueur->update([
                    'company_name' => $data['company_name'] ?? $loueur->company_name,
                    'description' => $data['description'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'whatsapp' => $data['whatsapp'] ?? null,
                    'email_contact' => $data['email_contact'] ?? null,
                    'address' => $data['address'] ?? null,
                    'city' => $data['city'] ?? null,
                    'wilaya' => $data['wilaya'] ?? null,
                ]);
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
                break;

            case 4:
                $loueur->setSetting('return_margin_hours', $data['return_margin_hours'] ?? 2, 'integer');
                $loueur->setSetting('fuel_return_fee', $data['fuel_return_fee'] ?? 0, 'decimal');
                $loueur->setSetting('wash_return_fee', $data['wash_return_fee'] ?? 0, 'decimal');
                $loueur->setSetting('rental_options', $data['rental_options'] ?? [], 'json');
                break;

            case 5:
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

        // Update progress to next step
        if ($this->currentStep < $this->totalSteps) {
            $loueur->update(['onboarding_step' => $this->currentStep]);
        }

        return redirect()->route('filament.loueur.pages.onboarding');
    }

    protected function saveCurrentStep(): void
    {
        $loueur = Auth::user()->loueur;
        $data = $this->data;

        switch ($this->currentStep) {
            case 1:
                $loueur->update([
                    'company_name' => $data['company_name'] ?? $loueur->company_name,
                    'description' => $data['description'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'whatsapp' => $data['whatsapp'] ?? null,
                    'email_contact' => $data['email_contact'] ?? null,
                    'address' => $data['address'] ?? null,
                    'city' => $data['city'] ?? null,
                    'wilaya' => $data['wilaya'] ?? null,
                ]);
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
                break;

            case 4:
                $loueur->setSetting('return_margin_hours', $data['return_margin_hours'] ?? 2, 'integer');
                $loueur->setSetting('fuel_return_fee', $data['fuel_return_fee'] ?? 0, 'decimal');
                $loueur->setSetting('wash_return_fee', $data['wash_return_fee'] ?? 0, 'decimal');
                $loueur->setSetting('rental_options', $data['rental_options'] ?? [], 'json');
                break;

            case 5:
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
        if ($this->currentStep > $loueur->onboarding_step) {
            $loueur->update(['onboarding_step' => $this->currentStep]);
        }
    }

    public function completeOnboarding()
    {
        // Validate the final step
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

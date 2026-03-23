<?php

namespace App\Filament\Chauffeur\Pages;

use App\Models\Loueur;
use App\Models\TransferRoute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Onboarding extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Configuration Chauffeur';

    protected static ?string $title = 'Configurez votre espace chauffeur';

    protected static ?int $navigationSort = -1;

    protected static string $view = 'filament.chauffeur.pages.onboarding';

    protected static ?string $slug = 'onboarding';

    public ?array $data = [];
    public int $currentStep = 0;
    public int $totalSteps = 6;

    // CGU checkboxes
    public bool $cguAccepted = false;
    public bool $contratAccepted = false;

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;

        return $loueur
            && !$loueur->hasCompletedOnboarding()
            && $loueur->account_type === 'taxi';
    }

    public function mount(): void
    {
        $loueur = Auth::user()?->loueur;

        if (!$loueur) {
            $this->redirect(route('filament.chauffeur.pages.dashboard'));
            return;
        }

        if ($loueur->account_type === 'loueur') {
            $this->redirect(route('filament.loueur.pages.onboarding'));
            return;
        }

        if ($loueur->hasCompletedOnboarding()) {
            $this->redirect(route('filament.chauffeur.pages.dashboard'));
            return;
        }

        $this->currentStep = min($loueur->onboarding_step, $this->totalSteps - 1);

        $this->cguAccepted = !empty($loueur->getSetting('cgu_accepted_at'));
        $this->contratAccepted = !empty($loueur->getSetting('contrat_chauffeur_accepted_at'));

        $this->form->fill($this->loadStepData($loueur));
    }

    protected function loadStepData(Loueur $loueur): array
    {
        return [
            // Step 1: Profile
            'company_name' => $loueur->company_name,
            'phone' => $loueur->phone,
            'whatsapp' => $loueur->whatsapp,
            'wilaya' => $loueur->wilaya,
            'description' => $loueur->description,

            // Step 2: Vehicle
            'vehicle_type' => $loueur->getSetting('chauffeur_vehicle_type'),
            'max_passengers' => $loueur->getSetting('chauffeur_max_passengers', 4),
            'has_air_conditioning' => $loueur->getSetting('chauffeur_has_ac', true),
            'has_wifi' => $loueur->getSetting('chauffeur_has_wifi', false),
            'has_child_seat' => $loueur->getSetting('chauffeur_has_child_seat', false),
            'has_large_trunk' => $loueur->getSetting('chauffeur_has_large_trunk', false),

            // Step 3: Services & Tarifs
            'offers_transfer' => (bool) $loueur->offers_transfer,
            'offers_city_rides' => (bool) $loueur->getSetting('offers_city_rides', false),
            'offers_delivery' => (bool) $loueur->offers_delivery,
            'accepts_paypal' => !empty($loueur->paypal_email),
            'paypal_email' => $loueur->paypal_email,
            'transfer_routes' => $loueur->transferRoutes()->get()->map(fn ($r) => [
                'id' => $r->id,
                'departure' => $r->departure,
                'destination' => $r->destination,
                'price' => $r->price,
                'round_trip' => (bool) $r->round_trip,
                'round_trip_price' => $r->round_trip_price,
            ])->toArray(),

            // Step 4: Availabilities
            'availabilities' => $loueur->getSetting('chauffeur_availabilities', $this->getDefaultAvailabilities()),

            // Step 5: Notifications
            'notify_push' => $loueur->getSetting('notify_push', true),
            'notify_whatsapp' => $loueur->getSetting('notify_whatsapp', true),
            'notify_email' => $loueur->getSetting('notify_email', true),
        ];
    }

    protected function getDefaultAvailabilities(): array
    {
        $days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
        $result = [];
        foreach ($days as $day) {
            $result[$day] = [
                'active' => in_array($day, ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi']),
                'start' => '08:00',
                'end' => '18:00',
            ];
        }
        return $result;
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
            0 => [], // CGU handled in blade
            1 => $this->getProfileSchema(),
            2 => $this->getVehicleSchema(),
            3 => $this->getServicesSchema(),
            4 => $this->getAvailabilitiesSchema(),
            5 => $this->getNotificationsSchema(),
            default => [],
        };
    }

    // ─── STEP 1: PROFILE ────────────────────────────────────────────
    protected function getProfileSchema(): array
    {
        return [
            Forms\Components\Section::make('Vos informations')
                ->description('Ces informations seront visibles par vos clients.')
                ->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->label('Nom complet')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ex: Mohamed Benali'),
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
                    Forms\Components\Select::make('wilaya')
                        ->label('Wilaya principale')
                        ->options(config('resadz.wilayas', []))
                        ->searchable()
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->maxLength(500)
                        ->placeholder('Ex: Chauffeur professionnel avec 5 ans d\'expérience, véhicule climatisé...'),
                ]),
        ];
    }

    // ─── STEP 2: VEHICLE ────────────────────────────────────────────
    protected function getVehicleSchema(): array
    {
        return [
            Forms\Components\Section::make('Votre véhicule')
                ->description('Décrivez votre véhicule pour que les clients sachent à quoi s\'attendre.')
                ->schema([
                    Forms\Components\Radio::make('vehicle_type')
                        ->label('Type de véhicule')
                        ->options([
                            'berline' => 'Berline',
                            'suv' => 'SUV',
                            'van' => 'Van',
                            'minibus' => 'Minibus',
                        ])
                        ->columns(4)
                        ->required(),
                    Forms\Components\TextInput::make('max_passengers')
                        ->label('Nombre de places')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(30)
                        ->required()
                        ->default(4),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Checkbox::make('has_air_conditioning')
                                ->label('Climatisation')
                                ->default(true),
                            Forms\Components\Checkbox::make('has_wifi')
                                ->label('WiFi'),
                            Forms\Components\Checkbox::make('has_child_seat')
                                ->label('Siège bébé'),
                            Forms\Components\Checkbox::make('has_large_trunk')
                                ->label('Coffre spacieux'),
                        ]),
                ]),
        ];
    }

    // ─── STEP 3: SERVICES & TARIFS ──────────────────────────────────
    protected function getServicesSchema(): array
    {
        return [
            Forms\Components\Section::make('Types de services')
                ->description('Sélectionnez les services que vous proposez.')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\Checkbox::make('offers_transfer')
                                ->label('Transferts aéroport/gare')
                                ->live(),
                            Forms\Components\Checkbox::make('offers_city_rides')
                                ->label('Courses en ville'),
                            Forms\Components\Checkbox::make('offers_delivery')
                                ->label('Livraisons'),
                        ]),
                ]),

            Forms\Components\Section::make('Paiement PayPal')
                ->description('Permettez à vos clients de payer via PayPal.')
                ->schema([
                    Forms\Components\Toggle::make('accepts_paypal')
                        ->label('Acceptez-vous les paiements PayPal ?')
                        ->live(),
                    Forms\Components\TextInput::make('paypal_email')
                        ->label('Votre email PayPal')
                        ->email()
                        ->placeholder('exemple@paypal.com')
                        ->helperText('Vos clients pourront payer directement sur votre PayPal.')
                        ->visible(fn (Forms\Get $get) => (bool) $get('accepts_paypal'))
                        ->required(fn (Forms\Get $get) => (bool) $get('accepts_paypal')),
                ]),

            Forms\Components\Section::make('Trajets réguliers')
                ->description('Ajoutez vos trajets habituels pour apparaître dans les recherches.')
                ->visible(fn (Forms\Get $get) => (bool) $get('offers_transfer'))
                ->schema([
                    Forms\Components\Repeater::make('transfer_routes')
                        ->label('')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('departure')
                                        ->label('Départ')
                                        ->required()
                                        ->placeholder('Ex: Aéroport Houari Boumediene'),
                                    Forms\Components\TextInput::make('destination')
                                        ->label('Destination')
                                        ->required()
                                        ->placeholder('Ex: Alger Centre'),
                                ]),
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\TextInput::make('price')
                                        ->label('Prix (DA)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->required()
                                        ->suffix('DA'),
                                    Forms\Components\Toggle::make('round_trip')
                                        ->label('Aller-retour ?')
                                        ->live()
                                        ->inline(false),
                                    Forms\Components\TextInput::make('round_trip_price')
                                        ->label('Prix aller-retour (DA)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->suffix('DA')
                                        ->visible(fn (Forms\Get $get) => (bool) $get('round_trip')),
                                ]),
                        ])
                        ->itemLabel(fn (array $state): ?string => ($state['departure'] ?? '') . ' → ' . ($state['destination'] ?? ''))
                        ->collapsible()
                        ->defaultItems(0)
                        ->addActionLabel('Ajouter un trajet'),
                ]),
        ];
    }

    // ─── STEP 4: AVAILABILITIES ─────────────────────────────────────
    protected function getAvailabilitiesSchema(): array
    {
        $days = [
            'lundi' => 'Lundi',
            'mardi' => 'Mardi',
            'mercredi' => 'Mercredi',
            'jeudi' => 'Jeudi',
            'vendredi' => 'Vendredi',
            'samedi' => 'Samedi',
            'dimanche' => 'Dimanche',
        ];

        $dayFields = [];
        foreach ($days as $key => $label) {
            $dayFields[] = Forms\Components\Grid::make(3)
                ->schema([
                    Forms\Components\Toggle::make("availabilities.{$key}.active")
                        ->label($label)
                        ->live()
                        ->inline(false),
                    Forms\Components\TimePicker::make("availabilities.{$key}.start")
                        ->label('Début')
                        ->seconds(false)
                        ->visible(fn (Forms\Get $get) => (bool) $get("availabilities.{$key}.active")),
                    Forms\Components\TimePicker::make("availabilities.{$key}.end")
                        ->label('Fin')
                        ->seconds(false)
                        ->visible(fn (Forms\Get $get) => (bool) $get("availabilities.{$key}.active")),
                ]);
        }

        return [
            Forms\Components\Section::make('Vos disponibilités')
                ->description('Indiquez vos horaires habituels. Vous pourrez les ajuster plus tard.')
                ->schema($dayFields),
        ];
    }

    // ─── STEP 5: NOTIFICATIONS ──────────────────────────────────────
    protected function getNotificationsSchema(): array
    {
        return [
            Forms\Components\Section::make('Notifications')
                ->description('Choisissez comment être alerté lors d\'une nouvelle demande de course.')
                ->schema([
                    Forms\Components\Toggle::make('notify_email')
                        ->label('Notifications par email')
                        ->helperText('Recevez un email à chaque nouvelle demande'),
                    Forms\Components\Toggle::make('notify_push')
                        ->label('Notifications push (navigateur)')
                        ->helperText('Notifications instantanées sur votre appareil'),
                    Forms\Components\Toggle::make('notify_whatsapp')
                        ->label('Notifications WhatsApp')
                        ->helperText('Recevez un message WhatsApp (recommandé)'),
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
                'description' => 'Vos informations',
                'icon' => 'heroicon-o-user',
            ],
            2 => [
                'title' => 'Véhicule',
                'description' => 'Votre véhicule',
                'icon' => 'heroicon-o-truck',
            ],
            3 => [
                'title' => 'Services',
                'description' => 'Services & tarifs',
                'icon' => 'heroicon-o-map',
            ],
            4 => [
                'title' => 'Horaires',
                'description' => 'Disponibilités',
                'icon' => 'heroicon-o-clock',
            ],
            5 => [
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
            $loueur = Auth::user()?->loueur;
            if ($loueur) {
                $loueur->update(['onboarding_step' => max(0, $this->currentStep - 1)]);
            }
        }

        return redirect()->route('filament.chauffeur.pages.onboarding');
    }

    public function acceptCguAndContinue()
    {
        if (!$this->cguAccepted || !$this->contratAccepted) {
            Notification::make()
                ->title('Validation requise')
                ->body('Vous devez accepter les CGU et le Contrat de Partenariat pour continuer.')
                ->danger()
                ->send();
            return;
        }

        $loueur = Auth::user()?->loueur;

        if (!$loueur) {
            return redirect()->route('filament.chauffeur.auth.login');
        }

        $loueur->setSetting('cgu_accepted_at', now()->toISOString(), 'string');
        $loueur->setSetting('cgu_version', '1.0', 'string');
        $loueur->setSetting('contrat_chauffeur_accepted_at', now()->toISOString(), 'string');
        $loueur->setSetting('onboarding_type', 'chauffeur', 'string');

        $loueur->update(['onboarding_step' => 1]);

        return redirect()->route('filament.chauffeur.pages.onboarding');
    }

    protected function validateCurrentStep(Loueur $loueur, array $data): ?string
    {
        return match ($this->currentStep) {
            1 => $this->validateProfileStep($data),
            3 => $this->validateServicesStep($data),
            default => null,
        };
    }

    protected function validateProfileStep(array $data): ?string
    {
        if (empty($data['company_name'])) {
            return 'Le nom est obligatoire.';
        }
        if (empty($data['phone'])) {
            return 'Le numéro de téléphone est obligatoire.';
        }
        if (empty($data['wilaya'])) {
            return 'La wilaya est obligatoire.';
        }
        return null;
    }

    protected function validateServicesStep(array $data): ?string
    {
        if (($data['accepts_paypal'] ?? false) && empty($data['paypal_email'])) {
            return 'Veuillez renseigner votre email PayPal.';
        }
        if (($data['accepts_paypal'] ?? false) && !filter_var($data['paypal_email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            return 'L\'email PayPal n\'est pas valide.';
        }
        return null;
    }

    protected function saveTransferRoutes(Loueur $loueur, array $routes): void
    {
        $existingIds = [];

        foreach ($routes as $routeData) {
            $attrs = [
                'loueur_id' => $loueur->id,
                'departure' => $routeData['departure'],
                'destination' => $routeData['destination'],
                'price' => $routeData['price'] ?? 0,
                'round_trip' => (bool) ($routeData['round_trip'] ?? false),
                'round_trip_price' => $routeData['round_trip_price'] ?? null,
                'is_active' => true,
            ];

            if (!empty($routeData['id'])) {
                $route = TransferRoute::where('id', $routeData['id'])
                    ->where('loueur_id', $loueur->id)
                    ->first();

                if ($route) {
                    $route->update($attrs);
                    $existingIds[] = $route->id;
                }
            } else {
                $route = TransferRoute::create($attrs);
                $existingIds[] = $route->id;
            }
        }

        $loueur->transferRoutes()
            ->whereNotIn('id', $existingIds)
            ->delete();
    }

    public function nextStep()
    {
        if ($this->currentStep === 0) {
            return $this->acceptCguAndContinue();
        }

        $this->form->validate();

        $loueur = Auth::user()?->loueur;
        if (!$loueur) {
            return redirect()->route('filament.chauffeur.auth.login');
        }
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

        switch ($this->currentStep) {
            case 1:
                $loueur->update([
                    'company_name' => $data['company_name'] ?? $loueur->company_name,
                    'phone' => $data['phone'] ?? null,
                    'whatsapp' => $data['whatsapp'] ?? null,
                    'wilaya' => $data['wilaya'] ?? null,
                    'description' => $data['description'] ?? null,
                ]);
                break;

            case 2:
                $loueur->setSetting('chauffeur_vehicle_type', $data['vehicle_type'] ?? null, 'string');
                $loueur->setSetting('chauffeur_max_passengers', $data['max_passengers'] ?? 4, 'integer');
                $loueur->setSetting('chauffeur_has_ac', $data['has_air_conditioning'] ?? true, 'boolean');
                $loueur->setSetting('chauffeur_has_wifi', $data['has_wifi'] ?? false, 'boolean');
                $loueur->setSetting('chauffeur_has_child_seat', $data['has_child_seat'] ?? false, 'boolean');
                $loueur->setSetting('chauffeur_has_large_trunk', $data['has_large_trunk'] ?? false, 'boolean');
                break;

            case 3:
                $loueur->update([
                    'offers_transfer' => (bool) ($data['offers_transfer'] ?? false),
                    'offers_delivery' => (bool) ($data['offers_delivery'] ?? false),
                    'paypal_email' => ($data['accepts_paypal'] ?? false) ? ($data['paypal_email'] ?? null) : null,
                ]);
                $loueur->setSetting('offers_city_rides', (bool) ($data['offers_city_rides'] ?? false), 'boolean');

                if ($data['offers_transfer'] ?? false) {
                    $this->saveTransferRoutes($loueur, $data['transfer_routes'] ?? []);
                }
                break;

            case 4:
                $loueur->setSetting('chauffeur_availabilities', $data['availabilities'] ?? [], 'json');
                break;

            case 5:
                $loueur->setSetting('notify_push', $data['notify_push'] ?? true, 'boolean');
                $loueur->setSetting('notify_whatsapp', $data['notify_whatsapp'] ?? true, 'boolean');
                $loueur->setSetting('notify_email', $data['notify_email'] ?? true, 'boolean');
                break;
        }

        if ($this->currentStep < $this->totalSteps - 1) {
            $loueur->update(['onboarding_step' => $this->currentStep + 1]);
        }

        return redirect()->route('filament.chauffeur.pages.onboarding');
    }

    public function completeOnboarding()
    {
        $this->form->validate();

        $loueur = Auth::user()?->loueur;
        if (!$loueur) {
            return redirect()->route('filament.chauffeur.auth.login');
        }
        $data = $this->data;

        $loueur->setSetting('notify_push', $data['notify_push'] ?? true, 'boolean');
        $loueur->setSetting('notify_whatsapp', $data['notify_whatsapp'] ?? true, 'boolean');
        $loueur->setSetting('notify_email', $data['notify_email'] ?? true, 'boolean');

        $loueur->completeOnboarding();

        Notification::make()
            ->title('Configuration terminée !')
            ->body('Votre espace chauffeur est prêt. Vous pouvez commencer à recevoir des courses !')
            ->success()
            ->send();

        return redirect()->route('filament.chauffeur.pages.dashboard');
    }

    public function skipOnboarding()
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) {
            return redirect()->route('filament.chauffeur.auth.login');
        }
        $loueur->completeOnboarding();

        Notification::make()
            ->title('Configuration ignorée')
            ->body('Vous pourrez configurer votre espace plus tard dans les paramètres.')
            ->warning()
            ->send();

        return redirect()->route('filament.chauffeur.pages.dashboard');
    }
}

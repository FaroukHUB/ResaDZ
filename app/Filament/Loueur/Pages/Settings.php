<?php

namespace App\Filament\Loueur\Pages;

use App\Models\Loueur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Settings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $navigationLabel = 'Paramètres';

    protected static ?string $title = 'Paramètres';

    public function getTitle(): string
    {
        $loueur = Auth::user()->loueur;
        if ($loueur && $loueur->isTaxi()) {
            return 'Paramètres de mon activité';
        }
        return 'Paramètres de mon agence';
    }

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.loueur.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $loueur = Auth::user()->loueur;

        if ($loueur) {
            $this->form->fill([
                'company_name' => $loueur->company_name,
                'description' => $loueur->description,
                'phone' => $loueur->phone,
                'whatsapp' => $loueur->whatsapp,
                'email_contact' => $loueur->email_contact,
                'address' => $loueur->address,
                'city' => $loueur->city,
                'wilaya' => $loueur->wilaya,
                'facebook' => $loueur->facebook,
                'instagram' => $loueur->instagram,
                'tiktok' => $loueur->tiktok,
                'payment_methods' => $loueur->payment_methods ?? [],
                'paypal_email' => $loueur->paypal_email,
                'iban' => $loueur->iban,
                'wise_email' => $loueur->wise_email,
                'baridimob_rip' => $loueur->baridimob_rip,
                'meta_title' => $loueur->meta_title,
                'meta_description' => $loueur->meta_description,
                // Settings from loueur_settings table
                'advance_percentage' => $loueur->getSetting('advance_percentage', 30),
                'advance_payment_methods' => $loueur->getSetting('advance_payment_methods', []),
                'cancellation_deadline_hours' => $loueur->getSetting('cancellation_deadline_hours', 48),
                'auto_confirm_bookings' => $loueur->getSetting('auto_confirm_bookings', false),
                'require_documents' => $loueur->getSetting('require_documents', true),
                'notify_push' => $loueur->getSetting('notify_push', true),
                'notify_whatsapp' => $loueur->getSetting('notify_whatsapp', true),
                'notify_email' => $loueur->getSetting('notify_email', true),
                // Predefined conditions
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
                // Conditions PDF + custom
                'conditions_pdf' => $loueur->getSetting('conditions_pdf', null),
                'rental_conditions' => $loueur->getSetting('rental_conditions', []),
                // Return options
                'return_margin_hours' => $loueur->getSetting('return_margin_hours', 2),
                'fuel_return_fee' => $loueur->getSetting('fuel_return_fee', 0),
                'wash_return_fee' => $loueur->getSetting('wash_return_fee', 0),
                // Services
                'offers_transfer' => $loueur->offers_transfer,
                'offers_delivery' => $loueur->offers_delivery,
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Paramètres')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Profil')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->label('Nom de l\'agence')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->helperText('Présentez votre agence en quelques phrases'),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('city')
                                            ->label('Ville'),
                                        Forms\Components\TextInput::make('wilaya')
                                            ->label('Wilaya'),
                                    ]),
                                Forms\Components\TextInput::make('address')
                                    ->label('Adresse'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Téléphone')
                                            ->tel(),
                                        Forms\Components\TextInput::make('whatsapp')
                                            ->label('WhatsApp'),
                                    ]),
                                Forms\Components\TextInput::make('email_contact')
                                    ->label('Email de contact')
                                    ->email(),
                                Forms\Components\Section::make('Réseaux sociaux')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('facebook')
                                                    ->label('Facebook')
                                                    ->url()
                                                    ->placeholder('https://facebook.com/...'),
                                                Forms\Components\TextInput::make('instagram')
                                                    ->label('Instagram')
                                                    ->placeholder('@votrecompte'),
                                                Forms\Components\TextInput::make('tiktok')
                                                    ->label('TikTok')
                                                    ->placeholder('@votrecompte'),
                                            ]),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Paiements')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\CheckboxList::make('payment_methods')
                                    ->label('Méthodes de paiement acceptées')
                                    ->options([
                                        'cash' => 'Espèces',
                                        'cib' => 'CIB',
                                        'dahabia' => 'Dahabia',
                                        'baridimob' => 'BaridiMob',
                                        'paypal' => 'PayPal',
                                        'bank_transfer' => 'Virement bancaire',
                                        'wise' => 'Wise',
                                    ])
                                    ->columns(3),
                                Forms\Components\Section::make('Détails des méthodes')
                                    ->schema([
                                        Forms\Components\TextInput::make('paypal_email')
                                            ->label('Email PayPal'),
                                        Forms\Components\TextInput::make('iban')
                                            ->label('IBAN (virement)'),
                                        Forms\Components\TextInput::make('wise_email')
                                            ->label('Email Wise'),
                                        Forms\Components\TextInput::make('baridimob_rip')
                                            ->label('RIP BaridiMob'),
                                    ])
                                    ->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Réservations')
                            ->icon('heroicon-o-calendar')
                            ->visible(fn () => Auth::user()->loueur?->isLoueur())
                            ->schema([
                                Forms\Components\Section::make('Acompte')
                                    ->description('Configurez le montant de l\'acompte demandé au client')
                                    ->schema([
                                        Forms\Components\TextInput::make('advance_percentage')
                                            ->label('Pourcentage d\'acompte')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('%')
                                            ->helperText('Ex: 30 = 30% du total. 0 = pas d\'acompte. L\'acompte sera calculé automatiquement en DA ou € selon la devise de la réservation.'),
                                    ]),
                                Forms\Components\Section::make('Méthodes de paiement de l\'acompte')
                                    ->description('Pour chaque méthode acceptée, définissez le délai accordé au client. Si le délai est dépassé, la réservation est automatiquement annulée.')
                                    ->schema([
                                        Forms\Components\Repeater::make('advance_payment_methods')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\Select::make('method')
                                                    ->label('Méthode de paiement')
                                                    ->options([
                                                        'cash' => 'Espèces (sur place)',
                                                        'cib' => 'CIB (carte bancaire)',
                                                        'dahabia' => 'Dahabia',
                                                        'baridimob' => 'BaridiMob',
                                                        'paypal' => 'PayPal',
                                                        'bank_transfer' => 'Virement bancaire',
                                                    ])
                                                    ->required()
                                                    ->distinct(),
                                                Forms\Components\TextInput::make('timer_hours')
                                                    ->label('Délai accordé (heures)')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->maxValue(168)
                                                    ->required()
                                                    ->suffix('h')
                                                    ->helperText('Ex: 4 = le client a 4h pour payer'),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Ajouter une méthode')
                                            ->reorderable(false)
                                            ->helperText('Exemples : Espèces → 4h, PayPal → 24h. Si aucune méthode n\'est configurée, pas de timer ni de choix de méthode pour le client.'),
                                    ]),
                                Forms\Components\Section::make('Délais')
                                    ->schema([
                                        Forms\Components\TextInput::make('cancellation_deadline_hours')
                                            ->label('Délai d\'annulation (heures)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->helperText('Heures avant le début de la location où l\'annulation est possible'),
                                    ]),
                                Forms\Components\Section::make('Options')
                                    ->schema([
                                        Forms\Components\Toggle::make('auto_confirm_bookings')
                                            ->label('Confirmer automatiquement les réservations')
                                            ->helperText('Les réservations seront confirmées sans intervention manuelle'),
                                        Forms\Components\Toggle::make('require_documents')
                                            ->label('Exiger les documents du client')
                                            ->helperText('Pièce d\'identité et permis de conduire'),
                                    ]),
                                Forms\Components\Section::make('Options de retour')
                                    ->description('Configurez les options et frais liés au retour du véhicule')
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
                                                    ->helperText('Frais si le client rend le véhicule sans le plein (0 = option désactivée)'),
                                                Forms\Components\TextInput::make('wash_return_fee')
                                                    ->label('Frais retour sans lavage')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->suffix('DA')
                                                    ->helperText('Frais si le client rend le véhicule non lavé (0 = option désactivée)'),
                                            ]),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Notifications')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                Forms\Components\Toggle::make('notify_push')
                                    ->label('Notifications Push')
                                    ->helperText('Recevoir des notifications instantanées sur votre navigateur'),
                                Forms\Components\Toggle::make('notify_email')
                                    ->label('Notifications Email')
                                    ->helperText('Recevoir les notifications de réservation par email'),
                                Forms\Components\Toggle::make('notify_whatsapp')
                                    ->label('Notifications WhatsApp')
                                    ->helperText('Recevoir les notifications de réservation sur WhatsApp (bientôt disponible)')
                                    ->disabled(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Conditions')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->visible(fn () => Auth::user()->loueur?->isLoueur())
                            ->schema([
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

                                        // ── Caution ──
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
                                            ->helperText('Format PDF uniquement, 5 Mo maximum.'),
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
                            ]),
                        Forms\Components\Tabs\Tab::make('Synchronisation')
                            ->icon('heroicon-o-arrow-path')
                            ->visible(fn () => Auth::user()->loueur?->isLoueur())
                            ->schema([
                                Forms\Components\Section::make('Synchronisation Google Agenda')
                                    ->description('Synchronisez vos réservations et blocages avec Google Agenda, Apple Calendar ou Outlook.')
                                    ->schema([
                                        Forms\Components\Placeholder::make('ical_instructions')
                                            ->label('Comment synchroniser')
                                            ->content('Pour synchroniser votre calendrier ResaDZ avec Google Agenda :

1. Copiez le lien iCal ci-dessous
2. Ouvrez Google Agenda (calendar.google.com)
3. Cliquez sur "+" à côté de "Autres agendas"
4. Sélectionnez "À partir de l\'URL"
5. Collez le lien et cliquez sur "Ajouter un agenda"

Le calendrier sera automatiquement mis à jour toutes les quelques heures.'),
                                        Forms\Components\TextInput::make('ical_url_display')
                                            ->label('Votre lien iCal')
                                            ->default(function () {
                                                $loueur = Auth::user()->loueur;
                                                if (!$loueur) return 'Non disponible';
                                                $token = \App\Http\Controllers\Api\CalendarController::generateToken($loueur->id);
                                                return url('/calendar/ical/' . $token . '.ics');
                                            })
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->helperText('Copiez ce lien et collez-le dans Google Agenda'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Services')
                            ->icon('heroicon-o-squares-plus')
                            ->schema([
                                Forms\Components\Section::make('Services proposés')
                                    ->description(function () {
                                        $loueur = Auth::user()->loueur;
                                        if ($loueur && $loueur->isTaxi()) {
                                            return 'Activez les services que vous proposez. Gérez vos trajets et tarifs depuis les menus Transferts et Livraison.';
                                        }
                                        return 'En plus de la location, vous pouvez proposer un service de transfert. Gérez vos trajets depuis le menu Transferts.';
                                    })
                                    ->schema([
                                        Forms\Components\Toggle::make('offers_transfer')
                                            ->label('Service de transfert')
                                            ->helperText('Proposez des trajets vers les aéroports, gares et villes. Gérez vos trajets et tarifs dans le menu "Transferts".')
                                            ->visible(fn () => Auth::user()->loueur?->isLoueur()),
                                        Forms\Components\Toggle::make('offers_delivery')
                                            ->label('Service de livraison')
                                            ->helperText('Proposez la livraison de colis, documents et repas. Gérez vos tarifs dans le menu "Livraison".')
                                            ->visible(fn () => Auth::user()->loueur?->isTaxi()),
                                        Forms\Components\Placeholder::make('taxi_transfer_info')
                                            ->label('')
                                            ->content('Le service de transfert est activé par défaut pour les comptes Taxi/VTC.')
                                            ->visible(fn () => Auth::user()->loueur?->isTaxi()),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Titre SEO')
                                    ->maxLength(60)
                                    ->helperText('Titre affiché dans les moteurs de recherche (max 60 caractères)'),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Description SEO')
                                    ->rows(2)
                                    ->maxLength(160)
                                    ->helperText('Description affichée dans les résultats de recherche (max 160 caractères)'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Sécurité')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Forms\Components\Section::make('Mot de passe')
                                    ->description(function () {
                                        $user = Auth::user();
                                        if (empty($user->password)) {
                                            return 'Vous vous êtes inscrit via Google. Définissez un mot de passe pour pouvoir aussi vous connecter avec votre email.';
                                        }
                                        return 'Modifiez votre mot de passe de connexion.';
                                    })
                                    ->schema([
                                        Forms\Components\TextInput::make('current_password')
                                            ->label('Mot de passe actuel')
                                            ->password()
                                            ->revealable()
                                            ->visible(fn () => !empty(Auth::user()->password))
                                            ->helperText('Requis pour modifier votre mot de passe'),
                                        Forms\Components\TextInput::make('new_password')
                                            ->label('Nouveau mot de passe')
                                            ->password()
                                            ->revealable()
                                            ->minLength(8)
                                            ->helperText('Minimum 8 caractères'),
                                        Forms\Components\TextInput::make('new_password_confirmation')
                                            ->label('Confirmer le nouveau mot de passe')
                                            ->password()
                                            ->revealable()
                                            ->same('new_password'),
                                    ]),
                                Forms\Components\Section::make('Connexion')
                                    ->schema([
                                        Forms\Components\Placeholder::make('login_methods')
                                            ->label('Méthodes de connexion disponibles')
                                            ->content(function () {
                                                $user = Auth::user();
                                                $methods = [];
                                                if (!empty($user->password)) {
                                                    $methods[] = '✓ Email + Mot de passe';
                                                }
                                                $methods[] = '✓ Google (toujours disponible)';
                                                return implode("\n", $methods);
                                            }),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $loueur = Auth::user()->loueur;

        if (!$loueur) {
            Notification::make()
                ->title('Erreur')
                ->body('Aucune agence associée à votre compte.')
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();

        // Update Loueur model
        $loueur->update([
            'company_name' => $data['company_name'],
            'description' => $data['description'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'email_contact' => $data['email_contact'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'wilaya' => $data['wilaya'] ?? null,
            'facebook' => $data['facebook'] ?? null,
            'instagram' => $data['instagram'] ?? null,
            'tiktok' => $data['tiktok'] ?? null,
            'payment_methods' => $data['payment_methods'] ?? [],
            'paypal_email' => $data['paypal_email'] ?? null,
            'iban' => $data['iban'] ?? null,
            'wise_email' => $data['wise_email'] ?? null,
            'baridimob_rip' => $data['baridimob_rip'] ?? null,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ]);

        // Update settings
        $loueur->setSetting('advance_percentage', $data['advance_percentage'] ?? 0, 'integer');
        $loueur->setSetting('advance_payment_methods', $data['advance_payment_methods'] ?? [], 'json');
        $loueur->setSetting('cancellation_deadline_hours', $data['cancellation_deadline_hours'] ?? 48, 'integer');
        $loueur->setSetting('auto_confirm_bookings', $data['auto_confirm_bookings'] ?? false, 'boolean');
        $loueur->setSetting('require_documents', $data['require_documents'] ?? true, 'boolean');
        $loueur->setSetting('notify_push', $data['notify_push'] ?? true, 'boolean');
        $loueur->setSetting('notify_whatsapp', $data['notify_whatsapp'] ?? true, 'boolean');
        $loueur->setSetting('notify_email', $data['notify_email'] ?? true, 'boolean');


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

        // Conditions PDF + custom
        $loueur->setSetting('conditions_pdf', $data['conditions_pdf'] ?? null, 'string');
        $loueur->setSetting('rental_conditions', $data['rental_conditions'] ?? [], 'json');

        // Return options
        $loueur->setSetting('return_margin_hours', $data['return_margin_hours'] ?? 2, 'integer');
        $loueur->setSetting('fuel_return_fee', $data['fuel_return_fee'] ?? 0, 'decimal');
        $loueur->setSetting('wash_return_fee', $data['wash_return_fee'] ?? 0, 'decimal');


        // Services
        if ($loueur->isLoueur()) {
            $loueur->update(['offers_transfer' => $data['offers_transfer'] ?? false]);
        }
        if ($loueur->isTaxi()) {
            $loueur->update(['offers_delivery' => $data['offers_delivery'] ?? false]);
        }

        // Handle password update
        $user = Auth::user();
        if (!empty($data['new_password'])) {
            // If user has a password, verify current password
            if (!empty($user->password)) {
                if (empty($data['current_password']) || !Hash::check($data['current_password'], $user->password)) {
                    Notification::make()
                        ->title('Erreur')
                        ->body('Le mot de passe actuel est incorrect.')
                        ->danger()
                        ->send();
                    return;
                }
            }

            // Update password
            $user->password = Hash::make($data['new_password']);
            $user->save();

            Notification::make()
                ->title('Mot de passe mis à jour')
                ->body('Votre mot de passe a été défini avec succès. Vous pouvez maintenant vous connecter avec votre email et mot de passe.')
                ->success()
                ->send();
        }

        Notification::make()
            ->title('Paramètres enregistrés')
            ->body('Vos paramètres ont été mis à jour avec succès.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer les paramètres')
                ->submit('save'),
        ];
    }
}

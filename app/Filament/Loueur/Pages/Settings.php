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
                'reservation_timer_hours' => $loueur->getSetting('reservation_timer_hours', 24),
                'advance_percentage' => $loueur->getSetting('advance_percentage', 30),
                'min_advance_amount' => $loueur->getSetting('min_advance_amount', 0),
                'cancellation_deadline_hours' => $loueur->getSetting('cancellation_deadline_hours', 48),
                'auto_confirm_bookings' => $loueur->getSetting('auto_confirm_bookings', false),
                'require_documents' => $loueur->getSetting('require_documents', true),
                'notify_push' => $loueur->getSetting('notify_push', true),
                'notify_whatsapp' => $loueur->getSetting('notify_whatsapp', true),
                'notify_email' => $loueur->getSetting('notify_email', true),
                // Badges
                'badge_insurance' => $loueur->getSetting('badge_insurance', false),
                'badge_delivery' => $loueur->getSetting('badge_delivery', false),
                'badge_degressive' => $loueur->getSetting('badge_degressive', false),
                'badge_airport' => $loueur->getSetting('badge_airport', false),
                'badge_km_unlimited' => $loueur->getSetting('badge_km_unlimited', false),
                'custom_badges' => $loueur->getSetting('custom_badges', []),
                // Conditions
                'conditions_pdf' => $loueur->getSetting('conditions_pdf', null),
                'rental_conditions' => $loueur->getSetting('rental_conditions', []),
                // Return options
                'return_margin_hours' => $loueur->getSetting('return_margin_hours', 2),
                'fuel_return_fee' => $loueur->getSetting('fuel_return_fee', 0),
                'wash_return_fee' => $loueur->getSetting('wash_return_fee', 0),
                // Deposit settings
                'deposit_required' => $loueur->getSetting('deposit_required', false),
                'deposit_payment_methods' => $loueur->getSetting('deposit_payment_methods', []),
                // Rental options (siège bébé, GPS, etc.)
                'rental_options' => $loueur->getSetting('rental_options', []),
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
                                Forms\Components\Section::make('Délais et acomptes')
                                    ->description('Configurez les règles de réservation')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('reservation_timer_hours')
                                                    ->label('Délai de confirmation (heures)')
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->helperText('Temps accordé au client pour confirmer sa réservation'),
                                                Forms\Components\TextInput::make('cancellation_deadline_hours')
                                                    ->label('Délai d\'annulation (heures)')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->helperText('Heures avant le début de la location'),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('advance_percentage')
                                                    ->label('Pourcentage d\'acompte')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->suffix('%')
                                                    ->helperText('Ex: 30 = 30% du total'),
                                                Forms\Components\TextInput::make('min_advance_amount')
                                                    ->label('Acompte minimum (DA)')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->suffix('DA')
                                                    ->helperText('0 = pas de minimum'),
                                            ]),
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
                                            ->minValue(1)
                                            ->maxValue(12)
                                            ->suffix('heures')
                                            ->helperText('Temps accordé après l\'heure de prise pour le retour (ex: 2h = retour avant 12h si prise à 10h)'),
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
                                Forms\Components\Section::make('Acompte en ligne')
                                    ->description('Permettre aux clients de régler un acompte pour garantir leur réservation')
                                    ->schema([
                                        Forms\Components\Toggle::make('deposit_required')
                                            ->label('Proposer le paiement d\'un acompte')
                                            ->helperText('Le client pourra payer un acompte pour confirmer sa réservation')
                                            ->live(),
                                        Forms\Components\CheckboxList::make('deposit_payment_methods')
                                            ->label('Méthodes de paiement acceptées pour l\'acompte')
                                            ->options([
                                                'paypal' => 'PayPal',
                                                'cash' => 'Espèces (sur place)',
                                            ])
                                            ->columns(2)
                                            ->visible(fn ($get) => $get('deposit_required'))
                                            ->helperText('Le client pourra choisir parmi ces méthodes'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Options')
                            ->icon('heroicon-o-squares-plus')
                            ->visible(fn () => Auth::user()->loueur?->isLoueur())
                            ->schema([
                                Forms\Components\Section::make('Options de location')
                                    ->description('Configurez les options payantes ou offertes que vous proposez à vos clients (siège bébé, GPS, chauffeur, etc.)')
                                    ->schema([
                                        Forms\Components\Repeater::make('rental_options')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\Grid::make(4)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Nom de l\'option')
                                                            ->required()
                                                            ->maxLength(100)
                                                            ->placeholder('Ex: Siège bébé'),
                                                        Forms\Components\TextInput::make('price')
                                                            ->label('Prix')
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->suffix('DA')
                                                            ->placeholder('0 = offert')
                                                            ->helperText('Laisser 0 pour offrir'),
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
                                                            ->default(1)
                                                            ->helperText('Stock disponible'),
                                                    ]),
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('image')
                                                            ->label('Photo (optionnel)')
                                                            ->image()
                                                            ->directory('rental-options')
                                                            ->maxSize(2048)
                                                            ->helperText('Image de l\'option (max 2 Mo)'),
                                                        Forms\Components\Textarea::make('description')
                                                            ->label('Description (optionnel)')
                                                            ->rows(2)
                                                            ->placeholder('Description courte de l\'option...'),
                                                    ]),
                                                Forms\Components\Toggle::make('is_free')
                                                    ->label('Option offerte')
                                                    ->helperText('Affiche un badge "Offert" à côté de cette option')
                                                    ->live(),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('Ajouter une option')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                ($state['name'] ?? 'Nouvelle option') .
                                                (isset($state['quantity']) && $state['quantity'] > 1 ? ' (x' . $state['quantity'] . ')' : '') .
                                                (($state['is_free'] ?? false) ? ' - Offert' : (isset($state['price']) && $state['price'] > 0 ? ' - ' . number_format($state['price'], 0, ',', ' ') . ' DA' : ''))
                                            ),
                                    ]),
                                Forms\Components\Section::make('Exemples d\'options')
                                    ->description('Voici quelques idées d\'options à proposer :')
                                    ->schema([
                                        Forms\Components\Placeholder::make('examples')
                                            ->label('')
                                            ->content('
                                                • **Siège bébé** : 500 DA/jour
                                                • **GPS** : 1 000 DA/jour ou offert
                                                • **Chauffeur** : 3 000 DA/jour
                                                • **Wifi portable** : 800 DA/jour
                                                • **Assurance tous risques** : 2 000 DA/jour
                                                • **Kilométrage illimité** : 1 500 DA/location (offert)
                                            ')
                                            ->extraAttributes(['class' => 'text-sm text-gray-600']),
                                    ])
                                    ->collapsed(),
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
                        Forms\Components\Tabs\Tab::make('Badges')
                            ->icon('heroicon-o-tag')
                            ->visible(fn () => Auth::user()->loueur?->isLoueur())
                            ->schema([
                                Forms\Components\Section::make('Badges des cartes véhicules')
                                    ->description('Sélectionnez les badges à afficher sur vos cartes véhicules. Ces badges apparaissent sur le marketplace pour rassurer les clients.')
                                    ->schema([
                                        Forms\Components\Toggle::make('badge_insurance')
                                            ->label('Assurance incluse')
                                            ->helperText('Affiche "Assurance incluse" sur vos véhicules'),
                                        Forms\Components\Toggle::make('badge_delivery')
                                            ->label('Livraison offerte')
                                            ->helperText('Affiche "Livraison offerte" sur vos véhicules'),
                                        Forms\Components\Toggle::make('badge_degressive')
                                            ->label('Prix dégressif')
                                            ->helperText('Affiche "Prix dégressif selon la durée" sur vos véhicules'),
                                        Forms\Components\Toggle::make('badge_airport')
                                            ->label('Livraison aéroport')
                                            ->helperText('Affiche "Livraison aéroport" sur vos véhicules'),
                                        Forms\Components\Toggle::make('badge_km_unlimited')
                                            ->label('Kilométrage illimité')
                                            ->helperText('Affiche "Kilométrage illimité" sur vos véhicules'),
                                    ]),
                                Forms\Components\Section::make('Badges personnalisés')
                                    ->description('Ajoutez vos propres badges (max 3)')
                                    ->schema([
                                        Forms\Components\Repeater::make('custom_badges')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\TextInput::make('text')
                                                    ->label('Texte du badge')
                                                    ->required()
                                                    ->maxLength(50)
                                                    ->placeholder('Ex: GPS inclus'),
                                            ])
                                            ->maxItems(3)
                                            ->defaultItems(0)
                                            ->addActionLabel('Ajouter un badge'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Conditions')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->visible(fn () => Auth::user()->loueur?->isLoueur())
                            ->schema([
                                Forms\Components\Section::make('Conditions de location (PDF)')
                                    ->description('Si vous disposez déjà d\'un document PDF avec vos conditions, uploadez-le ici. Il sera téléchargeable par les clients.')
                                    ->schema([
                                        Forms\Components\FileUpload::make('conditions_pdf')
                                            ->label('Document PDF des conditions')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->maxSize(5120)
                                            ->directory('conditions-pdf')
                                            ->visibility('public')
                                            ->downloadable()
                                            ->openable()
                                            ->helperText('Format PDF uniquement, 5 Mo maximum.'),
                                    ]),
                                Forms\Components\Section::make('Conditions de location')
                                    ->description('Définissez vos conditions de location. Ces informations seront affichées aux clients sur la page de détail du véhicule.')
                                    ->schema([
                                        Forms\Components\Repeater::make('rental_conditions')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\Select::make('title')
                                                    ->label('Titre de la condition')
                                                    ->options([
                                                        'Âge minimum' => 'Âge minimum',
                                                        'Permis de conduire' => 'Permis de conduire',
                                                        'Caution' => 'Caution',
                                                        'Documents requis' => 'Documents requis',
                                                        'Carburant' => 'Carburant',
                                                        'Kilométrage' => 'Kilométrage',
                                                        'Livraison' => 'Livraison',
                                                        'Annulation' => 'Annulation',
                                                        'Assurance' => 'Assurance',
                                                        'Pénalités' => 'Pénalités',
                                                        'Horaires' => 'Horaires',
                                                        'Zone de circulation' => 'Zone de circulation',
                                                        'Autre' => 'Autre (personnalisé)',
                                                    ])
                                                    ->required()
                                                    ->searchable()
                                                    ->live(),
                                                Forms\Components\TextInput::make('custom_title')
                                                    ->label('Titre personnalisé')
                                                    ->visible(fn ($get) => $get('title') === 'Autre')
                                                    ->required(fn ($get) => $get('title') === 'Autre')
                                                    ->maxLength(100),
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Description')
                                                    ->required()
                                                    ->rows(2)
                                                    ->placeholder(fn ($get) => match($get('title')) {
                                                        'Âge minimum' => 'Ex: Le conducteur doit avoir au minimum 21 ans.',
                                                        'Permis de conduire' => 'Ex: Permis de conduire valide depuis au moins 2 ans.',
                                                        'Caution' => 'Ex: Caution de 50 000 DA exigée à la prise du véhicule.',
                                                        'Documents requis' => 'Ex: Carte d\'identité + Permis de conduire + Justificatif de domicile.',
                                                        'Carburant' => 'Ex: Véhicule remis avec le plein, à rendre avec le plein.',
                                                        'Kilométrage' => 'Ex: 200 km/jour inclus. Supplément de 15 DA/km au-delà.',
                                                        'Livraison' => 'Ex: Livraison gratuite à Alger centre. Frais supplémentaires hors zone.',
                                                        'Annulation' => 'Ex: Annulation gratuite jusqu\'à 48h avant. 50% de l\'acompte retenu après.',
                                                        'Assurance' => 'Ex: Assurance tous risques incluse. Franchise de 20 000 DA.',
                                                        'Pénalités' => 'Ex: Retard de retour : 2 000 DA par heure supplémentaire.',
                                                        'Horaires' => 'Ex: Prise et retour du véhicule de 8h à 20h.',
                                                        'Zone de circulation' => 'Ex: Circulation autorisée uniquement en Algérie.',
                                                        default => 'Décrivez cette condition...',
                                                    }),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('Ajouter une condition')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                $state['title'] === 'Autre'
                                                    ? ($state['custom_title'] ?? 'Condition personnalisée')
                                                    : ($state['title'] ?? 'Nouvelle condition')
                                            ),
                                    ]),
                                Forms\Components\Section::make('Exemple de conditions')
                                    ->description('Voici quelques suggestions pour vous aider à rédiger vos conditions :')
                                    ->schema([
                                        Forms\Components\Placeholder::make('examples')
                                            ->label('')
                                            ->content('
                                                • **Âge minimum** : Le conducteur doit avoir au minimum 21 ans (25 ans pour les véhicules haut de gamme).
                                                • **Permis** : Permis de conduire valide depuis au moins 2 ans.
                                                • **Caution** : Caution de 30 000 à 100 000 DA selon le véhicule, restituée au retour.
                                                • **Documents** : CNI + Permis de conduire + Justificatif de domicile de moins de 3 mois.
                                                • **Carburant** : Véhicule remis avec le plein, à rendre avec le plein (sinon surfacturation).
                                                • **Kilométrage** : 200 km/jour inclus. Au-delà : 15 DA/km supplémentaire.
                                            ')
                                            ->extraAttributes(['class' => 'text-sm text-gray-600']),
                                    ])
                                    ->collapsed(),
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
        $loueur->setSetting('reservation_timer_hours', $data['reservation_timer_hours'] ?? 24, 'integer');
        $loueur->setSetting('advance_percentage', $data['advance_percentage'] ?? 30, 'integer');
        $loueur->setSetting('min_advance_amount', $data['min_advance_amount'] ?? 0, 'decimal');
        $loueur->setSetting('cancellation_deadline_hours', $data['cancellation_deadline_hours'] ?? 48, 'integer');
        $loueur->setSetting('auto_confirm_bookings', $data['auto_confirm_bookings'] ?? false, 'boolean');
        $loueur->setSetting('require_documents', $data['require_documents'] ?? true, 'boolean');
        $loueur->setSetting('notify_push', $data['notify_push'] ?? true, 'boolean');
        $loueur->setSetting('notify_whatsapp', $data['notify_whatsapp'] ?? true, 'boolean');
        $loueur->setSetting('notify_email', $data['notify_email'] ?? true, 'boolean');

        // Badges
        $loueur->setSetting('badge_insurance', $data['badge_insurance'] ?? false, 'boolean');
        $loueur->setSetting('badge_delivery', $data['badge_delivery'] ?? false, 'boolean');
        $loueur->setSetting('badge_degressive', $data['badge_degressive'] ?? false, 'boolean');
        $loueur->setSetting('badge_airport', $data['badge_airport'] ?? false, 'boolean');
        $loueur->setSetting('badge_km_unlimited', $data['badge_km_unlimited'] ?? false, 'boolean');
        $loueur->setSetting('custom_badges', $data['custom_badges'] ?? [], 'json');

        // Conditions
        $loueur->setSetting('conditions_pdf', $data['conditions_pdf'] ?? null, 'string');
        $loueur->setSetting('rental_conditions', $data['rental_conditions'] ?? [], 'json');

        // Return options
        $loueur->setSetting('return_margin_hours', $data['return_margin_hours'] ?? 2, 'integer');
        $loueur->setSetting('fuel_return_fee', $data['fuel_return_fee'] ?? 0, 'decimal');
        $loueur->setSetting('wash_return_fee', $data['wash_return_fee'] ?? 0, 'decimal');

        // Deposit settings
        $loueur->setSetting('deposit_required', $data['deposit_required'] ?? false, 'boolean');
        $loueur->setSetting('deposit_payment_methods', $data['deposit_payment_methods'] ?? [], 'json');

        // Rental options
        $loueur->setSetting('rental_options', $data['rental_options'] ?? [], 'json');

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

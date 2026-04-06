<?php

namespace App\Filament\Loueur\Resources;

use App\Filament\Loueur\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\DeliveryZone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmedMail;
use App\Mail\ReviewRequestMail;
use App\Models\Review;
use Closure;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Réservations';

    protected static ?string $navigationLabel = 'Mes Réservations';

    protected static ?string $modelLabel = 'Réservation';

    protected static ?string $pluralModelLabel = 'Réservations';

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
            ->orderBy('created_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        $loueur = Auth::user()->loueur;

        return $form
            ->schema([
                Forms\Components\Tabs::make('Réservation')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informations')
                            ->icon('heroicon-o-information-circle')
                            ->badge('Principal')
                            ->schema([
                                Forms\Components\Placeholder::make('info_help')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString('
                                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg text-sm text-blue-800 dark:text-blue-200">
                                            <strong>💡 Informations principales</strong> — Ces données définissent la réservation. Le statut contrôle l\'affichage côté client et les notifications automatiques.
                                        </div>
                                    ')),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('reference')
                                            ->label('Référence')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->helperText('Générée automatiquement — communiquez-la au client'),
                                        Forms\Components\Select::make('status')
                                            ->label('Statut')
                                            ->options([
                                                'pending' => 'En attente',
                                                'confirmed' => 'Confirmée',
                                                'active' => 'En cours',
                                                'completed' => 'Terminée',
                                                'cancelled' => 'Annulée',
                                                'expired' => 'Expirée',
                                            ])
                                            ->required()
                                            ->helperText('Confirmée = client notifié, En cours = véhicule bloqué'),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('vehicle_id')
                                            ->label('Véhicule')
                                            ->options(fn () => $loueur
                                                ? Vehicle::where('loueur_id', $loueur->id)->pluck('full_name', 'id')
                                                : []
                                            )
                                            ->searchable()
                                            ->required()
                                            ->helperText('Le véhicule sera marqué indisponible sur ces dates'),
                                        Forms\Components\Select::make('currency')
                                            ->label('Devise')
                                            ->options([
                                                'DZD' => 'Dinar (DA)',
                                                'EUR' => 'Euro (€)',
                                            ])
                                            ->default('DZD')
                                            ->helperText('Devise utilisée pour cette réservation'),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('start_date')
                                            ->label('Date de début')
                                            ->required()
                                            ->live()
                                            ->helperText('Date et heure de remise du véhicule'),
                                        Forms\Components\DateTimePicker::make('end_date')
                                            ->label('Date de fin')
                                            ->required()
                                            ->live()
                                            ->helperText('Date et heure de retour prévue')
                                            ->rules([
                                                fn (Forms\Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                                    $vehicleId = $get('vehicle_id');
                                                    $startDate = $get('start_date');
                                                    if (!$vehicleId || !$startDate || !$value) return;

                                                    $vehicle = Vehicle::find($vehicleId);
                                                    if ($vehicle && !$vehicle->isAvailableForDates($startDate, $value)) {
                                                        $fail('Ce véhicule n\'est pas disponible pour ces dates (dates bloquées ou déjà réservées).');
                                                    }
                                                },
                                            ]),
                                    ]),
                                Forms\Components\TextInput::make('total_days')
                                    ->label('Nombre de jours')
                                    ->numeric()
                                    ->minValue(1)
                                    ->helperText('Calculé automatiquement mais modifiable'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Client')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Placeholder::make('client_help')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString('
                                        <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-200">
                                            <strong>👤 Informations client</strong> — L\'email est utilisé pour les notifications automatiques (confirmation, rappel, demande d\'avis). Le WhatsApp permet d\'envoyer des messages rapides.
                                        </div>
                                    ')),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('client_name')
                                            ->label('Nom du client')
                                            ->required()
                                            ->helperText('Nom complet tel qu\'il apparaît sur le permis'),
                                        Forms\Components\TextInput::make('client_phone')
                                            ->label('Téléphone')
                                            ->tel()
                                            ->helperText('Numéro principal pour vous contacter'),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('client_email')
                                            ->label('Email')
                                            ->email()
                                            ->helperText('Important ! Sert pour les confirmations et demandes d\'avis'),
                                        Forms\Components\TextInput::make('client_whatsapp')
                                            ->label('WhatsApp')
                                            ->helperText('Format international : +213 xxx... pour messages WhatsApp'),
                                    ]),
                                Forms\Components\Section::make('Documents')
                                    ->description('Conservez les documents du client pour votre protection juridique. Ils ne sont jamais partagés publiquement.')
                                    ->schema([
                                        Forms\Components\FileUpload::make('client_id_document')
                                            ->label('Pièce d\'identité')
                                            ->directory('bookings/documents')
                                            ->visibility('private')
                                            ->helperText('CNI ou passeport — obligatoire pour le contrat'),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\FileUpload::make('client_license_front')
                                                    ->label('Permis (recto)')
                                                    ->directory('bookings/documents')
                                                    ->visibility('private')
                                                    ->helperText('Face avec la photo'),
                                                Forms\Components\FileUpload::make('client_license_back')
                                                    ->label('Permis (verso)')
                                                    ->directory('bookings/documents')
                                                    ->visibility('private')
                                                    ->helperText('Face avec les infos'),
                                            ]),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Livraison')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Forms\Components\Placeholder::make('delivery_help')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString('
                                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg text-sm text-amber-800 dark:text-amber-200">
                                            <strong>🚚 Livraison</strong> — Définissez où le client récupère et rend le véhicule. Les frais de livraison sont calculés selon vos zones configurées dans "Zones de livraison".
                                        </div>
                                    ')),
                                Forms\Components\Section::make('Récupération')
                                    ->description('Où le client récupère le véhicule')
                                    ->schema([
                                        Forms\Components\Select::make('pickup_zone_id')
                                            ->label('Zone de récupération')
                                            ->options(fn () => $loueur
                                                ? DeliveryZone::where('loueur_id', $loueur->id)->pluck('name', 'id')
                                                : []
                                            )
                                            ->helperText('Zone prédéfinie — les frais seront appliqués automatiquement'),
                                        Forms\Components\TextInput::make('custom_pickup_location')
                                            ->label('📍 Lieu personnalisé demandé par le client')
                                            ->disabled()
                                            ->visible(fn ($record) => !empty($record?->custom_pickup_location))
                                            ->extraAttributes(['class' => 'bg-amber-50 border-amber-300 font-semibold']),
                                        Forms\Components\TextInput::make('custom_delivery_fee')
                                            ->label('Frais de livraison personnalisés')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->visible(fn ($record) => !empty($record?->custom_pickup_location))
                                            ->helperText('Fixez le prix pour la livraison à ce lieu personnalisé'),
                                        Forms\Components\TextInput::make('pickup_address')
                                            ->label('Adresse de récupération')
                                            ->helperText('Adresse exacte pour le point de rendez-vous'),
                                        Forms\Components\Textarea::make('pickup_notes')
                                            ->label('Notes de récupération')
                                            ->rows(2)
                                            ->helperText('Ex: "Devant le café X", "Appeler 5 min avant"'),
                                    ]),
                                Forms\Components\Section::make('Retour')
                                    ->description('Où le client rend le véhicule')
                                    ->schema([
                                        Forms\Components\Select::make('return_zone_id')
                                            ->label('Zone de retour')
                                            ->options(fn () => $loueur
                                                ? DeliveryZone::where('loueur_id', $loueur->id)->pluck('name', 'id')
                                                : []
                                            )
                                            ->helperText('Peut être différent de la zone de récupération'),
                                        Forms\Components\TextInput::make('custom_return_location')
                                            ->label('📍 Lieu de retour personnalisé demandé par le client')
                                            ->disabled()
                                            ->visible(fn ($record) => !empty($record?->custom_return_location))
                                            ->extraAttributes(['class' => 'bg-amber-50 border-amber-300 font-semibold']),
                                        Forms\Components\TextInput::make('custom_return_fee')
                                            ->label('Frais de retour personnalisés')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->visible(fn ($record) => !empty($record?->custom_return_location))
                                            ->helperText('Fixez le prix pour le retour à ce lieu personnalisé'),
                                        Forms\Components\TextInput::make('return_address')
                                            ->label('Adresse de retour')
                                            ->helperText('Laissez vide si identique à la récupération'),
                                        Forms\Components\Textarea::make('return_notes')
                                            ->label('Notes de retour')
                                            ->rows(2)
                                            ->helperText('Instructions spécifiques pour le retour'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Tarification')
                            ->icon('heroicon-o-currency-euro')
                            ->schema([
                                Forms\Components\Placeholder::make('pricing_help')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString('
                                        <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700 rounded-lg text-sm text-indigo-800 dark:text-indigo-200">
                                            <strong>💰 Tarification</strong> — Le total est calculé : Prix de base + Surcharges + Livraison + Options - Remises. Le client voit ce total sur sa confirmation.
                                        </div>
                                    ')),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('base_price')
                                            ->label('Prix de base')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->helperText('Prix du véhicule × nombre de jours'),
                                        Forms\Components\TextInput::make('duration_discount')
                                            ->label('Remise durée')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->helperText('Réduction longue durée appliquée'),
                                        Forms\Components\TextInput::make('season_surcharge')
                                            ->label('Surcharge saison')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->helperText('Supplément haute saison (été, fêtes)'),
                                    ]),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('delivery_fee')
                                            ->label('Frais livraison')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->helperText('Selon la zone de récupération'),
                                        Forms\Components\TextInput::make('return_fee')
                                            ->label('Frais retour')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->helperText('Selon la zone de retour'),
                                        Forms\Components\TextInput::make('options_total')
                                            ->label('Total options')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->helperText('Siège bébé, GPS, chauffeur...'),
                                    ]),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('extra_fees')
                                            ->label('Frais extra')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->helperText('Frais additionnels manuels'),
                                        Forms\Components\TextInput::make('discount_amount')
                                            ->label('Remise')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->helperText('Code promo ou remise manuelle'),
                                        Forms\Components\TextInput::make('total_price')
                                            ->label('TOTAL')
                                            ->numeric()
                                            ->suffix('DA')
                                            ->required()
                                            ->helperText('Montant final facturé au client'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Paiement')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Placeholder::make('payment_help')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString('
                                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-lg text-sm text-emerald-800 dark:text-emerald-200">
                                            <strong>💳 Suivi des paiements</strong> — Gérez l\'acompte, la caution et le paiement final. Le statut "Payé" est visible par le client sur sa page de confirmation.
                                        </div>
                                    ')),
                                Forms\Components\Section::make('Acompte')
                                    ->description('L\'acompte confirme la réservation. Sans paiement avant expiration, la réservation peut être annulée.')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('advance_amount')
                                                    ->label('Montant acompte')
                                                    ->numeric()
                                                    ->suffix('DA')
                                                    ->helperText('Généralement 20-30% du total'),
                                                Forms\Components\Select::make('advance_status')
                                                    ->label('Statut acompte')
                                                    ->options([
                                                        'pending' => 'En attente',
                                                        'paid' => 'Payé',
                                                        'refunded' => 'Remboursé',
                                                    ])
                                                    ->helperText('Passez à "Payé" quand reçu'),
                                                Forms\Components\Select::make('advance_payment_method')
                                                    ->label('Méthode')
                                                    ->options([
                                                        'cash' => 'Espèces',
                                                        'cib' => 'CIB',
                                                        'dahabia' => 'Dahabia',
                                                        'baridimob' => 'BaridiMob',
                                                        'paypal' => 'PayPal',
                                                        'bank_transfer' => 'Virement',
                                                    ])
                                                    ->helperText('Comment le client a payé'),
                                            ]),
                                        Forms\Components\DateTimePicker::make('advance_expires_at')
                                            ->label('Expiration acompte')
                                            ->helperText('Date limite pour recevoir l\'acompte'),
                                    ]),
                                Forms\Components\Section::make('Caution')
                                    ->description('Montant bloqué pendant la location — à rendre au client si pas de dégâts.')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('deposit_amount')
                                                    ->label('Montant caution')
                                                    ->numeric()
                                                    ->suffix('DA')
                                                    ->helperText('Montant défini sur la fiche véhicule'),
                                                Forms\Components\Select::make('deposit_currency')
                                                    ->label('Devise')
                                                    ->options([
                                                        'DZD' => 'DA',
                                                        'EUR' => '€',
                                                    ])
                                                    ->helperText('En quelle devise'),
                                                Forms\Components\Select::make('deposit_status')
                                                    ->label('Statut')
                                                    ->options([
                                                        'pending' => 'En attente',
                                                        'received' => 'Reçue',
                                                        'returned' => 'Rendue',
                                                        'partial' => 'Partielle',
                                                    ])
                                                    ->helperText('Partielle = déduit des dégâts'),
                                            ]),
                                    ]),
                                Forms\Components\Section::make('Paiement final')
                                    ->description('Solde à payer à la remise ou au retour du véhicule.')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('amount_paid')
                                                    ->label('Montant payé')
                                                    ->numeric()
                                                    ->suffix('DA')
                                                    ->helperText('Total encaissé à ce jour'),
                                                Forms\Components\TextInput::make('amount_remaining')
                                                    ->label('Reste à payer')
                                                    ->numeric()
                                                    ->suffix('DA')
                                                    ->helperText('Ce que le client doit encore'),
                                                Forms\Components\Select::make('payment_status')
                                                    ->label('Statut paiement')
                                                    ->options([
                                                        'pending' => 'En attente',
                                                        'partial' => 'Partiel',
                                                        'paid' => 'Payé',
                                                        'refunded' => 'Remboursé',
                                                    ])
                                                    ->helperText('Affiché au client'),
                                            ]),
                                        Forms\Components\Select::make('payment_method')
                                            ->label('Méthode de paiement')
                                            ->options([
                                                'cash' => 'Espèces',
                                                'cib' => 'CIB',
                                                'dahabia' => 'Dahabia',
                                                'baridimob' => 'BaridiMob',
                                                'paypal' => 'PayPal',
                                                'bank_transfer' => 'Virement',
                                            ])
                                            ->helperText('Méthode utilisée pour le solde'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('État véhicule')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Forms\Components\Placeholder::make('condition_help')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString('
                                        <div class="p-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700 rounded-lg text-sm text-rose-800 dark:text-rose-200">
                                            <strong>📋 État du véhicule</strong> — Documentez l\'état avant/après pour vous protéger en cas de litige. Les photos et notes servent de preuve juridique.
                                        </div>
                                    ')),
                                Forms\Components\Section::make('Départ')
                                    ->description('À remplir AVANT de remettre le véhicule au client')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('mileage_start')
                                                    ->label('Kilométrage départ')
                                                    ->numeric()
                                                    ->suffix('km')
                                                    ->helperText('Relevez le compteur exact'),
                                                Forms\Components\Select::make('fuel_level_start')
                                                    ->label('Niveau carburant')
                                                    ->options([
                                                        'empty' => 'Vide',
                                                        'quarter' => '1/4',
                                                        'half' => '1/2',
                                                        'three_quarters' => '3/4',
                                                        'full' => 'Plein',
                                                    ])
                                                    ->helperText('Le client doit rendre avec le même niveau'),
                                            ]),
                                        Forms\Components\Textarea::make('condition_notes_before')
                                            ->label('Notes état avant')
                                            ->rows(2)
                                            ->helperText('Rayures existantes, impacts, état intérieur...'),
                                        Forms\Components\FileUpload::make('photos_before')
                                            ->label('Photos avant')
                                            ->multiple()
                                            ->directory('bookings/photos')
                                            ->visibility('private')
                                            ->helperText('Prenez des photos de tous les angles + intérieur'),
                                    ]),
                                Forms\Components\Section::make('Retour')
                                    ->description('À remplir au retour du véhicule — comparez avec l\'état de départ')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('mileage_end')
                                                    ->label('Kilométrage retour')
                                                    ->numeric()
                                                    ->suffix('km')
                                                    ->helperText('Pour calculer les km parcourus'),
                                                Forms\Components\Select::make('fuel_level_end')
                                                    ->label('Niveau carburant')
                                                    ->options([
                                                        'empty' => 'Vide',
                                                        'quarter' => '1/4',
                                                        'half' => '1/2',
                                                        'three_quarters' => '3/4',
                                                        'full' => 'Plein',
                                                    ])
                                                    ->helperText('Si moins qu\'au départ, facturer le carburant'),
                                            ]),
                                        Forms\Components\Textarea::make('condition_notes_after')
                                            ->label('Notes état après')
                                            ->rows(2)
                                            ->helperText('Nouveaux dégâts, problèmes signalés...'),
                                        Forms\Components\FileUpload::make('photos_after')
                                            ->label('Photos après')
                                            ->multiple()
                                            ->directory('bookings/photos')
                                            ->visibility('private')
                                            ->helperText('Documentez tout nouveau dommage'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Notes')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Textarea::make('internal_notes')
                                    ->label('Notes internes')
                                    ->rows(4)
                                    ->helperText('Ces notes ne sont pas visibles par le client'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Réf.')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vehicle.full_name')
                    ->label('Véhicule')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->formatStateUsing(fn ($record) => $record->getFormattedTotal())
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'success' => 'active',
                        'gray' => 'completed',
                        'danger' => fn ($state) => in_array($state, ['cancelled', 'expired']),
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'active' => 'En cours',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                        'expired' => 'Expirée',
                        default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('advance_status')
                    ->label('Acompte')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'En attente',
                        'paid' => 'Payé ✓',
                        'refunded' => 'Remboursé',
                        default => $state ?? '-',
                    }),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Paiement total')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'partial',
                        'success' => 'paid',
                        'danger' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'En attente',
                        'partial' => 'Partiel',
                        'paid' => 'Payé ✓',
                        'refunded' => 'Remboursé',
                        default => $state ?? '-',
                    }),
                Tables\Columns\TextColumn::make('advance_payment_method')
                    ->label('Mode')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'stripe' => '💳 CB en ligne',
                        'cash' => '💵 Espèces',
                        'paypal' => '🅿️ PayPal',
                        default => $state ?: '-',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'active' => 'En cours',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée',
                        'expired' => 'Expirée',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Paiement')
                    ->options([
                        'pending' => 'En attente',
                        'partial' => 'Partiel',
                        'paid' => 'Payé',
                    ]),
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Véhicule')
                    ->relationship('vehicle', 'full_name'),
            ])
            ->actions([
                Tables\Actions\Action::make('confirmBooking')
                    ->label('Confirmer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Booking $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmer la réservation')
                    ->modalDescription('Voulez-vous confirmer cette réservation ? Un email sera envoyé au client avec un lien pour accéder à sa page de confirmation.')
                    ->modalSubmitActionLabel('Confirmer et envoyer l\'email')
                    ->action(function (Booking $record) {
                        $record->update([
                            'status' => 'confirmed',
                            'confirmed_by_loueur_at' => now(),
                        ]);

                        // Send email to client
                        if ($record->client_email) {
                            try {
                                Mail::to($record->client_email)->send(new BookingConfirmedMail($record));
                                Notification::make()
                                    ->title('Réservation confirmée')
                                    ->body('Un email a été envoyé au client.')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Réservation confirmée')
                                    ->body('La réservation est confirmée mais l\'email n\'a pas pu être envoyé.')
                                    ->warning()
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->title('Réservation confirmée')
                                ->body('Aucun email configuré pour ce client.')
                                ->warning()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('copyConfirmationLink')
                    ->label('Copier le lien')
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->visible(fn (Booking $record) => $record->isConfirmedByLoueur())
                    ->action(function (Booking $record) {
                        Notification::make()
                            ->title('Lien copié')
                            ->body('Lien de confirmation: ' . $record->getConfirmationUrl())
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('completeBooking')
                    ->label('Terminer')
                    ->icon('heroicon-o-flag')
                    ->color('gray')
                    ->visible(fn (Booking $record) => in_array($record->status, ['confirmed', 'active']))
                    ->requiresConfirmation()
                    ->modalHeading('Terminer la location')
                    ->modalDescription('Marquer cette location comme terminée ? Un email sera envoyé au client pour lui demander de laisser un avis.')
                    ->modalSubmitActionLabel('Terminer et demander un avis')
                    ->action(function (Booking $record) {
                        $record->update(['status' => 'completed']);

                        // Send review request email to client
                        if ($record->client_email) {
                            try {
                                Mail::to($record->client_email)->send(new ReviewRequestMail($record));
                                Notification::make()
                                    ->title('Location terminée')
                                    ->body('Un email a été envoyé au client pour lui demander un avis.')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Location terminée')
                                    ->body('L\'email de demande d\'avis n\'a pas pu être envoyé.')
                                    ->warning()
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->title('Location terminée')
                                ->body('Aucun email configuré pour ce client - pas de demande d\'avis envoyée.')
                                ->warning()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('reviewClient')
                    ->label('Noter le client')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn (Booking $record) => $record->status === 'completed' && !$record->loueur_reviewed)
                    ->form([
                        Forms\Components\Placeholder::make('info')
                            ->label('')
                            ->content(fn (Booking $record) => new \Illuminate\Support\HtmlString(
                                '<p class="text-sm text-gray-600">Notez votre expérience avec <strong>' . e($record->client_name ?? 'ce client') . '</strong>.</p>'
                            )),
                        Forms\Components\Select::make('rating_overall')
                            ->label('Note globale')
                            ->options([5 => '⭐⭐⭐⭐⭐ Excellent', 4 => '⭐⭐⭐⭐ Très bien', 3 => '⭐⭐⭐ Bien', 2 => '⭐⭐ Moyen', 1 => '⭐ Mauvais'])
                            ->required(),
                        Forms\Components\Select::make('rating_respect')
                            ->label('Respect & comportement')
                            ->options([5 => '⭐⭐⭐⭐⭐', 4 => '⭐⭐⭐⭐', 3 => '⭐⭐⭐', 2 => '⭐⭐', 1 => '⭐']),
                        Forms\Components\Select::make('rating_punctuality')
                            ->label('Ponctualité')
                            ->options([5 => '⭐⭐⭐⭐⭐', 4 => '⭐⭐⭐⭐', 3 => '⭐⭐⭐', 2 => '⭐⭐', 1 => '⭐']),
                        Forms\Components\Select::make('rating_cleanliness')
                            ->label('Propreté du véhicule au retour')
                            ->options([5 => '⭐⭐⭐⭐⭐', 4 => '⭐⭐⭐⭐', 3 => '⭐⭐⭐', 2 => '⭐⭐', 1 => '⭐']),
                        Forms\Components\Textarea::make('comment')
                            ->label('Commentaire (optionnel)')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Votre expérience avec ce client...'),
                    ])
                    ->modalHeading('Noter le client')
                    ->modalSubmitActionLabel('Publier ma note')
                    ->action(function (Booking $record, array $data) {
                        $loueur = Auth::user()->loueur;

                        Review::create([
                            'booking_id' => $record->id,
                            'loueur_id' => $loueur->id,
                            'type' => 'loueur_to_client',
                            'reviewer_id' => $loueur->user_id,
                            'reviewed_user_id' => $record->client_id,
                            'rating_overall' => $data['rating_overall'],
                            'rating_respect' => $data['rating_respect'] ?? null,
                            'rating_punctuality' => $data['rating_punctuality'] ?? null,
                            'rating_cleanliness' => $data['rating_cleanliness'] ?? null,
                            'comment' => $data['comment'] ?? null,
                            'is_public' => true,
                            'is_approved' => true,
                        ]);

                        $record->update(['loueur_reviewed' => true]);

                        Notification::make()
                            ->title('Merci !')
                            ->body('Votre avis sur le client a été enregistré.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('downloadContract')
                    ->label('Contrat')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (Booking $record) => route('contract.download', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('whatsappConfirm')
                        ->label('WhatsApp: Confirmer')
                        ->icon('heroicon-o-chat-bubble-left')
                        ->color('success')
                        ->visible(fn (Booking $record) => $record->client_whatsapp && $record->status === 'confirmed')
                        ->url(fn (Booking $record) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->client_whatsapp) . '?text=' . urlencode(
                            "Bonjour {$record->client_name} !\n\n" .
                            "Votre réservation est CONFIRMÉE ✅\n\n" .
                            "📅 Du " . $record->start_date->format('d/m/Y') . " au " . $record->end_date->format('d/m/Y') . "\n" .
                            "🚗 " . ($record->vehicle->full_name ?? 'Véhicule') . "\n" .
                            "💰 Total: " . number_format($record->total_price, 0, ',', ' ') . " DA\n\n" .
                            "À bientôt !\n" .
                            Setting::get('company_name', 'ResaDZ')
                        ))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('whatsappReminder')
                        ->label('WhatsApp: Rappel')
                        ->icon('heroicon-o-bell')
                        ->color('warning')
                        ->visible(fn (Booking $record) => $record->client_whatsapp && in_array($record->status, ['confirmed', 'active']))
                        ->url(fn (Booking $record) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->client_whatsapp) . '?text=' . urlencode(
                            "Bonjour {$record->client_name} !\n\n" .
                            "⏰ Rappel pour votre location:\n\n" .
                            "📅 " . $record->start_date->format('d/m/Y') . " à " . ($record->pickup_time ?? '09:00') . "\n" .
                            "🚗 " . ($record->vehicle->full_name ?? 'Véhicule') . "\n\n" .
                            "N'oubliez pas votre permis et pièce d'identité !\n\n" .
                            Setting::get('company_name', 'ResaDZ')
                        ))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('whatsappReview')
                        ->label('WhatsApp: Demander avis')
                        ->icon('heroicon-o-star')
                        ->color('gray')
                        ->visible(fn (Booking $record) => $record->client_whatsapp && $record->status === 'completed')
                        ->url(fn (Booking $record) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->client_whatsapp) . '?text=' . urlencode(
                            "Bonjour {$record->client_name} !\n\n" .
                            "Merci d'avoir loué avec nous ! 🙏\n\n" .
                            "Votre avis compte beaucoup. Pourriez-vous nous laisser un commentaire ?\n\n" .
                            "👉 " . route('review.create', $record->confirmation_token) . "\n\n" .
                            "Merci !\n" .
                            Setting::get('company_name', 'ResaDZ')
                        ))
                        ->openUrlInNewTab(),
                ])->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                    ->color('success')
                    ->visible(fn (Booking $record) => !empty($record->client_whatsapp)),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}

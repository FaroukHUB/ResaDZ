<?php

namespace App\Filament\Loueur\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Acomptes extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Acomptes & Réservations';

    protected static ?string $title = 'Acomptes & Réservations';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.loueur.pages.acomptes';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;
        return $loueur && $loueur->isLoueur();
    }

    public function mount(): void
    {
        $loueur = Auth::user()->loueur;

        if ($loueur) {
            $this->form->fill([
                'advance_percentage' => $loueur->getSetting('advance_percentage', 30),
                'advance_payment_methods' => $loueur->getSetting('advance_payment_methods', []),
                'cancellation_deadline_hours' => $loueur->getSetting('cancellation_deadline_hours', 48),
                'auto_confirm_bookings' => $loueur->getSetting('auto_confirm_bookings', false),
                'require_documents' => $loueur->getSetting('require_documents', true),
                'return_margin_hours' => $loueur->getSetting('return_margin_hours', 2),
                'custom_location_enabled' => $loueur->getSetting('custom_location_enabled', false),
                'free_airport_delivery_enabled' => $loueur->getSetting('free_airport_delivery_days', 0) > 0,
                'free_airport_delivery_days' => $loueur->getSetting('free_airport_delivery_days', 0),
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
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
                            ->helperText('Ex: 30 = 30% du total. 0 = pas d\'acompte.'),
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
                            ->reorderable(false),
                    ]),
                Forms\Components\Section::make('Délais & Options')
                    ->schema([
                        Forms\Components\TextInput::make('cancellation_deadline_hours')
                            ->label('Délai d\'annulation (heures)')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Heures avant le début de la location où l\'annulation est possible'),
                        Forms\Components\TextInput::make('return_margin_hours')
                            ->label('Marge horaire pour le retour')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(12)
                            ->suffix('heures')
                            ->helperText('Temps accordé après l\'heure prévue de retour'),
                        Forms\Components\Toggle::make('auto_confirm_bookings')
                            ->label('Confirmer automatiquement les réservations'),
                        Forms\Components\Toggle::make('require_documents')
                            ->label('Exiger les documents du client (pièce d\'identité + permis)'),
                    ]),
                Forms\Components\Section::make('Lieu de livraison personnalisé')
                    ->description('Permettez aux clients de proposer un lieu de récupération/retour personnalisé en dehors de vos zones prédéfinies.')
                    ->schema([
                        Forms\Components\Toggle::make('custom_location_enabled')
                            ->label('Accepter les demandes de lieu personnalisé')
                            ->helperText('Le client pourra indiquer une adresse libre. Vous fixerez le prix manuellement sur chaque réservation concernée.'),
                    ]),
                Forms\Components\Section::make('Livraison aéroport offerte')
                    ->description('Offrez la livraison aéroport à partir d\'un certain nombre de jours de location.')
                    ->schema([
                        Forms\Components\Placeholder::make('airport_help')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-700 rounded-xl">
                                    <div class="flex items-start gap-3">
                                        <span class="text-xl flex-shrink-0">✈️</span>
                                        <div>
                                            <p class="font-semibold text-sky-900 dark:text-sky-100">Un argument très fort pour la diaspora qui rentre en été</p>
                                            <p class="text-sm text-sky-800 dark:text-sky-200 mt-1">Ça booste les longues locations ! Le badge apparaîtra sur vos véhicules et les clients verront la livraison gratuite dans le récapitulatif.</p>
                                        </div>
                                    </div>
                                </div>
                            ')),
                        Forms\Components\Toggle::make('free_airport_delivery_enabled')
                            ->label('Activer la livraison aéroport offerte')
                            ->live(),
                        Forms\Components\TextInput::make('free_airport_delivery_days')
                            ->label('À partir de combien de jours ?')
                            ->numeric()
                            ->minValue(1)
                            ->suffix('jours')
                            ->placeholder('Ex: 7')
                            ->helperText('La livraison aéroport sera gratuite pour toute location de ce nombre de jours ou plus')
                            ->visible(fn (Forms\Get $get) => (bool) $get('free_airport_delivery_enabled')),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) return;

        $data = $this->form->getState();

        $loueur->setSetting('advance_percentage', $data['advance_percentage'] ?? 0, 'integer');
        $loueur->setSetting('advance_payment_methods', $data['advance_payment_methods'] ?? [], 'json');
        $loueur->setSetting('cancellation_deadline_hours', $data['cancellation_deadline_hours'] ?? 48, 'integer');
        $loueur->setSetting('auto_confirm_bookings', $data['auto_confirm_bookings'] ?? false, 'boolean');
        $loueur->setSetting('require_documents', $data['require_documents'] ?? true, 'boolean');
        $loueur->setSetting('return_margin_hours', $data['return_margin_hours'] ?? 2, 'integer');
        $loueur->setSetting('custom_location_enabled', $data['custom_location_enabled'] ?? false, 'boolean');

        $freeAirportDays = ($data['free_airport_delivery_enabled'] ?? false)
            ? max(0, (int) ($data['free_airport_delivery_days'] ?? 0))
            : 0;
        $loueur->setSetting('free_airport_delivery_days', $freeAirportDays, 'integer');

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer')
                ->submit('save'),
        ];
    }
}

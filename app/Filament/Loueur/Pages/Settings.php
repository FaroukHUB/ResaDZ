<?php

namespace App\Filament\Loueur\Pages;

use App\Models\Loueur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Settings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $navigationLabel = 'Paramètres';

    protected static ?string $title = 'Paramètres de mon agence';

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
                'notify_whatsapp' => $loueur->getSetting('notify_whatsapp', true),
                'notify_email' => $loueur->getSetting('notify_email', true),
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
                            ]),
                        Forms\Components\Tabs\Tab::make('Notifications')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                Forms\Components\Toggle::make('notify_whatsapp')
                                    ->label('Notifications WhatsApp')
                                    ->helperText('Recevoir les notifications de réservation sur WhatsApp'),
                                Forms\Components\Toggle::make('notify_email')
                                    ->label('Notifications Email')
                                    ->helperText('Recevoir les notifications de réservation par email'),
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
        $loueur->setSetting('notify_whatsapp', $data['notify_whatsapp'] ?? true, 'boolean');
        $loueur->setSetting('notify_email', $data['notify_email'] ?? true, 'boolean');

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

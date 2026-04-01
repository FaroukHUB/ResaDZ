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
                'disponible_national' => $loueur->disponible_national ?? false,
                'logo' => $loueur->logo,
                'cover_image' => $loueur->cover_image,
                'specialites' => $loueur->specialites ?? [],
                'langues' => $loueur->langues ?? [],
                'horaires' => $loueur->horaires,
                'facebook' => $loueur->facebook,
                'instagram' => $loueur->instagram,
                'tiktok' => $loueur->tiktok,
                'payment_methods' => $loueur->payment_methods ?? [],
                'paypal_email' => $loueur->paypal_email,
                'iban' => $loueur->iban,
                'wise_email' => $loueur->wise_email,
                'baridimob_rip' => $loueur->baridimob_rip,
                // Settings from loueur_settings table
                'notify_push' => $loueur->getSetting('notify_push', true),
                'notify_whatsapp' => $loueur->getSetting('notify_whatsapp', true),
                'notify_email' => $loueur->getSetting('notify_email', true),
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
                                    ->rows(4)
                                    ->id('loueur-description')
                                    ->helperText('Présentez votre agence en quelques phrases'),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('generateDescription')
                                        ->label('Générer avec l\'IA')
                                        ->icon('heroicon-o-sparkles')
                                        ->color('gray')
                                        ->modalHeading('Générer une description avec l\'IA')
                                        ->modalDescription('Renseignez les informations de votre agence et l\'IA rédigera une description professionnelle.')
                                        ->modalWidth('lg')
                                        ->form([
                                            Forms\Components\TextInput::make('annee_creation')
                                                ->label('Année de création')
                                                ->placeholder('Ex: 2022')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('ville')
                                                ->label('Ville / Wilaya')
                                                ->placeholder('Ex: Birkhadem, Alger'),
                                            Forms\Components\TextInput::make('types_vehicules')
                                                ->label('Types de véhicules')
                                                ->placeholder('Ex: SUV, berlines, citadines, 7 places, utilitaires'),
                                            Forms\Components\TextInput::make('services')
                                                ->label('Services proposés')
                                                ->placeholder('Ex: livraison aéroport, location longue durée, avec chauffeur'),
                                            Forms\Components\TextInput::make('clientele')
                                                ->label('Clientèle cible')
                                                ->placeholder('Ex: diaspora, touristes, professionnels, familles'),
                                            Forms\Components\TextInput::make('avantages')
                                                ->label('Points forts / Avantages')
                                                ->placeholder('Ex: véhicules récents, prix compétitifs, disponible 24h/24'),
                                        ])
                                        ->action(function (array $data, $livewire) {
                                            try {
                                                $response = \Illuminate\Support\Facades\Http::withHeaders([
                                                    'Authorization' => 'Bearer ' . config('services.groq.api_key'),
                                                    'Content-Type' => 'application/json',
                                                ])
                                                ->timeout(15)
                                                ->post('https://api.groq.com/openai/v1/chat/completions', [
                                                    'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
                                                    'messages' => [
                                                        ['role' => 'user', 'content' => $this->buildDescriptionPrompt($data)],
                                                    ],
                                                    'temperature' => 0.7,
                                                    'max_tokens' => 300,
                                                ]);

                                                if ($response->successful()) {
                                                    $description = trim(str_replace(['"', '«', '»'], '', $response->json('choices.0.message.content', '')));
                                                    $livewire->data['description'] = $description;
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Description générée')
                                                        ->body('Vous pouvez la modifier avant d\'enregistrer.')
                                                        ->success()
                                                        ->send();
                                                } else {
                                                    \Filament\Notifications\Notification::make()
                                                        ->title('Erreur')
                                                        ->body('Impossible de générer la description. Réessayez.')
                                                        ->danger()
                                                        ->send();
                                                }
                                            } catch (\Exception $e) {
                                                \Filament\Notifications\Notification::make()
                                                    ->title('Erreur')
                                                    ->body('Service IA indisponible. Réessayez dans quelques instants.')
                                                    ->danger()
                                                    ->send();
                                            }
                                        }),
                                ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('city')
                                            ->label('Ville'),
                                        Forms\Components\TextInput::make('wilaya')
                                            ->label('Wilaya'),
                                    ]),
                                Forms\Components\TextInput::make('address')
                                    ->label('Adresse'),
                                Forms\Components\Toggle::make('disponible_national')
                                    ->label('Je propose mes véhicules partout en Algérie')
                                    ->helperText('Votre profil et vos véhicules apparaîtront dans toutes les wilayas'),
                                Forms\Components\Section::make('Images')
                                    ->schema([
                                        Forms\Components\FileUpload::make('logo')
                                            ->label('Logo')
                                            ->image()
                                            ->directory('loueurs/logos')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('1:1')
                                            ->imageResizeTargetWidth('200')
                                            ->imageResizeTargetHeight('200')
                                            ->helperText('Logo affiché sur votre profil et les cartes véhicules (carré, 200x200px)'),
                                        Forms\Components\FileUpload::make('cover_image')
                                            ->label('Photo de couverture / Bannière')
                                            ->image()
                                            ->directory('loueurs/covers')
                                            ->helperText('Image affichée en bannière sur votre profil (idéal: 1400x400px)'),
                                    ]),
                                Forms\Components\Section::make('Présentation')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('specialites')
                                            ->label('Spécialités')
                                            ->options(\App\Models\Loueur::SPECIALITES)
                                            ->columns(2)
                                            ->helperText('Sélectionnez vos domaines d\'expertise'),
                                        Forms\Components\CheckboxList::make('langues')
                                            ->label('Langues parlées')
                                            ->options(\App\Models\Loueur::LANGUES)
                                            ->columns(4),
                                        Forms\Components\TextInput::make('horaires')
                                            ->label('Horaires d\'ouverture')
                                            ->placeholder('Ex: Lun-Sam 8h-20h, Dim sur RDV')
                                            ->maxLength(255),
                                    ]),
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
                                        'wise' => 'En ligne (Wise, Revolut...)',
                                    ])
                                    ->columns(3),
                                Forms\Components\Section::make('Détails des méthodes')
                                    ->schema([
                                        Forms\Components\TextInput::make('paypal_email')
                                            ->label('Email PayPal'),
                                        Forms\Components\TextInput::make('iban')
                                            ->label('IBAN (virement)'),
                                        Forms\Components\TextInput::make('wise_email')
                                            ->label('Email paiement en ligne (Wise, Revolut...)'),
                                        Forms\Components\TextInput::make('baridimob_rip')
                                            ->label('RIP BaridiMob'),
                                    ])
                                    ->columns(2),
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
            'disponible_national' => $data['disponible_national'] ?? false,
            'logo' => $data['logo'] ?? null,
            'cover_image' => $data['cover_image'] ?? null,
            'specialites' => $data['specialites'] ?? [],
            'langues' => $data['langues'] ?? [],
            'horaires' => $data['horaires'] ?? null,
            'facebook' => $data['facebook'] ?? null,
            'instagram' => $data['instagram'] ?? null,
            'tiktok' => $data['tiktok'] ?? null,
            'payment_methods' => $data['payment_methods'] ?? [],
            'paypal_email' => $data['paypal_email'] ?? null,
            'iban' => $data['iban'] ?? null,
            'wise_email' => $data['wise_email'] ?? null,
            'baridimob_rip' => $data['baridimob_rip'] ?? null,
        ]);

        // Update settings
        $loueur->setSetting('notify_push', $data['notify_push'] ?? true, 'boolean');
        $loueur->setSetting('notify_whatsapp', $data['notify_whatsapp'] ?? true, 'boolean');
        $loueur->setSetting('notify_email', $data['notify_email'] ?? true, 'boolean');

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

    protected function buildDescriptionPrompt(array $data): string
    {
        $loueur = Auth::user()->loueur;
        $companyName = $loueur?->company_name ?? 'Mon agence';

        return "Tu es un expert en rédaction de profils d'agences de location de voiture en Algérie.

Rédige une description professionnelle et convaincante pour cette agence :

- Nom de l'agence : {$companyName}
- Créée en : " . ($data['annee_creation'] ?? 'non précisé') . "
- Basée à : " . ($data['ville'] ?? 'non précisé') . "
- Types de véhicules : " . ($data['types_vehicules'] ?? 'non précisé') . "
- Services proposés : " . ($data['services'] ?? 'non précisé') . "
- Clientèle cible : " . ($data['clientele'] ?? 'non précisé') . "
- Points forts : " . ($data['avantages'] ?? 'non précisé') . "
- Plateforme : ResaDZ

La description doit :
- Faire 3 à 4 phrases maximum
- Être rédigée à la 3ème personne (l'agence, pas nous)
- Inspirer confiance aux clients
- Mentionner la localisation si fournie
- Être en français
- Ne pas utiliser de superlatifs comme 'meilleur' ou 'numéro 1'
- Sonner humaine et authentique, pas publicitaire
- Ne pas commencer par 'Bienvenue'

Réponds uniquement avec la description, sans introduction ni commentaire.";
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

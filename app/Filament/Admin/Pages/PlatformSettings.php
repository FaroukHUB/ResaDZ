<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PlatformSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static string $view = 'filament.admin.pages.platform-settings';

    protected static ?string $navigationLabel = 'Paramètres plateforme';

    protected static ?string $title = 'Paramètres de la plateforme';

    protected static ?string $navigationGroup = 'Paramètres';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // Company Info
            'company_name' => Setting::get('company_name', 'ResaDZ'),
            'company_tagline' => Setting::get('company_tagline', 'Marketplace de location de voitures'),
            'company_country' => Setting::get('company_country', 'Algérie'),
            'company_email' => Setting::get('company_email', 'contact@resadz.com'),
            'company_phone' => Setting::get('company_phone', ''),
            'company_address' => Setting::get('company_address', ''),

            // Commission & Pricing - Loueurs (taux dégressifs)
            'commission_rate_1_to_10_days' => Setting::get('commission_rate_1_to_10_days', 8),
            'commission_rate_11_plus_days' => Setting::get('commission_rate_11_plus_days', 6),

            // Commission & Pricing - Chauffeurs/Taxis (taux fixe 10%)
            'commission_rate_transfer' => Setting::get('commission_rate_transfer', 10),
            'commission_rate_delivery' => Setting::get('commission_rate_delivery', 10),

            'dzd_to_usd_rate' => Setting::get('dzd_to_usd_rate', 0.0074),
            'dzd_to_eur_rate' => Setting::get('dzd_to_eur_rate', 0.0068),
            'min_paypal_amount_usd' => Setting::get('min_paypal_amount_usd', 1),

            // Booking Settings
            'operating_hours_start' => Setting::get('operating_hours_start', 7),
            'operating_hours_end' => Setting::get('operating_hours_end', 21),
            'default_return_margin_hours' => Setting::get('default_return_margin_hours', 2),
            'min_return_hour' => Setting::get('min_return_hour', 10),
            'max_return_hour' => Setting::get('max_return_hour', 22),
            'weekend_days' => Setting::get('weekend_days', ['friday', 'saturday']),

            // Invoice Settings
            'invoice_due_days' => Setting::get('invoice_due_days', 30),
            'commission_invoice_due_days' => Setting::get('commission_invoice_due_days', 15),
            'invoice_prefix' => Setting::get('invoice_prefix', 'FAC'),
            'booking_reference_prefix' => Setting::get('booking_reference_prefix', 'RES'),

            // Calendar Settings
            'calendar_past_months' => Setting::get('calendar_past_months', 3),
            'calendar_future_months' => Setting::get('calendar_future_months', 12),

            // Trial Settings
            'trial_days' => Setting::get('trial_days', 30),

            // Hero Section
            'hero_title_line1' => Setting::get('hero_title_line1', 'Louez votre voiture'),
            'hero_title_line2' => Setting::get('hero_title_line2', 'partout en Algérie'),
            'hero_subtitle' => Setting::get('hero_subtitle', 'Comparez et réservez parmi des centaines de véhicules disponibles dans toutes les wilayas'),
            'hero_wilayas_count' => Setting::get('hero_wilayas_count', 58),

            // How It Works Section
            'how_it_works_title' => Setting::get('how_it_works_title', 'En 3 étapes simples'),
            'step1_title' => Setting::get('step1_title', 'Recherchez'),
            'step1_description' => Setting::get('step1_description', 'Trouvez le véhicule idéal parmi notre large sélection'),
            'step2_title' => Setting::get('step2_title', 'Réservez'),
            'step2_description' => Setting::get('step2_description', 'Réservez en ligne en quelques clics, c\'est rapide et sécurisé'),
            'step3_title' => Setting::get('step3_title', 'Roulez'),
            'step3_description' => Setting::get('step3_description', 'Récupérez votre véhicule et profitez de votre trajet'),

            // Section Titles
            'featured_section_title' => Setting::get('featured_section_title', 'Notre sélection pour vous'),
            'categories_section_title' => Setting::get('categories_section_title', 'Parcourir par catégorie'),
            'loueurs_section_title' => Setting::get('loueurs_section_title', 'Nos loueurs partenaires'),

            // Social Media
            'facebook' => Setting::get('facebook', ''),
            'instagram' => Setting::get('instagram', ''),
            'tiktok' => Setting::get('tiktok', 'https://www.tiktok.com/@resadzalger'),
            'google_reviews_url' => Setting::get('google_reviews_url', ''),
            'tutorial_calendar_video' => Setting::get('tutorial_calendar_video', 'https://youtu.be/vhC-3PXGUP8'),
            'google_rating' => Setting::get('google_rating', 4.5),
            'whatsapp' => Setting::get('whatsapp', ''),

            // Logos & Favicon
            'studio_visual_priority' => Setting::get('studio_visual_priority', false),
            'logo_light' => Setting::get('logo_light', ''),
            'logo_dark' => Setting::get('logo_dark', ''),
            'favicon' => Setting::get('favicon', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        // Company Info Tab
                        Forms\Components\Tabs\Tab::make('Entreprise')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\Section::make('Informations de l\'entreprise')
                                    ->description('Ces informations apparaissent sur les factures et documents')
                                    ->schema([
                                        Forms\Components\TextInput::make('company_name')
                                            ->label('Nom de l\'entreprise')
                                            ->required()
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('company_tagline')
                                            ->label('Slogan')
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('company_country')
                                            ->label('Pays')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('company_email')
                                            ->label('Email de contact')
                                            ->email()
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('company_phone')
                                            ->label('Téléphone')
                                            ->tel()
                                            ->maxLength(50),
                                        Forms\Components\Textarea::make('company_address')
                                            ->label('Adresse')
                                            ->rows(2)
                                            ->maxLength(500),
                                    ])
                                    ->columns(2),
                            ]),

                        // Appearance Tab
                        Forms\Components\Tabs\Tab::make('Apparence')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\Section::make('Visuels des véhicules')
                                    ->description('Choisissez quelle image apparaît en premier sur les cartes et les fiches véhicules.')
                                    ->schema([
                                        Forms\Components\Toggle::make('studio_visual_priority')
                                            ->label('Afficher le visuel studio en image principale')
                                            ->helperText('Désactivé : la photo du loueur prime, le visuel studio ne sert que si elle est absente. Activé : le visuel studio passe en premier et la photo du loueur bascule en tête de galerie — rien n\'est perdu, elle reste visible en un clic.'),
                                    ]),
                                Forms\Components\Section::make('Logos')
                                    ->description('Uploadez vos logos pour le header et le footer')
                                    ->schema([
                                        Forms\Components\FileUpload::make('favicon')
                                            ->label('Favicon')
                                            ->image()
                                            ->directory('logos')
                                            ->disk('public')
                                            ->imageResizeMode('contain')
                                            ->imageResizeTargetWidth('96')
                                            ->imageResizeTargetHeight('96')
                                            ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/svg+xml', 'image/webp'])
                                            ->maxSize(1024)
                                            ->helperText('Favicon du site (icône onglet navigateur). PNG 96x96 recommandé. Si vide, le logo sera utilisé.')
                                            ->columnSpanFull(),
                                        Forms\Components\FileUpload::make('logo_light')
                                            ->label('Logo fond clair (header)')
                                            ->image()
                                            ->directory('logos')
                                            ->disk('public')
                                            ->imageResizeMode('contain')
                                            ->imageCropAspectRatio(null)
                                            ->imageResizeTargetWidth('400')
                                            ->imageResizeTargetHeight('120')
                                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'])
                                            ->maxSize(2048)
                                            ->helperText('Logo pour le header (fond blanc). PNG transparent recommandé. Max 2MB.'),
                                        Forms\Components\FileUpload::make('logo_dark')
                                            ->label('Logo fond sombre (footer)')
                                            ->image()
                                            ->directory('logos')
                                            ->disk('public')
                                            ->imageResizeMode('contain')
                                            ->imageCropAspectRatio(null)
                                            ->imageResizeTargetWidth('400')
                                            ->imageResizeTargetHeight('120')
                                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'])
                                            ->maxSize(2048)
                                            ->helperText('Logo pour le footer (fond noir). PNG transparent ou version blanche recommandé. Max 2MB.'),
                                    ])
                                    ->columns(2),
                            ]),

                        // Commission & Pricing Tab
                        Forms\Components\Tabs\Tab::make('Commission & Tarifs')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Section::make('Commission ResaDZ (Taux dégressifs)')
                                    ->description('Commission prélevée UNIQUEMENT au loueur, en pourcentage du montant HT. Le locataire ne paie aucune commission.')
                                    ->schema([
                                        Forms\Components\Placeholder::make('commission_info')
                                            ->label('')
                                            ->content('La commission est calculée sur le montant total HT de la location (prix/jour × nombre de jours). Les frais de livraison et options ne sont pas commissionnés.')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('commission_rate_1_to_10_days')
                                            ->label('Taux 1-10 jours')
                                            ->numeric()
                                            ->required()
                                            ->default(8)
                                            ->suffix('%')
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->helperText('Commission pour les locations de 1 à 10 jours'),
                                        Forms\Components\TextInput::make('commission_rate_11_plus_days')
                                            ->label('Taux +10 jours')
                                            ->numeric()
                                            ->required()
                                            ->default(6)
                                            ->suffix('%')
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->helperText('Commission pour les locations de plus de 10 jours'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Commission Chauffeurs/Taxis (Taux fixe)')
                                    ->description('Commission prélevée UNIQUEMENT au chauffeur sur les transferts et livraisons. Le client ne paie aucune commission.')
                                    ->schema([
                                        Forms\Components\Placeholder::make('commission_driver_info')
                                            ->label('')
                                            ->content('Commission fixe de 10% sur le montant total de chaque course (transfert ou livraison).')
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('commission_rate_transfer')
                                            ->label('Taux Transferts')
                                            ->numeric()
                                            ->required()
                                            ->default(10)
                                            ->suffix('%')
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->helperText('Commission sur les courses de transfert (taxi, VTC)'),
                                        Forms\Components\TextInput::make('commission_rate_delivery')
                                            ->label('Taux Livraisons')
                                            ->numeric()
                                            ->required()
                                            ->default(10)
                                            ->suffix('%')
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->helperText('Commission sur les livraisons de colis'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Conversion de devises')
                                    ->schema([
                                        Forms\Components\TextInput::make('dzd_to_usd_rate')
                                            ->label('Taux DZD → USD')
                                            ->numeric()
                                            ->required()
                                            ->step(0.0001)
                                            ->helperText('1 DZD = X USD (pour PayPal)'),
                                        Forms\Components\TextInput::make('dzd_to_eur_rate')
                                            ->label('Taux DZD → EUR')
                                            ->numeric()
                                            ->required()
                                            ->step(0.0001)
                                            ->helperText('1 DZD = X EUR (affichage équivalent Euro)'),
                                        Forms\Components\TextInput::make('min_paypal_amount_usd')
                                            ->label('Montant minimum PayPal')
                                            ->numeric()
                                            ->required()
                                            ->suffix('USD'),
                                    ])
                                    ->columns(3),
                            ]),

                        // Booking Settings Tab
                        Forms\Components\Tabs\Tab::make('Réservations')
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                Forms\Components\Section::make('Horaires d\'ouverture')
                                    ->schema([
                                        Forms\Components\TextInput::make('operating_hours_start')
                                            ->label('Heure d\'ouverture')
                                            ->numeric()
                                            ->required()
                                            ->minValue(0)
                                            ->maxValue(23)
                                            ->suffix('h'),
                                        Forms\Components\TextInput::make('operating_hours_end')
                                            ->label('Heure de fermeture')
                                            ->numeric()
                                            ->required()
                                            ->minValue(0)
                                            ->maxValue(23)
                                            ->suffix('h'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Paramètres de retour')
                                    ->schema([
                                        Forms\Components\TextInput::make('default_return_margin_hours')
                                            ->label('Marge heure retour (défaut)')
                                            ->numeric()
                                            ->required()
                                            ->suffix('heures')
                                            ->helperText('Heures ajoutées à l\'heure de prise en charge'),
                                        Forms\Components\TextInput::make('min_return_hour')
                                            ->label('Heure min de retour')
                                            ->numeric()
                                            ->required()
                                            ->suffix('h'),
                                        Forms\Components\TextInput::make('max_return_hour')
                                            ->label('Heure max de retour')
                                            ->numeric()
                                            ->required()
                                            ->suffix('h'),
                                    ])
                                    ->columns(3),

                                Forms\Components\Section::make('Weekend')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('weekend_days')
                                            ->label('Jours de weekend (majoration)')
                                            ->options([
                                                'monday' => 'Lundi',
                                                'tuesday' => 'Mardi',
                                                'wednesday' => 'Mercredi',
                                                'thursday' => 'Jeudi',
                                                'friday' => 'Vendredi',
                                                'saturday' => 'Samedi',
                                                'sunday' => 'Dimanche',
                                            ])
                                            ->columns(4),
                                    ]),
                            ]),

                        // Invoice Settings Tab
                        Forms\Components\Tabs\Tab::make('Facturation')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('Échéances')
                                    ->schema([
                                        Forms\Components\TextInput::make('invoice_due_days')
                                            ->label('Échéance facture standard')
                                            ->numeric()
                                            ->required()
                                            ->suffix('jours'),
                                        Forms\Components\TextInput::make('commission_invoice_due_days')
                                            ->label('Échéance facture commission')
                                            ->numeric()
                                            ->required()
                                            ->suffix('jours'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Préfixes')
                                    ->schema([
                                        Forms\Components\TextInput::make('invoice_prefix')
                                            ->label('Préfixe facture')
                                            ->maxLength(10)
                                            ->helperText('Ex: FAC → FAC-2024-001'),
                                        Forms\Components\TextInput::make('booking_reference_prefix')
                                            ->label('Préfixe réservation')
                                            ->maxLength(10)
                                            ->helperText('Ex: RES → RES-ABCD1234'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Calendrier')
                                    ->schema([
                                        Forms\Components\TextInput::make('calendar_past_months')
                                            ->label('Mois passés affichés')
                                            ->numeric()
                                            ->suffix('mois'),
                                        Forms\Components\TextInput::make('calendar_future_months')
                                            ->label('Mois futurs affichés')
                                            ->numeric()
                                            ->suffix('mois'),
                                    ])
                                    ->columns(2),
                            ]),

                        // Content Tab
                        Forms\Components\Tabs\Tab::make('Contenu page accueil')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Section::make('Section Hero')
                                    ->schema([
                                        Forms\Components\TextInput::make('hero_title_line1')
                                            ->label('Titre ligne 1')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('hero_title_line2')
                                            ->label('Titre ligne 2')
                                            ->maxLength(100),
                                        Forms\Components\Textarea::make('hero_subtitle')
                                            ->label('Sous-titre')
                                            ->rows(2)
                                            ->maxLength(300),
                                        Forms\Components\TextInput::make('hero_wilayas_count')
                                            ->label('Nombre de wilayas')
                                            ->numeric(),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Section "En X étapes"')
                                    ->schema([
                                        Forms\Components\TextInput::make('how_it_works_title')
                                            ->label('Titre section')
                                            ->maxLength(100)
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('step1_title')
                                            ->label('Étape 1 - Titre')
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('step1_description')
                                            ->label('Étape 1 - Description')
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('step2_title')
                                            ->label('Étape 2 - Titre')
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('step2_description')
                                            ->label('Étape 2 - Description')
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('step3_title')
                                            ->label('Étape 3 - Titre')
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('step3_description')
                                            ->label('Étape 3 - Description')
                                            ->maxLength(200),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Titres de sections')
                                    ->schema([
                                        Forms\Components\TextInput::make('featured_section_title')
                                            ->label('Section véhicules en vedette')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('categories_section_title')
                                            ->label('Section catégories')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('loueurs_section_title')
                                            ->label('Section loueurs')
                                            ->maxLength(100),
                                    ])
                                    ->columns(3),
                            ]),

                        // Trial Tab
                        Forms\Components\Tabs\Tab::make('Période d\'essai')
                            ->icon('heroicon-o-gift')
                            ->schema([
                                Forms\Components\Section::make('Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('trial_days')
                                            ->label('Durée période d\'essai')
                                            ->numeric()
                                            ->required()
                                            ->suffix('jours')
                                            ->helperText('Durée par défaut quand vous offrez une période sans commission'),
                                    ]),
                            ]),

                        // Social Media Tab
                        Forms\Components\Tabs\Tab::make('Tutoriels vidéo')
                            ->icon('heroicon-o-play-circle')
                            ->schema([
                                Forms\Components\Section::make('Vidéos d\'aide pour les loueurs')
                                    ->description('Collez un lien YouTube ou Vimeo. Laissez vide pour masquer la vidéo.')
                                    ->schema([
                                        Forms\Components\TextInput::make('tutorial_calendar_video')
                                            ->label('Calendrier loueur')
                                            ->url()
                                            ->placeholder('https://youtu.be/xxxxxxxx')
                                            ->prefixIcon('heroicon-o-play')
                                            ->helperText('Affichée dans le panel loueur, page Calendrier.'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Réseaux sociaux')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\Section::make('Liens réseaux sociaux')
                                    ->description('Ces liens apparaissent dans le footer du site')
                                    ->schema([
                                        Forms\Components\TextInput::make('facebook')
                                            ->label('Facebook')
                                            ->url()
                                            ->placeholder('https://facebook.com/votrepage')
                                            ->prefixIcon('heroicon-o-link')
                                            ->helperText('URL complète de votre page Facebook'),
                                        Forms\Components\TextInput::make('instagram')
                                            ->label('Instagram')
                                            ->url()
                                            ->placeholder('https://instagram.com/votrecompte')
                                            ->prefixIcon('heroicon-o-link')
                                            ->helperText('URL complète de votre profil Instagram'),
                                        Forms\Components\TextInput::make('tiktok')
                                            ->label('TikTok')
                                            ->url()
                                            ->placeholder('https://www.tiktok.com/@votrecompte')
                                            ->prefixIcon('heroicon-o-link')
                                            ->helperText('URL complète de votre profil TikTok'),
                                        Forms\Components\TextInput::make('google_reviews_url')
                                            ->label('Avis Google (lien)')
                                            ->url()
                                            ->placeholder('https://g.page/r/...')
                                            ->prefixIcon('heroicon-o-link')
                                            ->helperText('Lien vers votre fiche/avis Google. Affiche le badge Avis Google dans le header.'),
                                        Forms\Components\TextInput::make('google_rating')
                                            ->label('Note Google')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(5)
                                            ->step(0.1)
                                            ->placeholder('4.5')
                                            ->helperText('Note affichée en étoiles dans le header (ex : 4.5)'),
                                        Forms\Components\TextInput::make('whatsapp')
                                            ->label('WhatsApp')
                                            ->tel()
                                            ->placeholder('+213540565001')
                                            ->prefixIcon('heroicon-o-phone')
                                            ->helperText('Numéro WhatsApp avec indicatif pays (+213...)'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Company Info
        Setting::set('company_name', $data['company_name'], 'company', 'text');
        Setting::set('company_tagline', $data['company_tagline'], 'company', 'text');
        Setting::set('company_country', $data['company_country'], 'company', 'text');
        Setting::set('company_email', $data['company_email'], 'company', 'text');
        Setting::set('company_phone', $data['company_phone'], 'company', 'text');
        Setting::set('company_address', $data['company_address'], 'company', 'text');

        // Commission & Pricing - Loueurs (taux dégressifs)
        Setting::set('commission_rate_1_to_10_days', $data['commission_rate_1_to_10_days'], 'pricing', 'number');
        Setting::set('commission_rate_11_plus_days', $data['commission_rate_11_plus_days'], 'pricing', 'number');

        // Commission & Pricing - Chauffeurs/Taxis (taux fixe 10%)
        Setting::set('commission_rate_transfer', $data['commission_rate_transfer'], 'pricing', 'number');
        Setting::set('commission_rate_delivery', $data['commission_rate_delivery'], 'pricing', 'number');

        Setting::set('dzd_to_usd_rate', $data['dzd_to_usd_rate'], 'pricing', 'number');
        Setting::set('dzd_to_eur_rate', $data['dzd_to_eur_rate'], 'pricing', 'number');
        Setting::set('min_paypal_amount_usd', $data['min_paypal_amount_usd'], 'pricing', 'number');

        // Booking Settings
        Setting::set('operating_hours_start', $data['operating_hours_start'], 'booking', 'number');
        Setting::set('operating_hours_end', $data['operating_hours_end'], 'booking', 'number');
        Setting::set('default_return_margin_hours', $data['default_return_margin_hours'], 'booking', 'number');
        Setting::set('min_return_hour', $data['min_return_hour'], 'booking', 'number');
        Setting::set('max_return_hour', $data['max_return_hour'], 'booking', 'number');
        Setting::set('weekend_days', $data['weekend_days'], 'booking', 'json');

        // Invoice Settings
        Setting::set('invoice_due_days', $data['invoice_due_days'], 'invoice', 'number');
        Setting::set('commission_invoice_due_days', $data['commission_invoice_due_days'], 'invoice', 'number');
        Setting::set('invoice_prefix', $data['invoice_prefix'], 'invoice', 'text');
        Setting::set('booking_reference_prefix', $data['booking_reference_prefix'], 'invoice', 'text');
        Setting::set('calendar_past_months', $data['calendar_past_months'], 'calendar', 'number');
        Setting::set('calendar_future_months', $data['calendar_future_months'], 'calendar', 'number');

        // Content
        Setting::set('hero_title_line1', $data['hero_title_line1'], 'content', 'text');
        Setting::set('hero_title_line2', $data['hero_title_line2'], 'content', 'text');
        Setting::set('hero_subtitle', $data['hero_subtitle'], 'content', 'text');
        Setting::set('hero_wilayas_count', $data['hero_wilayas_count'], 'content', 'number');
        Setting::set('how_it_works_title', $data['how_it_works_title'], 'content', 'text');
        Setting::set('step1_title', $data['step1_title'], 'content', 'text');
        Setting::set('step1_description', $data['step1_description'], 'content', 'text');
        Setting::set('step2_title', $data['step2_title'], 'content', 'text');
        Setting::set('step2_description', $data['step2_description'], 'content', 'text');
        Setting::set('step3_title', $data['step3_title'], 'content', 'text');
        Setting::set('step3_description', $data['step3_description'], 'content', 'text');
        Setting::set('featured_section_title', $data['featured_section_title'], 'content', 'text');
        Setting::set('categories_section_title', $data['categories_section_title'], 'content', 'text');
        Setting::set('loueurs_section_title', $data['loueurs_section_title'], 'content', 'text');

        // Trial
        Setting::set('trial_days', $data['trial_days'], 'trial', 'number');

        // Social Media
        Setting::set('facebook', $data['facebook'] ?? '', 'social', 'text');
        Setting::set('instagram', $data['instagram'] ?? '', 'social', 'text');
        Setting::set('tiktok', $data['tiktok'] ?? '', 'social', 'text');
        Setting::set('google_reviews_url', $data['google_reviews_url'] ?? '', 'social', 'text');
        Setting::set('tutorial_calendar_video', $data['tutorial_calendar_video'] ?? '', 'tutorials', 'text');
        Setting::set('google_rating', $data['google_rating'] ?? 4.5, 'social', 'text');
        Setting::set('whatsapp', $data['whatsapp'] ?? '', 'contact', 'text');

        // Logos & Favicon
        Setting::set('studio_visual_priority', $data['studio_visual_priority'] ?? false, 'appearance', 'boolean');
        Setting::set('logo_light', $data['logo_light'] ?? '', 'appearance', 'text');
        Setting::set('logo_dark', $data['logo_dark'] ?? '', 'appearance', 'text');
        Setting::set('favicon', $data['favicon'] ?? '', 'appearance', 'text');

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();
    }
}

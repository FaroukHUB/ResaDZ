<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LeadCaptureSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static string $view = 'filament.admin.pages.lead-capture-settings';

    protected static ?string $navigationLabel = 'Popup Newsletter';

    protected static ?string $title = 'Configuration Popup Newsletter';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'enabled' => Setting::get('lead_capture_enabled', true),
            'delay_seconds' => Setting::get('lead_capture_delay', 15),
            'frequency' => Setting::get('lead_capture_frequency', 'once_per_session'),
            'emoji' => Setting::get('lead_capture_emoji', '🎉'),
            'title' => Setting::get('lead_capture_title', 'Ne ratez plus aucune offre !'),
            'subtitle' => Setting::get('lead_capture_subtitle', 'Rejoignez notre communauté pour des promos exclusives'),
            'placeholder' => Setting::get('lead_capture_placeholder', 'Votre email'),
            'button_text' => Setting::get('lead_capture_button_text', 'OK'),
            'dismiss_text' => Setting::get('lead_capture_dismiss_text', 'Ne plus afficher'),
            'success_message' => Setting::get('lead_capture_success_message', 'Inscription réussie !'),
            'header_gradient_from' => Setting::get('lead_capture_gradient_from', '#dc2626'),
            'header_gradient_to' => Setting::get('lead_capture_gradient_to', '#ef4444'),
            'button_color' => Setting::get('lead_capture_button_color', '#dc2626'),
            'button_hover_color' => Setting::get('lead_capture_button_hover_color', '#b91c1c'),
            'show_social_channels' => Setting::get('lead_capture_show_social', true),
            'pages' => Setting::get('lead_capture_pages', ['home', 'vehicles', 'vehicle_detail']),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Général')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Toggle::make('enabled')
                                    ->label('Activer la popup')
                                    ->helperText('Afficher la popup newsletter aux visiteurs')
                                    ->default(true),

                                Forms\Components\TextInput::make('delay_seconds')
                                    ->label('Délai d\'affichage (secondes)')
                                    ->numeric()
                                    ->default(15)
                                    ->minValue(0)
                                    ->maxValue(120)
                                    ->suffix('secondes'),

                                Forms\Components\Select::make('frequency')
                                    ->label('Fréquence d\'affichage')
                                    ->options([
                                        'always' => 'À chaque visite',
                                        'once_per_session' => 'Une fois par session',
                                        'once_per_day' => 'Une fois par jour',
                                        'once_ever' => 'Une seule fois (jamais réafficher)',
                                    ])
                                    ->default('once_per_session'),

                                Forms\Components\CheckboxList::make('pages')
                                    ->label('Afficher sur les pages')
                                    ->options([
                                        'home' => 'Page d\'accueil',
                                        'vehicles' => 'Liste des véhicules',
                                        'vehicle_detail' => 'Détail véhicule',
                                        'loueur' => 'Page loueur',
                                        'booking' => 'Page de réservation',
                                    ])
                                    ->default(['home', 'vehicles', 'vehicle_detail'])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contenu')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Forms\Components\TextInput::make('emoji')
                                    ->label('Emoji')
                                    ->default('🎉')
                                    ->maxLength(10),

                                Forms\Components\TextInput::make('title')
                                    ->label('Titre')
                                    ->default('Ne ratez plus aucune offre !')
                                    ->maxLength(100)
                                    ->required(),

                                Forms\Components\TextInput::make('subtitle')
                                    ->label('Sous-titre')
                                    ->default('Rejoignez notre communauté pour des promos exclusives')
                                    ->maxLength(200),

                                Forms\Components\TextInput::make('placeholder')
                                    ->label('Placeholder email')
                                    ->default('Votre email')
                                    ->maxLength(50),

                                Forms\Components\TextInput::make('button_text')
                                    ->label('Texte du bouton')
                                    ->default('OK')
                                    ->maxLength(30),

                                Forms\Components\TextInput::make('dismiss_text')
                                    ->label('Texte "Ne plus afficher"')
                                    ->default('Ne plus afficher')
                                    ->maxLength(50),

                                Forms\Components\TextInput::make('success_message')
                                    ->label('Message de succès')
                                    ->default('Inscription réussie !')
                                    ->maxLength(100),

                                Forms\Components\Toggle::make('show_social_channels')
                                    ->label('Afficher les réseaux sociaux')
                                    ->helperText('Affiche les liens vers vos réseaux sociaux configurés')
                                    ->default(true),
                            ]),

                        Forms\Components\Tabs\Tab::make('Couleurs')
                            ->icon('heroicon-o-swatch')
                            ->schema([
                                Forms\Components\Section::make('Header')
                                    ->description('Couleurs du gradient de l\'en-tête')
                                    ->schema([
                                        Forms\Components\ColorPicker::make('header_gradient_from')
                                            ->label('Couleur début gradient')
                                            ->default('#dc2626'),

                                        Forms\Components\ColorPicker::make('header_gradient_to')
                                            ->label('Couleur fin gradient')
                                            ->default('#ef4444'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Bouton')
                                    ->description('Couleurs du bouton d\'inscription')
                                    ->schema([
                                        Forms\Components\ColorPicker::make('button_color')
                                            ->label('Couleur du bouton')
                                            ->default('#dc2626'),

                                        Forms\Components\ColorPicker::make('button_hover_color')
                                            ->label('Couleur au survol')
                                            ->default('#b91c1c'),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('lead_capture_enabled', $data['enabled'], 'lead_capture', 'boolean');
        Setting::set('lead_capture_delay', $data['delay_seconds'], 'lead_capture', 'number');
        Setting::set('lead_capture_frequency', $data['frequency'], 'lead_capture', 'text');
        Setting::set('lead_capture_emoji', $data['emoji'], 'lead_capture', 'text');
        Setting::set('lead_capture_title', $data['title'], 'lead_capture', 'text');
        Setting::set('lead_capture_subtitle', $data['subtitle'], 'lead_capture', 'text');
        Setting::set('lead_capture_placeholder', $data['placeholder'], 'lead_capture', 'text');
        Setting::set('lead_capture_button_text', $data['button_text'], 'lead_capture', 'text');
        Setting::set('lead_capture_dismiss_text', $data['dismiss_text'], 'lead_capture', 'text');
        Setting::set('lead_capture_success_message', $data['success_message'], 'lead_capture', 'text');
        Setting::set('lead_capture_gradient_from', $data['header_gradient_from'], 'lead_capture', 'text');
        Setting::set('lead_capture_gradient_to', $data['header_gradient_to'], 'lead_capture', 'text');
        Setting::set('lead_capture_button_color', $data['button_color'], 'lead_capture', 'text');
        Setting::set('lead_capture_button_hover_color', $data['button_hover_color'], 'lead_capture', 'text');
        Setting::set('lead_capture_show_social', $data['show_social_channels'], 'lead_capture', 'boolean');
        Setting::set('lead_capture_pages', $data['pages'], 'lead_capture', 'json');

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Enregistrer')
                ->submit('save'),
        ];
    }
}

<?php

namespace App\Filament\Loueur\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ConditionsLocation extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Conditions de location';

    protected static ?string $title = 'Conditions de location';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.loueur.pages.conditions-location';

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
                'conditions_pdf' => $loueur->getSetting('conditions_pdf', null),
                'rental_conditions' => $loueur->getSetting('rental_conditions', []),
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Conditions de location')
                    ->description('Cochez les conditions qui s\'appliquent à votre agence. Elles seront affichées aux clients lors de la réservation.')
                    ->schema([
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
                                    ->required(),
                            ])
                            ->collapsible()
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter une condition personnalisée'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $loueur = Auth::user()->loueur;
        if (!$loueur) return;

        $data = $this->form->getState();

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
        $loueur->setSetting('conditions_pdf', $data['conditions_pdf'] ?? null, 'string');
        $loueur->setSetting('rental_conditions', $data['rental_conditions'] ?? [], 'json');

        Notification::make()
            ->title('Conditions enregistrées')
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

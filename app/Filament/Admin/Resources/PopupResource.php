<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PopupResource\Pages;
use App\Models\Popup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PopupResource extends Resource
{
    protected static ?string $model = Popup::class;

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $navigationLabel = 'Popups';

    protected static ?string $modelLabel = 'Popup';

    protected static ?string $pluralModelLabel = 'Popups';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Popup')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Contenu')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom interne')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Pour vous repérer dans l\'admin'),

                                Forms\Components\TextInput::make('title')
                                    ->label('Titre affiché')
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('content')
                                    ->label('Contenu / Message')
                                    ->rows(4)
                                    ->helperText('Texte affiché dans le popup'),

                                Forms\Components\FileUpload::make('image')
                                    ->label('Image')
                                    ->image()
                                    ->directory('popups')
                                    ->imageResizeMode('contain')
                                    ->imageCropAspectRatio('16:9')
                                    ->helperText('Format recommandé: 16:9 (800x450px)'),

                                Forms\Components\Section::make('Bouton d\'action')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('button_text')
                                                    ->label('Texte du bouton')
                                                    ->placeholder('Ex: Voir l\'offre'),
                                                Forms\Components\TextInput::make('button_url')
                                                    ->label('Lien du bouton')
                                                    ->url()
                                                    ->placeholder('https://...'),
                                            ]),
                                        Forms\Components\ColorPicker::make('button_color')
                                            ->label('Couleur du bouton')
                                            ->default('#dc2626'),
                                    ])
                                    ->collapsible(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Apparence')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('size')
                                            ->label('Taille')
                                            ->options([
                                                'small' => 'Petit (384px)',
                                                'medium' => 'Moyen (512px)',
                                                'large' => 'Grand (672px)',
                                            ])
                                            ->default('medium'),

                                        Forms\Components\Select::make('position')
                                            ->label('Position')
                                            ->options([
                                                'center' => 'Centre',
                                                'bottom-right' => 'Bas droite',
                                                'bottom-left' => 'Bas gauche',
                                            ])
                                            ->default('center'),

                                        Forms\Components\ColorPicker::make('background_color')
                                            ->label('Couleur de fond')
                                            ->default('#ffffff'),

                                        Forms\Components\ColorPicker::make('text_color')
                                            ->label('Couleur du texte')
                                            ->default('#1f2937'),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('show_overlay')
                                            ->label('Fond sombre')
                                            ->helperText('Assombrir l\'arrière-plan')
                                            ->default(true),

                                        Forms\Components\Toggle::make('closable')
                                            ->label('Bouton fermer')
                                            ->helperText('Permettre de fermer le popup')
                                            ->default(true),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Règles d\'affichage')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Actif')
                                    ->helperText('Activer/désactiver ce popup')
                                    ->default(false),

                                Forms\Components\Section::make('Période')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\DateTimePicker::make('starts_at')
                                                    ->label('Date de début')
                                                    ->helperText('Laisser vide = immédiat'),

                                                Forms\Components\DateTimePicker::make('ends_at')
                                                    ->label('Date de fin')
                                                    ->helperText('Laisser vide = pas de fin'),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Fréquence')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('frequency')
                                                    ->label('Afficher le popup')
                                                    ->options([
                                                        'always' => 'À chaque visite',
                                                        'once_per_session' => '1 fois par session',
                                                        'once_per_day' => '1 fois par jour',
                                                        'once_ever' => '1 seule fois',
                                                    ])
                                                    ->default('once_per_session'),

                                                Forms\Components\TextInput::make('delay_seconds')
                                                    ->label('Délai avant affichage')
                                                    ->numeric()
                                                    ->suffix('secondes')
                                                    ->default(2)
                                                    ->minValue(0)
                                                    ->maxValue(60),
                                            ]),

                                        Forms\Components\TextInput::make('priority')
                                            ->label('Priorité')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Plus élevé = affiché en premier si plusieurs popups'),
                                    ]),

                                Forms\Components\Section::make('Ciblage')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('show_on_pages')
                                            ->label('Pages où afficher')
                                            ->options([
                                                'home' => 'Page d\'accueil',
                                                'vehicles_list' => 'Liste des véhicules',
                                                'vehicle' => 'Détail véhicule',
                                                'loueur' => 'Page loueur',
                                                'booking' => 'Page réservation',
                                            ])
                                            ->columns(3)
                                            ->helperText('Laisser vide = toutes les pages'),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Toggle::make('show_on_mobile')
                                                    ->label('Afficher sur mobile')
                                                    ->default(true),

                                                Forms\Components\Toggle::make('show_on_desktop')
                                                    ->label('Afficher sur desktop')
                                                    ->default(true),
                                            ]),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Statistiques')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Placeholder::make('views_count_display')
                                    ->label('Nombre de vues')
                                    ->content(fn (?Popup $record) => $record ? number_format($record->views_count) : '0'),

                                Forms\Components\Placeholder::make('clicks_count_display')
                                    ->label('Nombre de clics')
                                    ->content(fn (?Popup $record) => $record ? number_format($record->clicks_count) : '0'),

                                Forms\Components\Placeholder::make('ctr_display')
                                    ->label('Taux de clic (CTR)')
                                    ->content(fn (?Popup $record) => $record ? $record->ctr . '%' : '0%'),
                            ])
                            ->visible(fn (?Popup $record) => $record !== null),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->height(40)
                    ->width(70),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->limit(30)
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('frequency')
                    ->label('Fréquence')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'always' => 'Toujours',
                        'once_per_session' => '1x/session',
                        'once_per_day' => '1x/jour',
                        'once_ever' => '1x total',
                        default => $state,
                    })
                    ->color('gray'),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Immédiat')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sans fin')
                    ->sortable(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Vues')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Clics')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('ctr')
                    ->label('CTR')
                    ->suffix('%')
                    ->color(fn (Popup $record) => $record->ctr > 5 ? 'success' : ($record->ctr > 2 ? 'warning' : 'gray')),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priorité')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),

                Tables\Filters\Filter::make('currently_active')
                    ->label('Actuellement visible')
                    ->query(fn ($query) => $query->active()),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle')
                    ->label(fn (Popup $record) => $record->is_active ? 'Désactiver' : 'Activer')
                    ->icon(fn (Popup $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn (Popup $record) => $record->is_active ? 'warning' : 'success')
                    ->action(fn (Popup $record) => $record->update(['is_active' => !$record->is_active])),

                Tables\Actions\Action::make('resetStats')
                    ->label('Reset stats')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(fn (Popup $record) => $record->update(['views_count' => 0, 'clicks_count' => 0])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('priority', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPopups::route('/'),
            'create' => Pages\CreatePopup::route('/create'),
            'edit' => Pages\EditPopup::route('/{record}/edit'),
        ];
    }
}

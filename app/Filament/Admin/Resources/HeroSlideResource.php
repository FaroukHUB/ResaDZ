<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HeroSlideResource\Pages;
use App\Models\HeroSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $navigationLabel = 'Hero Slider';

    protected static ?string $modelLabel = 'Slide';

    protected static ?string $pluralModelLabel = 'Hero Slides';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Image')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image du slide')
                            ->image()
                            ->required()
                            ->directory('hero-slides')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->helperText('Format recommandé: 1920x1080px (16:9)'),
                    ]),

                Forms\Components\Section::make('Contenu (optionnel)')
                    ->description('Texte affiché sur l\'image')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->maxLength(255)
                            ->placeholder('Ex: Louez votre voiture'),
                        Forms\Components\TextInput::make('subtitle')
                            ->label('Sous-titre')
                            ->maxLength(255)
                            ->placeholder('Ex: partout en Algérie'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('button_text')
                                    ->label('Texte du bouton')
                                    ->placeholder('Ex: Voir les offres'),
                                Forms\Components\TextInput::make('button_url')
                                    ->label('URL du bouton')
                                    ->placeholder('Ex: /vehicules'),
                            ]),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Paramètres')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->label('Ordre d\'affichage')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Plus petit = affiché en premier'),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Actif')
                                    ->default(true)
                                    ->helperText('Afficher ce slide sur le site'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->height(60)
                    ->width(100),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->placeholder('(Sans titre)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('Sous-titre')
                    ->placeholder('-')
                    ->limit(30),
                Tables\Columns\TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit' => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }
}

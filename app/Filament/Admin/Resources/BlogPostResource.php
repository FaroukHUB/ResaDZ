<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use FilamentTiptapEditor\TiptapEditor;
use FilamentTiptapEditor\Enums\TiptapOutput;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    // Désactive les vérifications d'autorisation (pas de Policy)
    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return true;
    }

    public static function canView($record): bool
    {
        return true;
    }

    protected static ?string $navigationLabel = 'Actualités';

    protected static ?string $modelLabel = 'Article';

    protected static ?string $pluralModelLabel = 'Articles';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        // Main content - 2 columns
                        Forms\Components\Section::make('Contenu')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Titre')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) =>
                                        $set('slug', Str::slug($state))
                                    ),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug (URL)')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\Textarea::make('excerpt')
                                    ->label('Extrait')
                                    ->helperText('Court résumé affiché sur la carte (max 200 caractères)')
                                    ->maxLength(200)
                                    ->rows(3),

                                TiptapEditor::make('content')
                                    ->label('Contenu de l\'article')
                                    ->required()
                                    ->profile('default')
                                    ->tools([
                                        'heading',
                                        'bullet-list',
                                        'ordered-list',
                                        'blockquote',
                                        'hr',
                                        'bold',
                                        'italic',
                                        'strike',
                                        'underline',
                                        'superscript',
                                        'subscript',
                                        'lead',
                                        'small',
                                        'link',
                                        'media',
                                        'table',
                                        'align-left',
                                        'align-center',
                                        'align-right',
                                        'align-justify',
                                        'code',
                                        'code-block',
                                        'source',
                                        'undo',
                                        'redo',
                                    ])
                                    ->output(TiptapOutput::Html)
                                    ->maxContentWidth('5xl')
                                    ->extraInputAttributes(['style' => 'min-height: 500px;'])
                                    ->disk('public')
                                    ->directory('blog-images')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                                    ->maxFileSize(5120)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(2),

                        // Sidebar - 1 column
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make('Publication')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_published')
                                            ->label('Publié')
                                            ->default(false),

                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Mis en avant (homepage)')
                                            ->default(false),

                                        Forms\Components\DateTimePicker::make('published_at')
                                            ->label('Date de publication')
                                            ->default(now()),
                                    ]),

                                Forms\Components\Section::make('Image & Badges')
                                    ->schema([
                                        Forms\Components\FileUpload::make('featured_image')
                                            ->label('Image de couverture')
                                            ->image()
                                            ->disk('public')
                                            ->directory('blog')
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9')
                                            ->imageResizeTargetWidth('1200')
                                            ->imageResizeTargetHeight('675'),

                                        Forms\Components\TextInput::make('badge_text')
                                            ->label('Texte badge (petit)')
                                            ->placeholder('EX: JUSQU\'À')
                                            ->maxLength(50),

                                        Forms\Components\TextInput::make('badge_value')
                                            ->label('Valeur badge (gros)')
                                            ->placeholder('EX: 15% DE REMISE')
                                            ->maxLength(50),
                                    ]),

                                Forms\Components\Section::make('Call to Action')
                                    ->schema([
                                        Forms\Components\TextInput::make('cta_text')
                                            ->label('Texte du bouton')
                                            ->placeholder('Réserver')
                                            ->maxLength(50),

                                        Forms\Components\TextInput::make('cta_link')
                                            ->label('Lien du bouton')
                                            ->placeholder('/vehicles')
                                            ->maxLength(255),
                                    ]),

                                Forms\Components\Section::make('Catégorie & SEO')
                                    ->schema([
                                        Forms\Components\Select::make('category')
                                            ->label('Catégorie')
                                            ->options([
                                                'promo' => 'Promotions',
                                                'guide' => 'Guides & Conseils',
                                                'news' => 'Actualités',
                                                'destination' => 'Destinations',
                                            ]),

                                        Forms\Components\TagsInput::make('tags')
                                            ->label('Tags'),

                                        Forms\Components\TextInput::make('meta_title')
                                            ->label('Meta Title')
                                            ->maxLength(70),

                                        Forms\Components\Textarea::make('meta_description')
                                            ->label('Meta Description')
                                            ->maxLength(160)
                                            ->rows(2),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->disk('public')
                    ->circular(false)
                    ->width(80)
                    ->height(45),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'promo' => 'warning',
                        'guide' => 'info',
                        'news' => 'success',
                        'destination' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publié')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Vedette')
                    ->boolean(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Vues')
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publié'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Mis en avant'),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options([
                        'promo' => 'Promotions',
                        'guide' => 'Guides & Conseils',
                        'news' => 'Actualités',
                        'destination' => 'Destinations',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_published', true)->count() ?: null;
    }
}

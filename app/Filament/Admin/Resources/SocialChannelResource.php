<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SocialChannelResource\Pages;
use App\Models\SocialChannel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SocialChannelResource extends Resource
{
    protected static ?string $model = SocialChannel::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Canaux Sociaux';

    protected static ?string $modelLabel = 'Canal Social';

    protected static ?string $pluralModelLabel = 'Canaux Sociaux';

    protected static ?int $navigationSort = 25;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Groupe WhatsApp Bons Plans'),

                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'whatsapp' => 'WhatsApp',
                                'telegram' => 'Telegram',
                                'facebook' => 'Facebook',
                                'instagram' => 'Instagram',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('url')
                            ->label('Lien d\'invitation')
                            ->url()
                            ->required()
                            ->placeholder('https://chat.whatsapp.com/...')
                            ->helperText('Lien pour rejoindre le groupe/canal'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->placeholder('Description affichée aux visiteurs'),

                        Forms\Components\FileUpload::make('icon')
                            ->label('Icône personnalisée')
                            ->image()
                            ->directory('social-channels')
                            ->helperText('Optionnel - sinon icône par défaut'),
                    ]),

                Forms\Components\Section::make('Paramètres')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('member_count')
                                    ->label('Nombre de membres')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Pour affichage'),

                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Ordre')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Actif')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => match ($record->type) {
                        'whatsapp' => 'https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg',
                        'telegram' => 'https://upload.wikimedia.org/wikipedia/commons/8/82/Telegram_logo.svg',
                        default => null,
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'success' => 'whatsapp',
                        'info' => 'telegram',
                        'primary' => 'facebook',
                        'danger' => 'instagram',
                    ]),

                Tables\Columns\TextColumn::make('url')
                    ->label('Lien')
                    ->limit(30)
                    ->copyable()
                    ->url(fn ($record) => $record->url, true),

                Tables\Columns\TextColumn::make('member_count')
                    ->label('Membres')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'telegram' => 'Telegram',
                        'facebook' => 'Facebook',
                        'instagram' => 'Instagram',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),
            ])
            ->actions([
                Tables\Actions\Action::make('copyLink')
                    ->label('Copier')
                    ->icon('heroicon-o-clipboard')
                    ->action(fn () => null)
                    ->extraAttributes(fn ($record) => [
                        'x-data' => '{}',
                        'x-on:click' => "navigator.clipboard.writeText('{$record->url}'); \$tooltip('Copié!')",
                    ]),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialChannels::route('/'),
            'create' => Pages\CreateSocialChannel::route('/create'),
            'edit' => Pages\EditSocialChannel::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LoueurResource\Pages;
use App\Models\Loueur;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LoueurResource extends Resource
{
    protected static ?string $model = Loueur::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Gestion';

    protected static ?string $navigationLabel = 'Loueurs';

    protected static ?string $modelLabel = 'Loueur';

    protected static ?string $pluralModelLabel = 'Loueurs';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Loueur')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informations')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Utilisateur')
                                    ->options(User::where('role', 'loueur')
                                        ->orWhereDoesntHave('loueur')
                                        ->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->helperText('L\'utilisateur associé à ce loueur'),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('company_name')
                                            ->label('Nom de l\'agence')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state))),
                                        Forms\Components\TextInput::make('subdomain')
                                            ->label('Sous-domaine')
                                            ->prefix('https://')
                                            ->suffix('.resadz.dz')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Ex: sayara → sayara.resadz.dz'),
                                    ]),
                                Forms\Components\Hidden::make('slug'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3),
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
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('facebook')
                                            ->label('Facebook'),
                                        Forms\Components\TextInput::make('instagram')
                                            ->label('Instagram'),
                                        Forms\Components\TextInput::make('tiktok')
                                            ->label('TikTok'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Paiements')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\CheckboxList::make('payment_methods')
                                    ->label('Méthodes acceptées')
                                    ->options([
                                        'cash' => 'Espèces',
                                        'cib' => 'CIB',
                                        'dahabia' => 'Dahabia',
                                        'baridimob' => 'BaridiMob',
                                        'paypal' => 'PayPal',
                                        'bank_transfer' => 'Virement',
                                        'wise' => 'Wise',
                                    ])
                                    ->columns(3),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('paypal_email')
                                            ->label('Email PayPal'),
                                        Forms\Components\TextInput::make('iban')
                                            ->label('IBAN'),
                                        Forms\Components\TextInput::make('wise_email')
                                            ->label('Email Wise'),
                                        Forms\Components\TextInput::make('baridimob_rip')
                                            ->label('RIP BaridiMob'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Statut')
                            ->icon('heroicon-o-check-badge')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Actif')
                                            ->default(true)
                                            ->helperText('Le loueur peut accéder à son panel'),
                                        Forms\Components\Toggle::make('is_verified')
                                            ->label('Vérifié')
                                            ->helperText('Badge de confiance'),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('subscription_plan')
                                            ->label('Abonnement')
                                            ->options([
                                                'free' => 'Gratuit',
                                                'basic' => 'Basic',
                                                'pro' => 'Pro',
                                                'enterprise' => 'Enterprise',
                                            ])
                                            ->default('free'),
                                        Forms\Components\DatePicker::make('subscription_expires_at')
                                            ->label('Expiration abonnement'),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Images')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('logo')
                                    ->label('Logo')
                                    ->image()
                                    ->directory('loueurs/logos')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('200')
                                    ->imageResizeTargetHeight('200'),
                                Forms\Components\FileUpload::make('cover_image')
                                    ->label('Image de couverture')
                                    ->image()
                                    ->directory('loueurs/covers')
                                    ->imageResizeMode('cover')
                                    ->imageResizeTargetWidth('1200')
                                    ->imageResizeTargetHeight('400'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Agence')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subdomain')
                    ->label('Sous-domaine')
                    ->formatStateUsing(fn ($state) => $state . '.resadz.dz')
                    ->copyable()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Propriétaire')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ville')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('vehicles_count')
                    ->label('Véhicules')
                    ->counts('vehicles')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Réservations')
                    ->counts('bookings')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Vérifié')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Vérifié'),
                Tables\Filters\SelectFilter::make('subscription_plan')
                    ->label('Abonnement')
                    ->options([
                        'free' => 'Gratuit',
                        'basic' => 'Basic',
                        'pro' => 'Pro',
                        'enterprise' => 'Enterprise',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListLoueurs::route('/'),
            'create' => Pages\CreateLoueur::route('/create'),
            'view' => Pages\ViewLoueur::route('/{record}'),
            'edit' => Pages\EditLoueur::route('/{record}/edit'),
        ];
    }
}

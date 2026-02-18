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
use Filament\Infolists;
use Filament\Infolists\Infolist;
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
                        Forms\Components\Tabs\Tab::make('Compte & Agence')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Section::make('Identifiants de connexion')
                                    ->description('Ces identifiants permettront au loueur de se connecter à son dashboard')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('user_email')
                                                    ->label('Email')
                                                    ->email()
                                                    ->required()
                                                    ->unique('users', 'email', ignoreRecord: true, modifyRuleUsing: function ($rule, $record) {
                                                        return $record ? $rule->ignore($record->user_id) : $rule;
                                                    })
                                                    ->dehydrated(false)
                                                    ->afterStateHydrated(function ($component, $record) {
                                                        if ($record && $record->user) {
                                                            $component->state($record->user->email);
                                                        }
                                                    }),
                                                Forms\Components\TextInput::make('user_password')
                                                    ->label('Mot de passe')
                                                    ->password()
                                                    ->dehydrated(false)
                                                    ->required(fn ($record) => $record === null)
                                                    ->helperText(fn ($record) => $record ? 'Laissez vide pour ne pas changer' : 'Min. 8 caractères'),
                                            ]),
                                    ])
                                    ->collapsible(),

                                Forms\Components\Section::make('Informations de l\'agence')
                                    ->schema([
                                        Forms\Components\TextInput::make('company_name')
                                            ->label('Nom de l\'agence')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($set, $state) => $set('slug', Str::slug($state))),
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
                                    ->label('Email de contact (public)')
                                    ->email()
                                    ->helperText('Email affiché sur le site (peut être différent de l\'email de connexion)'),
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
                            ]),

                        Forms\Components\Tabs\Tab::make('Statut')
                            ->icon('heroicon-o-check-badge')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Actif')
                                            ->default(true)
                                            ->helperText('Le loueur peut accéder à son dashboard'),
                                        Forms\Components\Toggle::make('is_verified')
                                            ->label('Vérifié')
                                            ->helperText('Badge de confiance affiché sur le site'),
                                        Forms\Components\Toggle::make('is_suspended')
                                            ->label('Suspendu')
                                            ->helperText('Bloque l\'accès au compte'),
                                    ]),
                                Forms\Components\Textarea::make('suspension_reason')
                                    ->label('Raison de suspension')
                                    ->visible(fn ($get) => $get('is_suspended'))
                                    ->rows(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Essai & Commission')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Section::make('Période d\'essai')
                                    ->description('1 mois gratuit par défaut, renouvelable')
                                    ->schema([
                                        Forms\Components\DatePicker::make('trial_ends_at')
                                            ->label('Fin de période d\'essai')
                                            ->default(now()->addMonth())
                                            ->helperText('Pendant l\'essai, aucune commission n\'est prélevée'),
                                    ]),
                                Forms\Components\Section::make('Commission')
                                    ->description('Taux personnalisé (optionnel)')
                                    ->schema([
                                        Forms\Components\TextInput::make('commission_rate')
                                            ->label('Taux de commission')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('%')
                                            ->placeholder(Loueur::DEFAULT_COMMISSION_RATE . '% (par défaut)')
                                            ->helperText('Laissez vide pour utiliser le taux global'),
                                        Forms\Components\DatePicker::make('commission_paid_until')
                                            ->label('Commission payée jusqu\'au'),
                                        Forms\Components\Textarea::make('commission_notes')
                                            ->label('Notes sur les paiements')
                                            ->rows(2),
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
                                    ->directory('loueurs/covers'),
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
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email connexion')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wilaya')
                    ->label('Wilaya')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('vehicles_count')
                    ->label('Véhicules')
                    ->counts('vehicles')
                    ->sortable(),
                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->label('Essai')
                    ->date('d/m/Y')
                    ->color(fn ($record) => $record->isInTrial() ? 'success' : 'warning')
                    ->description(fn ($record) => $record->isInTrial() ? 'En cours' : ($record->trial_ends_at ? 'Expiré' : '-'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Vérifié')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_suspended')
                    ->label('Susp.')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Vérifié'),
                Tables\Filters\TernaryFilter::make('is_suspended')
                    ->label('Suspendu'),
                Tables\Filters\Filter::make('in_trial')
                    ->label('En période d\'essai')
                    ->query(fn ($query) => $query->inTrial()),
                Tables\Filters\Filter::make('trial_expired')
                    ->label('Essai expiré')
                    ->query(fn ($query) => $query->trialExpired()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('loginAs')
                    ->label('Accéder au dashboard')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('success')
                    ->url(fn (Loueur $record) => '/loueur')
                    ->openUrlInNewTab()
                    ->visible(fn (Loueur $record) => $record->is_active),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Accès au Dashboard')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email de connexion')
                            ->copyable()
                            ->icon('heroicon-o-envelope'),
                        Infolists\Components\TextEntry::make('dashboard_url')
                            ->label('URL du dashboard')
                            ->state(fn () => url('/loueur'))
                            ->copyable()
                            ->icon('heroicon-o-link'),
                        Infolists\Components\TextEntry::make('public_url')
                            ->label('Page publique')
                            ->state(fn (Loueur $record) => url('/loueur/' . $record->slug))
                            ->copyable()
                            ->url(fn (Loueur $record) => url('/loueur/' . $record->slug))
                            ->icon('heroicon-o-globe-alt'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Informations')
                    ->schema([
                        Infolists\Components\TextEntry::make('company_name')
                            ->label('Agence'),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('Téléphone'),
                        Infolists\Components\TextEntry::make('wilaya')
                            ->label('Wilaya'),
                        Infolists\Components\TextEntry::make('city')
                            ->label('Ville'),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Actif')
                            ->boolean(),
                        Infolists\Components\IconEntry::make('is_verified')
                            ->label('Vérifié')
                            ->boolean(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Essai & Commission')
                    ->schema([
                        Infolists\Components\TextEntry::make('trial_ends_at')
                            ->label('Fin d\'essai')
                            ->date('d/m/Y')
                            ->color(fn (Loueur $record) => $record->isInTrial() ? 'success' : 'warning')
                            ->badge(),
                        Infolists\Components\TextEntry::make('commission_rate_display')
                            ->label('Taux de commission')
                            ->state(fn (Loueur $record) => $record->getCommissionRate() . '%')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('unpaid_commission')
                            ->label('Commission impayée')
                            ->state(fn (Loueur $record) => number_format($record->getUnpaidCommission(), 0, ',', ' ') . ' DA')
                            ->color(fn (Loueur $record) => $record->getUnpaidCommission() > 0 ? 'danger' : 'success')
                            ->badge(),
                        Infolists\Components\TextEntry::make('commission_paid_until')
                            ->label('Payé jusqu\'au')
                            ->date('d/m/Y')
                            ->placeholder('Non défini'),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Statistiques')
                    ->schema([
                        Infolists\Components\TextEntry::make('vehicles_count')
                            ->label('Véhicules')
                            ->state(fn (Loueur $record) => $record->vehicles()->count()),
                        Infolists\Components\TextEntry::make('bookings_count')
                            ->label('Réservations')
                            ->state(fn (Loueur $record) => $record->bookings()->count()),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Inscrit le')
                            ->date('d/m/Y'),
                    ])
                    ->columns(3),
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

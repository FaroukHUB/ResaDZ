<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LoueurResource\Pages;
use App\Mail\WelcomeChauffeurMail;
use App\Mail\WelcomeLoueurMail;
use App\Models\Loueur;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

    protected static ?string $navigationBadgeTooltip = 'Nombre de loueurs actifs';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Loueur')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Compte & Agence')
                            ->icon('heroicon-o-user')
                            ->badge('Principal')
                            ->badgeColor('primary')
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
                                                    ->label('Ville principale'),
                                                Forms\Components\Select::make('wilayas')
                                                    ->label('Wilayas d\'activité')
                                                    ->multiple()
                                                    ->options(config('resadz.wilayas'))
                                                    ->searchable()
                                                    ->preload()
                                                    ->helperText('Le loueur peut opérer dans plusieurs wilayas')
                                                    ->afterStateHydrated(function ($component, $record) {
                                                        if ($record) {
                                                            $component->state($record->getWilayaCodes());
                                                        }
                                                    })
                                                    ->dehydrated(false),
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
                                        'wise' => 'En ligne (Wise, Revolut...)',
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
                                        Forms\Components\Toggle::make('disponible_national')
                                            ->label('Disponible national')
                                            ->helperText('Apparaît dans toutes les wilayas'),
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
                                    ->description('Offrir une période sans commission (optionnel)')
                                    ->schema([
                                        Forms\Components\DatePicker::make('trial_ends_at')
                                            ->label('Fin de période d\'essai')
                                            ->helperText('Laisser vide si pas de période offerte. Aucune commission prélevée pendant cette période.'),
                                    ]),
                                Forms\Components\Section::make('Commission')
                                    ->description('Suivi des paiements')
                                    ->schema([
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
                Tables\Columns\TextColumn::make('wilayas_display')
                    ->label('Wilayas')
                    ->state(fn (Loueur $record) => $record->getWilayasString() ?: '-')
                    ->wrap()
                    ->limit(30),
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
                Tables\Columns\IconColumn::make('disponible_national')
                    ->label('National')
                    ->boolean()
                    ->trueIcon('heroicon-o-globe-alt')
                    ->trueColor('success')
                    ->falseColor('gray'),
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
                Tables\Filters\TernaryFilter::make('disponible_national')
                    ->label('Disponible national'),
                Tables\Filters\Filter::make('in_trial')
                    ->label('En période d\'essai')
                    ->query(fn ($query) => $query->inTrial()),
                Tables\Filters\Filter::make('trial_expired')
                    ->label('Essai expiré')
                    ->query(fn ($query) => $query->trialExpired()),
            ])
            ->actions([
                Tables\Actions\Action::make('activate')
                    ->label('Accepter')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Activer ce compte')
                    ->modalDescription(fn (Loueur $record) => "Activer le compte de {$record->company_name} et envoyer le mail d'accès ?")
                    ->visible(fn (Loueur $record) => !$record->is_active && !$record->is_suspended)
                    ->action(function (Loueur $record) {
                        $record->update(['is_active' => true]);
                        static::sendActivationEmail($record);
                        Notification::make()
                            ->title('Compte activé')
                            ->body("Le compte de {$record->company_name} a été activé. Mail d'accès envoyé.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Refuser ce compte')
                    ->modalDescription(fn (Loueur $record) => "Refuser et suspendre le compte de {$record->company_name} ?")
                    ->visible(fn (Loueur $record) => !$record->is_active && !$record->is_suspended)
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Raison du refus (optionnel)')
                            ->rows(2),
                    ])
                    ->action(function (Loueur $record, array $data) {
                        $record->update([
                            'is_suspended' => true,
                            'suspension_reason' => $data['reason'] ?? 'Inscription refusée',
                        ]);
                        Notification::make()
                            ->title('Compte refusé')
                            ->body("Le compte de {$record->company_name} a été refusé.")
                            ->danger()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('loginAs')
                    ->label('Accéder au dashboard')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('success')
                    ->url(fn (Loueur $record) => '/loueur')
                    ->openUrlInNewTab()
                    ->visible(fn (Loueur $record) => $record->is_active),
                Tables\Actions\Action::make('sendWelcomeEmail')
                    ->label('Renvoyer mail bienvenue')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Renvoyer le mail de bienvenue')
                    ->modalDescription(fn (Loueur $record) => "Envoyer le mail de bienvenue à {$record->company_name} ({$record->user?->email}) ?")
                    ->action(function (Loueur $record) {
                        static::sendWelcomeEmailTo($record);
                        Notification::make()
                            ->title('Mail envoyé')
                            ->body("Mail de bienvenue envoyé à {$record->user?->email}")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Loueur $record) => $record->user?->email),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('sendWelcomeEmails')
                        ->label('Envoyer mail bienvenue')
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Envoyer les mails de bienvenue')
                        ->modalDescription('Envoyer le mail de bienvenue à tous les loueurs/chauffeurs sélectionnés ?')
                        ->action(function (Collection $records) {
                            $sent = 0;
                            foreach ($records as $record) {
                                if ($record->user?->email) {
                                    static::sendWelcomeEmailTo($record);
                                    $sent++;
                                }
                            }
                            Notification::make()
                                ->title('Mails envoyés')
                                ->body("{$sent} mail(s) de bienvenue envoyé(s)")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
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
                        Infolists\Components\TextEntry::make('wilayas_display')
                            ->label('Wilayas')
                            ->state(fn (Loueur $record) => $record->getWilayasString() ?: '-'),
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

    public static function sendWelcomeEmailTo(Loueur $loueur): void
    {
        try {
            $email = $loueur->user?->email;
            if (!$email) {
                return;
            }

            $mailable = $loueur->account_type === 'taxi'
                ? new WelcomeChauffeurMail($loueur)
                : new WelcomeLoueurMail($loueur);

            Mail::to($email)->send($mailable);
        } catch (\Exception $e) {
            Log::warning('Failed to send welcome email to loueur #' . $loueur->id . ': ' . $e->getMessage());

            Notification::make()
                ->title('Erreur d\'envoi')
                ->body('Impossible d\'envoyer le mail : ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function sendActivationEmail(Loueur $loueur): void
    {
        try {
            $email = $loueur->user?->email;
            if (!$email) return;

            $companyName = \App\Models\Setting::get('company_name', 'ResaDZ');
            $whatsappNumber = \App\Models\Setting::get('whatsapp', '');

            Mail::send('emails.account-activated-loueur', [
                'loueur' => $loueur,
                'companyName' => $companyName,
                'whatsappNumber' => $whatsappNumber,
            ], function ($message) use ($email, $loueur) {
                $message->to($email)
                    ->subject('Votre compte ResaDZ est activé ! 🎉')
                    ->from(
                        config('resadz_emails.inscription.address', config('mail.from.address')),
                        config('resadz_emails.inscription.name', config('mail.from.name'))
                    );
            });
        } catch (\Exception $e) {
            Log::warning('Failed to send activation email to loueur #' . $loueur->id . ': ' . $e->getMessage());
            Notification::make()
                ->title('Erreur d\'envoi')
                ->body('Compte activé mais email non envoyé : ' . $e->getMessage())
                ->warning()
                ->send();
        }
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

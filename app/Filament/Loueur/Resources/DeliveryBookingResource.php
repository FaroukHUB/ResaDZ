<?php

namespace App\Filament\Loueur\Resources;

use App\Filament\Loueur\Resources\DeliveryBookingResource\Pages;
use App\Models\DeliveryBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DeliveryBookingResource extends Resource
{
    protected static ?string $model = DeliveryBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Livraison';

    protected static ?string $navigationLabel = 'Commandes Livraison';

    protected static ?string $modelLabel = 'Livraison';

    protected static ?string $pluralModelLabel = 'Livraisons';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $loueur = Auth::user()->loueur;

        return parent::getEloquentQuery()
            ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id));
    }

    public static function getNavigationBadge(): ?string
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) return null;

        $count = DeliveryBooking::where('loueur_id', $loueur->id)
            ->whereIn('status', ['pending', 'confirmed', 'picked_up', 'in_transit'])
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations de livraison')
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->label('Référence')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'confirmed' => 'Confirmée',
                                'picked_up' => 'Colis récupéré',
                                'in_transit' => 'En cours de livraison',
                                'delivered' => 'Livrée',
                                'cancelled' => 'Annulée',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('tracking_code')
                            ->label('Code de suivi')
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                Forms\Components\Section::make('Adresses')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('pickup_address')
                            ->label('Adresse de récupération')
                            ->required(),

                        Forms\Components\TextInput::make('pickup_city')
                            ->label('Ville de récupération')
                            ->required(),

                        Forms\Components\TextInput::make('delivery_address')
                            ->label('Adresse de livraison')
                            ->required(),

                        Forms\Components\TextInput::make('delivery_city')
                            ->label('Ville de livraison')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Colis')
                    ->icon('heroicon-o-cube')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('package_type')
                            ->label('Type')
                            ->options([
                                'colis' => 'Colis',
                                'document' => 'Document',
                                'repas' => 'Repas',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('weight')
                            ->label('Poids (kg)')
                            ->numeric()
                            ->suffix('kg'),

                        Forms\Components\Textarea::make('package_description')
                            ->label('Description du colis')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('price')
                            ->label('Prix total (DA)')
                            ->numeric()
                            ->suffix('DA'),

                        Forms\Components\TextInput::make('commission_amount')
                            ->label('Commission ResaDZ (10%)')
                            ->disabled()
                            ->dehydrated(false)
                            ->suffix('DA')
                            ->helperText('Commission prélevée sur cette livraison'),

                        Forms\Components\DatePicker::make('pickup_date')
                            ->label('Date de récupération')
                            ->required(),

                        Forms\Components\TextInput::make('pickup_time')
                            ->label('Heure de récupération'),
                    ]),

                Forms\Components\Section::make('Expéditeur')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label('Nom')
                            ->required(),

                        Forms\Components\TextInput::make('client_phone')
                            ->label('Téléphone')
                            ->required(),

                        Forms\Components\TextInput::make('client_email')
                            ->label('Email'),

                        Forms\Components\Textarea::make('client_notes')
                            ->label('Notes'),
                    ]),

                Forms\Components\Section::make('Destinataire')
                    ->icon('heroicon-o-user-plus')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('recipient_name')
                            ->label('Nom du destinataire'),

                        Forms\Components\TextInput::make('recipient_phone')
                            ->label('Téléphone du destinataire'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Réf.')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-document-text'),

                Tables\Columns\TextColumn::make('tracking_code')
                    ->label('Suivi')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-qr-code')
                    ->copyMessage('Code copié !'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'picked_up' => 'info',
                        'in_transit' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'confirmed' => 'heroicon-o-check-circle',
                        'picked_up' => 'heroicon-o-hand-raised',
                        'in_transit' => 'heroicon-o-truck',
                        'delivered' => 'heroicon-o-check-badge',
                        'cancelled' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'picked_up' => 'Récupéré',
                        'in_transit' => 'En transit',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('package_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'colis' => 'primary',
                        'document' => 'info',
                        'repas' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'colis' => 'heroicon-o-cube',
                        'document' => 'heroicon-o-document',
                        'repas' => 'heroicon-o-fire',
                        default => 'heroicon-o-cube',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'colis' => 'Colis',
                        'document' => 'Document',
                        'repas' => 'Repas',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('pickup_city')
                    ->label('Trajet')
                    ->searchable()
                    ->icon('heroicon-o-map-pin')
                    ->description(fn ($record) => '→ ' . $record->delivery_city),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Expéditeur')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->description(fn ($record) => $record->recipient_name ? 'Pour: ' . $record->recipient_name : null),

                Tables\Columns\TextColumn::make('pickup_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->description(fn ($record) => '-' . number_format($record->commission_amount ?? 0, 0, ',', ' ') . ' comm.'),

                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net')
                    ->formatStateUsing(fn ($record) => number_format($record->net_amount ?? 0, 0, ',', ' ') . ' DA')
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('commission_paid')
                    ->label('Comm.')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmée',
                        'picked_up' => 'Récupéré',
                        'in_transit' => 'En transit',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée',
                    ]),
                Tables\Filters\SelectFilter::make('package_type')
                    ->label('Type')
                    ->options([
                        'colis' => 'Colis',
                        'document' => 'Document',
                        'repas' => 'Repas',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Accepter')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->button()
                    ->visible(fn (DeliveryBooking $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Accepter cette livraison ?')
                    ->modalDescription('Le client sera notifié.')
                    ->action(function (DeliveryBooking $record) {
                        $record->update(['status' => 'confirmed', 'confirmed_at' => now()]);
                        $record->addTrackingEvent('confirmed', null, 'Commande confirmée par le chauffeur');
                    }),

                Tables\Actions\Action::make('pickup')
                    ->label('Récupéré')
                    ->icon('heroicon-o-hand-raised')
                    ->color('info')
                    ->button()
                    ->visible(fn (DeliveryBooking $record) => $record->status === 'confirmed')
                    ->form([
                        Forms\Components\TextInput::make('location')
                            ->label('Lieu de récupération')
                            ->placeholder('Ex: Alger centre'),
                    ])
                    ->action(function (DeliveryBooking $record, array $data) {
                        $record->update(['status' => 'picked_up', 'picked_up_at' => now()]);
                        $record->addTrackingEvent('picked_up', $data['location'] ?? null, 'Colis récupéré');
                    }),

                Tables\Actions\Action::make('in_transit')
                    ->label('En route')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->button()
                    ->visible(fn (DeliveryBooking $record) => $record->status === 'picked_up')
                    ->form([
                        Forms\Components\TextInput::make('location')
                            ->label('Position actuelle')
                            ->placeholder('Ex: Autoroute Est-Ouest'),
                    ])
                    ->action(function (DeliveryBooking $record, array $data) {
                        $record->update(['status' => 'in_transit']);
                        $record->addTrackingEvent('in_transit', $data['location'] ?? null, 'En cours de livraison');
                    }),

                Tables\Actions\Action::make('deliver')
                    ->label('Livré')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->button()
                    ->visible(fn (DeliveryBooking $record) => in_array($record->status, ['picked_up', 'in_transit']))
                    ->requiresConfirmation()
                    ->action(function (DeliveryBooking $record) {
                        $record->update(['status' => 'delivered', 'delivered_at' => now()]);
                        $record->addTrackingEvent('delivered', $record->delivery_city, 'Colis livré au destinataire');
                        Notification::make()->title('Livraison terminée !')->success()->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray'),
            ])
            ->striped()
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveryBookings::route('/'),
            'edit' => Pages\EditDeliveryBooking::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) return false;

        // Les taxis utilisent /chauffeur, ici on affiche seulement pour les loueurs qui proposent des livraisons
        return !$loueur->isTaxi() && $loueur->offers_delivery;
    }
}

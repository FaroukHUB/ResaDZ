<?php

namespace App\Filament\Chauffeur\Resources;

use App\Filament\Chauffeur\Resources\DeliveryBookingResource\Pages;
use App\Models\ChauffeurVehicle;
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

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationGroup = 'Livraisons';

    protected static ?string $navigationLabel = 'Mes Livraisons';

    protected static ?string $modelLabel = 'Livraison';

    protected static ?string $pluralModelLabel = 'Livraisons';

    protected static ?int $navigationSort = 1;

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
        $loueur = Auth::user()->loueur;

        return $form
            ->schema([
                Forms\Components\Section::make('Informations de livraison')
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->label('Reference')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'confirmed' => 'Confirmee',
                                'picked_up' => 'Colis recupere',
                                'in_transit' => 'En cours de livraison',
                                'delivered' => 'Livree',
                                'cancelled' => 'Annulee',
                            ])
                            ->required(),

                        Forms\Components\Select::make('chauffeur_vehicle_id')
                            ->label('Vehicule')
                            ->options(fn () => ChauffeurVehicle::where('loueur_id', $loueur->id)
                                ->active()
                                ->get()
                                ->pluck('full_name', 'id'))
                            ->searchable()
                            ->preload(),

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
                            ->label('Adresse de recuperation')
                            ->required(),

                        Forms\Components\TextInput::make('pickup_city')
                            ->label('Ville de recuperation')
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
                            ->helperText('Commission prelevee sur cette livraison'),

                        Forms\Components\DatePicker::make('pickup_date')
                            ->label('Date de recuperation')
                            ->required(),

                        Forms\Components\TextInput::make('pickup_time')
                            ->label('Heure de recuperation'),
                    ]),

                Forms\Components\Section::make('Expediteur')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label('Nom')
                            ->required(),

                        Forms\Components\TextInput::make('client_phone')
                            ->label('Telephone')
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
                            ->label('Telephone du destinataire'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref.')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tracking_code')
                    ->label('Suivi')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'picked_up' => 'info',
                        'in_transit' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmee',
                        'picked_up' => 'Recupere',
                        'in_transit' => 'En transit',
                        'delivered' => 'Livree',
                        'cancelled' => 'Annulee',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('chauffeurVehicle.full_name')
                    ->label('Vehicule')
                    ->placeholder('Non assigne')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('package_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'colis' => 'primary',
                        'document' => 'info',
                        'repas' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'colis' => 'Colis',
                        'document' => 'Document',
                        'repas' => 'Repas',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('pickup_city')
                    ->label('De')
                    ->searchable(),

                Tables\Columns\TextColumn::make('delivery_city')
                    ->label('Vers')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->sortable(),

                Tables\Columns\TextColumn::make('commission_amount')
                    ->label('Commission')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->color('danger')
                    ->description('10%'),

                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net')
                    ->formatStateUsing(fn ($record) => number_format($record->net_amount ?? 0, 0, ',', ' ') . ' DA')
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('commission_paid')
                    ->label('Paye')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('client_name')
                    ->label('Expediteur')
                    ->searchable(),

                Tables\Columns\TextColumn::make('pickup_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creee le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'confirmed' => 'Confirmee',
                        'picked_up' => 'Recupere',
                        'in_transit' => 'En transit',
                        'delivered' => 'Livree',
                        'cancelled' => 'Annulee',
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
                    ->label('Confirmer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (DeliveryBooking $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (DeliveryBooking $record) {
                        $record->update(['status' => 'confirmed', 'confirmed_at' => now()]);
                        $record->addTrackingEvent('confirmed', null, 'Commande confirmee par le chauffeur');
                    }),

                Tables\Actions\Action::make('pickup')
                    ->label('Recupere')
                    ->icon('heroicon-o-cube')
                    ->color('info')
                    ->visible(fn (DeliveryBooking $record) => $record->status === 'confirmed')
                    ->form([
                        Forms\Components\TextInput::make('location')
                            ->label('Lieu de recuperation')
                            ->placeholder('Ex: Alger centre'),
                    ])
                    ->action(function (DeliveryBooking $record, array $data) {
                        $record->update(['status' => 'picked_up', 'picked_up_at' => now()]);
                        $record->addTrackingEvent('picked_up', $data['location'] ?? null, 'Colis recupere');
                    }),

                Tables\Actions\Action::make('in_transit')
                    ->label('En route')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
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
                    ->label('Livre')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (DeliveryBooking $record) => in_array($record->status, ['picked_up', 'in_transit']))
                    ->requiresConfirmation()
                    ->action(function (DeliveryBooking $record) {
                        $record->update(['status' => 'delivered', 'delivered_at' => now()]);
                        $record->addTrackingEvent('delivered', $record->delivery_city, 'Colis livre au destinataire');
                        Notification::make()->title('Livraison terminee !')->success()->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
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
}

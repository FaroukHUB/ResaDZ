<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VehicleBoostResource\Pages;
use App\Models\VehicleBoost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleBoostResource extends Resource
{
    protected static ?string $model = VehicleBoost::class;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $navigationLabel = 'Boosts Véhicules';
    protected static ?string $modelLabel = 'Boost';
    protected static ?string $pluralModelLabel = 'Boosts';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationBadgeTooltip = 'Boosts en attente de validation';

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', 'pending')->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Véhicule')
                            ->relationship('vehicle', 'model')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->brand->name . ' ' . $record->model)
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('loueur_id')
                            ->label('Loueur')
                            ->relationship('loueur', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('boost_package_id')
                            ->label('Pack')
                            ->relationship('boostPackage', 'name')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'pending_payment' => 'Paiement en cours',
                                'active' => 'Actif',
                                'expired' => 'Expiré',
                                'cancelled' => 'Annulé',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Paiement')
                    ->schema([
                        Forms\Components\TextInput::make('amount_paid')
                            ->label('Montant payé')
                            ->numeric()
                            ->suffix('DA'),

                        Forms\Components\Select::make('payment_method')
                            ->label('Mode de paiement')
                            ->options([
                                'cash' => 'Espèces',
                                'paypal' => 'PayPal',
                            ]),

                        Forms\Components\TextInput::make('payment_reference')
                            ->label('Référence paiement'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Période')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Début'),

                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Fin'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.brand.name')
                    ->label('Véhicule')
                    ->formatStateUsing(fn ($record) => $record->vehicle->brand->name . ' ' . $record->vehicle->model)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('loueur.company_name')
                    ->label('Loueur')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('boostPackage.name')
                    ->label('Pack')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'pending_payment' => 'info',
                        'active' => 'success',
                        'expired' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'pending_payment' => 'Paiement...',
                        'active' => 'Actif',
                        'expired' => 'Expiré',
                        'cancelled' => 'Annulé',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Paiement')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'cash' => 'Espèces',
                        'paypal' => 'PayPal',
                        default => '-',
                    }),

                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Montant')
                    ->money('DZD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Demande')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'pending_payment' => 'Paiement en cours',
                        'active' => 'Actif',
                        'expired' => 'Expiré',
                        'cancelled' => 'Annulé',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Mode de paiement')
                    ->options([
                        'cash' => 'Espèces',
                        'paypal' => 'PayPal',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('activate')
                    ->label('Activer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (VehicleBoost $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Activer le boost')
                    ->modalDescription('Confirmez-vous avoir reçu le paiement et souhaitez activer ce boost ?')
                    ->modalSubmitActionLabel('Oui, activer')
                    ->action(function (VehicleBoost $record) {
                        $record->activate();

                        Notification::make()
                            ->title('Boost activé')
                            ->body('Le boost pour "' . $record->vehicle->brand->name . ' ' . $record->vehicle->model . '" est maintenant actif jusqu\'au ' . $record->ends_at->format('d/m/Y') . '.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Annuler')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (VehicleBoost $record) => in_array($record->status, ['pending', 'pending_payment']))
                    ->requiresConfirmation()
                    ->modalHeading('Annuler le boost')
                    ->modalDescription('Êtes-vous sûr de vouloir annuler cette demande de boost ?')
                    ->action(function (VehicleBoost $record) {
                        $record->update(['status' => 'cancelled']);

                        Notification::make()
                            ->title('Boost annulé')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate_selected')
                        ->label('Activer les sélectionnés')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->activate();
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title($count . ' boost(s) activé(s)')
                                ->success()
                                ->send();
                        }),

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
            'index' => Pages\ListVehicleBoosts::route('/'),
            'create' => Pages\CreateVehicleBoost::route('/create'),
            'edit' => Pages\EditVehicleBoost::route('/{record}/edit'),
        ];
    }
}

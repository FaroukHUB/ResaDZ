<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReferralRewardResource\Pages;
use App\Models\ReferralReward;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReferralRewardResource extends Resource
{
    protected static ?string $model = ReferralReward::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Parrainage';

    protected static ?string $modelLabel = 'Programme Parrainage';

    protected static ?string $pluralModelLabel = 'Programmes Parrainage';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du programme')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Programme parrainage standard'),

                        Forms\Components\Select::make('event')
                            ->label('Événement déclencheur')
                            ->options([
                                'registration' => 'Inscription du filleul',
                                'first_booking' => 'Première réservation du filleul',
                                'account_verified' => 'Compte vérifié',
                            ])
                            ->required()
                            ->helperText('Quand la récompense est attribuée'),

                        Forms\Components\Select::make('reward_type')
                            ->label('Type de récompense')
                            ->options([
                                'credit' => 'Crédit (DA)',
                                'discount_percent' => 'Réduction (%)',
                                'discount_fixed' => 'Réduction fixe (DA)',
                            ])
                            ->required()
                            ->default('credit'),
                    ]),

                Forms\Components\Section::make('Montants')
                    ->description('Pour les réductions %, entrez le pourcentage (ex: 10 pour 10%)')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('referrer_amount')
                                    ->label('Récompense parrain')
                                    ->numeric()
                                    ->required()
                                    ->helperText('Montant en DA ou pourcentage selon le type'),

                                Forms\Components\TextInput::make('referred_amount')
                                    ->label('Récompense filleul')
                                    ->numeric()
                                    ->required()
                                    ->helperText('Montant en DA ou pourcentage selon le type'),
                            ]),
                    ]),

                Forms\Components\Section::make('Limites')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('max_uses_per_user')
                                    ->label('Max utilisations par parrain')
                                    ->numeric()
                                    ->placeholder('Illimité')
                                    ->helperText('Laisser vide = illimité'),

                                Forms\Components\TextInput::make('total_max_uses')
                                    ->label('Max utilisations total')
                                    ->numeric()
                                    ->placeholder('Illimité')
                                    ->helperText('Laisser vide = illimité'),
                            ]),
                    ]),

                Forms\Components\Section::make('Période')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Actif')
                                    ->default(true),

                                Forms\Components\DateTimePicker::make('starts_at')
                                    ->label('Date de début'),

                                Forms\Components\DateTimePicker::make('ends_at')
                                    ->label('Date de fin'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Programme')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('event')
                    ->label('Événement')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'registration' => 'Inscription',
                        'first_booking' => '1ère réservation',
                        'account_verified' => 'Compte vérifié',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'registration' => 'info',
                        'first_booking' => 'success',
                        'account_verified' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('referrer_amount')
                    ->label('Parrain')
                    ->numeric()
                    ->suffix(' DA'),

                Tables\Columns\TextColumn::make('referred_amount')
                    ->label('Filleul')
                    ->numeric()
                    ->suffix(' DA'),

                Tables\Columns\TextColumn::make('reward_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'credit' => 'Crédit',
                        'discount_percent' => 'Réduction %',
                        'discount_fixed' => 'Réduction DA',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Début')
                    ->dateTime('d/m/Y')
                    ->placeholder('Immédiat'),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y')
                    ->placeholder('Sans fin'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),

                Tables\Filters\SelectFilter::make('event')
                    ->label('Événement')
                    ->options([
                        'registration' => 'Inscription',
                        'first_booking' => '1ère réservation',
                        'account_verified' => 'Compte vérifié',
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferralRewards::route('/'),
            'create' => Pages\CreateReferralReward::route('/create'),
            'edit' => Pages\EditReferralReward::route('/{record}/edit'),
        ];
    }
}

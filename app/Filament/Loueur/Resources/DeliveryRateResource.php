<?php

namespace App\Filament\Loueur\Resources;

use App\Filament\Loueur\Resources\DeliveryRateResource\Pages;
use App\Models\DeliveryRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DeliveryRateResource extends Resource
{
    protected static ?string $model = DeliveryRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Livraison';

    protected static ?string $navigationLabel = 'Mes Tarifs';

    protected static ?string $modelLabel = 'Tarif';

    protected static ?string $pluralModelLabel = 'Tarifs Livraison';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $loueur = Auth::user()->loueur;

        return parent::getEloquentQuery()
            ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tarif de livraison')
                    ->description('Définissez vos tarifs par trajet et type de colis')
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('from_city')
                            ->label('Ville de départ')
                            ->placeholder('Ex: Alger, Oran...')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('to_city')
                            ->label('Ville de destination')
                            ->placeholder('Ex: Constantine, Annaba...')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('package_type')
                            ->label('Type de colis')
                            ->options([
                                'colis' => 'Colis',
                                'document' => 'Document',
                                'repas' => 'Repas',
                            ])
                            ->default('colis')
                            ->required(),

                        Forms\Components\TextInput::make('base_price')
                            ->label('Prix de base (DA)')
                            ->numeric()
                            ->required()
                            ->suffix('DA')
                            ->minValue(0),

                        Forms\Components\TextInput::make('price_per_kg')
                            ->label('Prix par kg supplémentaire (DA)')
                            ->numeric()
                            ->suffix('DA/kg')
                            ->minValue(0)
                            ->helperText('Optionnel — laissez vide si prix fixe'),

                        Forms\Components\TextInput::make('max_weight')
                            ->label('Poids max (kg)')
                            ->numeric()
                            ->suffix('kg')
                            ->minValue(0)
                            ->helperText('Optionnel'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from_city')
                    ->label('De')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('to_city')
                    ->label('Vers')
                    ->searchable()
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('base_price')
                    ->label('Prix de base')
                    ->money('DZD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_per_kg')
                    ->label('Prix/kg')
                    ->money('DZD')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('max_weight')
                    ->label('Poids max')
                    ->suffix(' kg')
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('package_type')
                    ->label('Type')
                    ->options([
                        'colis' => 'Colis',
                        'document' => 'Document',
                        'repas' => 'Repas',
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
            ])
            ->defaultSort('from_city');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveryRates::route('/'),
            'create' => Pages\CreateDeliveryRate::route('/create'),
            'edit' => Pages\EditDeliveryRate::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) return false;

        return $loueur->isTaxi() && $loueur->offers_delivery;
    }
}

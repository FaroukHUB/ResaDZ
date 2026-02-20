<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Véhicules';

    protected static ?string $modelLabel = 'Véhicule';

    protected static ?string $pluralModelLabel = 'Véhicules';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Véhicule')
                    ->schema([
                        Forms\Components\Select::make('loueur_id')
                            ->label('Loueur')
                            ->relationship('loueur', 'company_name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('brand_id')
                            ->label('Marque')
                            ->relationship('brand', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('category_id')
                            ->label('Catégorie')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('model')
                            ->label('Modèle')
                            ->required(),

                        Forms\Components\TextInput::make('full_name')
                            ->label('Nom complet')
                            ->required(),

                        Forms\Components\TextInput::make('year')
                            ->label('Année'),

                        Forms\Components\TextInput::make('price_per_day')
                            ->label('Prix/jour (DA)')
                            ->numeric()
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'available' => 'Disponible',
                                'reserved' => 'Réservé',
                                'maintenance' => 'Maintenance',
                                'unavailable' => 'Indisponible',
                            ])
                            ->default('available'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Mis en avant'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Photo')
                    ->square()
                    ->size(50),

                Tables\Columns\TextColumn::make('loueur.company_name')
                    ->label('Loueur')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\ImageColumn::make('brand.logo')
                    ->label('Marque')
                    ->circular()
                    ->size(30),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Véhicule')
                    ->searchable()
                    ->sortable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('year')
                    ->label('Année')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_per_day')
                    ->label('Prix/jour')
                    ->money('DZD', locale: 'fr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'reserved' => 'info',
                        'maintenance' => 'warning',
                        'unavailable' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Disponible',
                        'reserved' => 'Réservé',
                        'maintenance' => 'Maintenance',
                        'unavailable' => 'Indisponible',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Vedette')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('loueur_id')
                    ->label('Loueur')
                    ->relationship('loueur', 'company_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Marque')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'available' => 'Disponible',
                        'reserved' => 'Réservé',
                        'maintenance' => 'Maintenance',
                        'unavailable' => 'Indisponible',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Mis en avant'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}

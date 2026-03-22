<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BoostPackageResource\Pages;
use App\Models\BoostPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BoostPackageResource extends Resource
{
    protected static ?string $model = BoostPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Packages Boost';

    protected static ?string $modelLabel = 'Package Boost';

    protected static ?string $pluralModelLabel = 'Packages Boost';

    protected static ?int $navigationSort = 5;

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
                            ->placeholder('Ex: Boost Premium 7 jours'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->placeholder('Description visible par les loueurs'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('duration_days')
                                    ->label('Durée')
                                    ->numeric()
                                    ->required()
                                    ->suffix('jours')
                                    ->minValue(1)
                                    ->default(7),

                                Forms\Components\TextInput::make('price')
                                    ->label('Prix')
                                    ->numeric()
                                    ->required()
                                    ->suffix('DA')
                                    ->minValue(0),
                            ]),
                    ]),

                Forms\Components\Section::make('Options')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('boost_type')
                                    ->label('Type de boost')
                                    ->options([
                                        'standard' => 'Standard',
                                        'premium' => 'Premium',
                                        'featured' => 'À la une',
                                    ])
                                    ->default('standard')
                                    ->required(),

                                Forms\Components\TextInput::make('position_boost')
                                    ->label('Bonus de position')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Plus élevé = mieux classé'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('show_badge')
                                    ->label('Afficher badge "Sponsorisé"')
                                    ->default(true),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Actif')
                                    ->default(true),
                            ]),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre d\'affichage')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration_days')
                    ->label('Durée')
                    ->suffix(' jours')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('DZD', locale: 'fr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('boost_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'standard' => 'gray',
                        'premium' => 'warning',
                        'featured' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('show_badge')
                    ->label('Badge')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('vehicleBoosts_count')
                    ->label('Utilisations')
                    ->counts('vehicleBoosts')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Actif'),
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
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBoostPackages::route('/'),
            'create' => Pages\CreateBoostPackage::route('/create'),
            'edit' => Pages\EditBoostPackage::route('/{record}/edit'),
        ];
    }
}

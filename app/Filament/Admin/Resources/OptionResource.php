<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OptionResource\Pages;
use App\Filament\Admin\Resources\OptionResource\RelationManagers;
use App\Models\Option;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OptionResource extends Resource
{
    protected static ?string $model = Option::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Options de location';

    protected static ?string $modelLabel = 'Option';

    protected static ?string $pluralModelLabel = 'Options';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de l\'option')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Généré automatiquement'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->maxLength(500),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icône')
                            ->placeholder('heroicon-o-...')
                            ->maxLength(100),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tarification')
                    ->description('Définir les prix en Dinars et en Euros')
                    ->schema([
                        Forms\Components\Select::make('price_type')
                            ->label('Type de tarification')
                            ->options([
                                'per_day' => 'Par jour',
                                'per_rental' => 'Par location',
                                'free' => 'Gratuit',
                            ])
                            ->required()
                            ->default('per_day')
                            ->live(),
                        Forms\Components\TextInput::make('price')
                            ->label('Prix (DA)')
                            ->numeric()
                            ->required()
                            ->suffix('DA')
                            ->visible(fn (Forms\Get $get) => $get('price_type') !== 'free'),
                        Forms\Components\TextInput::make('price_eur')
                            ->label('Prix (EUR)')
                            ->numeric()
                            ->step(0.01)
                            ->suffix('€')
                            ->helperText('Prix pour les clients payant en euros')
                            ->visible(fn (Forms\Get $get) => $get('price_type') !== 'free'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Paramètres')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre d\'affichage')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
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
                Tables\Columns\TextColumn::make('price_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'per_day' => 'Par jour',
                        'per_rental' => 'Par location',
                        'free' => 'Gratuit',
                        default => $state,
                    })
                    ->color(fn (string $state) => match($state) {
                        'per_day' => 'info',
                        'per_rental' => 'warning',
                        'free' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix DA')
                    ->money('DZD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_eur')
                    ->label('Prix EUR')
                    ->money('EUR')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('price_type')
                    ->label('Type')
                    ->options([
                        'per_day' => 'Par jour',
                        'per_rental' => 'Par location',
                        'free' => 'Gratuit',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOptions::route('/'),
        ];
    }
}

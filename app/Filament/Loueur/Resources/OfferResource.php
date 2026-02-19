<?php

namespace App\Filament\Loueur\Resources;

use App\Filament\Loueur\Resources\OfferResource\Pages;
use App\Models\Vehicle;
use App\Models\VehicleOffer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OfferResource extends Resource
{
    protected static ?string $model = VehicleOffer::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Offres Spéciales';

    protected static ?string $modelLabel = 'Offre';

    protected static ?string $pluralModelLabel = 'Offres Spéciales';

    protected static ?int $navigationSort = 10;

    // Filter to show only offers for the connected loueur
    public static function getEloquentQuery(): Builder
    {
        $loueur = Auth::user()->loueur;

        return parent::getEloquentQuery()
            ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id));
    }

    public static function form(Form $form): Form
    {
        $loueur = Auth::user()->loueur;

        return $form
            ->schema([
                Forms\Components\Section::make('Offre Spéciale')
                    ->description('Créez des offres attractives pour vos véhicules')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Véhicule')
                            ->options(function () use ($loueur) {
                                if (!$loueur) return [];
                                return Vehicle::where('loueur_id', $loueur->id)
                                    ->where('is_active', true)
                                    ->pluck('full_name', 'id');
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\Hidden::make('loueur_id')
                            ->default(fn () => $loueur?->id),
                        Forms\Components\TextInput::make('title')
                            ->label('Titre de l\'offre')
                            ->placeholder('Ex: Offre spéciale weekend')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('badge_text')
                            ->label('Badge affiché')
                            ->placeholder('Ex: -10%, PROMO, -20% WE')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Texte court affiché sur la carte du véhicule'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('discount_type')
                                    ->label('Type de réduction')
                                    ->options([
                                        'percentage' => 'Pourcentage (%)',
                                        'fixed' => 'Montant fixe (DA)',
                                    ])
                                    ->default('percentage')
                                    ->required()
                                    ->live(),
                                Forms\Components\TextInput::make('discount_value')
                                    ->label('Valeur de la réduction')
                                    ->numeric()
                                    ->required()
                                    ->suffix(fn (Forms\Get $get) => $get('discount_type') === 'percentage' ? '%' : 'DA')
                                    ->helperText(fn (Forms\Get $get) => $get('discount_type') === 'percentage'
                                        ? 'Ex: 10 pour -10%'
                                        : 'Ex: 500 pour -500 DA'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Date de début')
                                    ->required()
                                    ->default(now()),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Date de fin')
                                    ->required()
                                    ->after('start_date'),
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->label('Description (optionnel)')
                            ->placeholder('Détails de l\'offre, conditions, etc.')
                            ->rows(3),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Offre active')
                            ->default(true)
                            ->helperText('Désactivez pour masquer temporairement l\'offre'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.full_name')
                    ->label('Véhicule')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('badge_text')
                    ->label('Badge')
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('discount_value')
                    ->label('Réduction')
                    ->formatStateUsing(fn ($record) => $record->discount_type === 'percentage'
                        ? '-' . $record->discount_value . '%'
                        : '-' . number_format($record->discount_value, 0, ',', ' ') . ' DA'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if (!$record->is_active) return 'Désactivée';
                        if ($record->end_date < now()) return 'Expirée';
                        if ($record->start_date > now()) return 'À venir';
                        return 'En cours';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'En cours' => 'success',
                        'À venir' => 'info',
                        'Expirée' => 'gray',
                        'Désactivée' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Statut')
                    ->options([
                        '1' => 'Actives',
                        '0' => 'Inactives',
                    ]),
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Véhicule')
                    ->relationship('vehicle', 'full_name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle')
                    ->label(fn ($record) => $record->is_active ? 'Désactiver' : 'Activer')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn ($record) => $record->is_active ? 'warning' : 'success')
                    ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}

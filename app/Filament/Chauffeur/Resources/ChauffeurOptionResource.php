<?php

namespace App\Filament\Chauffeur\Resources;

use App\Filament\Chauffeur\Resources\ChauffeurOptionResource\Pages;
use App\Models\ChauffeurOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ChauffeurOptionResource extends Resource
{
    protected static ?string $model = ChauffeurOption::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Mon Parc';

    protected static ?string $navigationLabel = 'Mes Options';

    protected static ?string $modelLabel = 'Option';

    protected static ?string $pluralModelLabel = 'Options';

    protected static ?int $navigationSort = 2;

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
                Forms\Components\Section::make('Information de l\'option')
                    ->icon('heroicon-o-sparkles')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de l\'option')
                            ->placeholder('Ex: WiFi gratuit, Siege auto...')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\Select::make('category')
                            ->label('Categorie')
                            ->options(ChauffeurOption::CATEGORIES)
                            ->default('comfort')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Decrivez cette option en detail...')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('icon')
                            ->label('Icone')
                            ->placeholder('heroicon-o-wifi')
                            ->helperText('Nom d\'icone Heroicon (ex: heroicon-o-wifi)')
                            ->maxLength(50),
                    ]),

                Forms\Components\Section::make('Tarification')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('pricing_type')
                            ->label('Type de tarification')
                            ->options(ChauffeurOption::PRICING_TYPES)
                            ->default('free')
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('price')
                            ->label('Prix (DA)')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('DA')
                            ->visible(fn (Forms\Get $get) => $get('pricing_type') === 'paid')
                            ->required(fn (Forms\Get $get) => $get('pricing_type') === 'paid'),

                        Forms\Components\TextInput::make('max_quantity')
                            ->label('Quantite maximum')
                            ->helperText('Ex: 2 sieges auto maximum')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->default(1),
                    ]),

                Forms\Components\Section::make('Statut')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Option active')
                            ->helperText('Desactivez pour masquer temporairement cette option')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Option')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->description ? \Str::limit($record->description, 50) : null),

                Tables\Columns\TextColumn::make('category')
                    ->label('Categorie')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'comfort' => 'primary',
                        'child' => 'info',
                        'accessibility' => 'success',
                        'luggage' => 'warning',
                        'service' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ChauffeurOption::CATEGORIES[$state] ?? $state),

                Tables\Columns\TextColumn::make('pricing_type')
                    ->label('Tarif')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'free' => 'success',
                        'paid' => 'warning',
                        'on_request' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ChauffeurOption::PRICING_TYPES[$state] ?? $state),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state, $record) => $record->formatted_price)
                    ->color(fn ($record) => $record->pricing_type === 'free' ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('max_quantity')
                    ->label('Qte max')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creee le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categorie')
                    ->options(ChauffeurOption::CATEGORIES),

                Tables\Filters\SelectFilter::make('pricing_type')
                    ->label('Tarification')
                    ->options(ChauffeurOption::PRICING_TYPES),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actives')
                    ->falseLabel('Inactives'),
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
            ->defaultSort('category')
            ->emptyStateHeading('Aucune option')
            ->emptyStateDescription('Ajoutez des options que vous proposez a vos clients (WiFi, siege auto, etc.).')
            ->emptyStateIcon('heroicon-o-sparkles');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChauffeurOptions::route('/'),
            'create' => Pages\CreateChauffeurOption::route('/create'),
            'edit' => Pages\EditChauffeurOption::route('/{record}/edit'),
        ];
    }
}

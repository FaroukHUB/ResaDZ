<?php

namespace App\Filament\Loueur\Resources;

use App\Filament\Loueur\Resources\ExpenseCategoryResource\Pages;
use App\Models\ExpenseCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Finances';

    protected static ?string $navigationLabel = 'Catégories de dépenses';

    protected static ?string $modelLabel = 'Catégorie';

    protected static ?string $pluralModelLabel = 'Catégories de dépenses';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $loueur = Auth::user()->loueur;

        return parent::getEloquentQuery()
            ->when($loueur, fn ($query) => $query->where(function ($q) use ($loueur) {
                $q->where('loueur_id', $loueur->id)
                  ->orWhere('is_default', true);
            }))
            ->orderBy('sort_order');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Catégorie')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Carburant, Assurance, Entretien...'),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icône')
                            ->placeholder('Ex: fuel, wrench, shield')
                            ->helperText('Nom de l\'icône (optionnel)'),
                        Forms\Components\ColorPicker::make('color')
                            ->label('Couleur'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
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
                Tables\Columns\ColorColumn::make('color')
                    ->label('Couleur'),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Par défaut')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => !$record->is_default),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenseCategories::route('/'),
            'create' => Pages\CreateExpenseCategory::route('/create'),
            'edit' => Pages\EditExpenseCategory::route('/{record}/edit'),
        ];
    }
}

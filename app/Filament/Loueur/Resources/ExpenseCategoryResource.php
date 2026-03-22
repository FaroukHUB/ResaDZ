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
                Forms\Components\Placeholder::make('category_help')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString('
                        <div class="p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg text-sm text-orange-800 dark:text-orange-200">
                            <strong>🏷️ Catégories de dépenses</strong> — Organisez vos dépenses par type pour mieux analyser vos coûts. Les catégories apparaissent dans le menu déroulant lors de l\'ajout d\'une dépense et dans les graphiques du tableau de bord financier.
                        </div>
                    ')),
                Forms\Components\Section::make('Catégorie')
                    ->description('Créez une catégorie personnalisée pour vos dépenses')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Carburant, Assurance, Entretien...')
                            ->helperText('Nom affiché dans la liste des catégories'),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icône')
                            ->placeholder('Ex: fuel, wrench, shield')
                            ->helperText('Icône Heroicon (optionnel) — ex: truck, currency-dollar'),
                        Forms\Components\ColorPicker::make('color')
                            ->label('Couleur')
                            ->helperText('Couleur du badge dans les rapports'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->helperText('Description optionnelle pour vous rappeler l\'usage'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0)
                            ->helperText('Les catégories avec un ordre plus petit apparaissent en premier'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Désactivez pour masquer sans supprimer'),
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

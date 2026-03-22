<?php

namespace App\Filament\Loueur\Resources;

use App\Filament\Loueur\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\ExpenseCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static ?string $navigationGroup = 'Finances';

    protected static ?string $navigationLabel = 'Dépenses';

    protected static ?string $modelLabel = 'Dépense';

    protected static ?string $pluralModelLabel = 'Dépenses';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $loueur = Auth::user()->loueur;

        return parent::getEloquentQuery()
            ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id))
            ->where('type', 'expense')
            ->orderBy('transaction_date', 'desc');
    }

    public static function form(Form $form): Form
    {
        $loueur = Auth::user()->loueur;

        return $form
            ->schema([
                Forms\Components\Hidden::make('type')
                    ->default('expense'),
                Forms\Components\Placeholder::make('expense_help')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString('
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-200">
                            <strong>📊 Pourquoi enregistrer vos dépenses ?</strong> — Elles apparaissent dans votre tableau de bord financier pour calculer votre bénéfice net. Associez-les à un véhicule pour connaître la rentabilité de chaque voiture !
                        </div>
                    ')),
                Forms\Components\Section::make('Dépense')
                    ->description('Enregistrez carburant, entretien, assurance, lavage, réparations...')
                    ->icon('heroicon-o-arrow-trending-down')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Plein carburant Tiguan')
                                    ->helperText('Soyez précis pour retrouver facilement'),
                                Forms\Components\DatePicker::make('transaction_date')
                                    ->label('Date')
                                    ->required()
                                    ->default(now())
                                    ->helperText('Date de la dépense (pas forcément aujourd\'hui)'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('expense_category_id')
                                    ->label('Catégorie')
                                    ->options(fn () => $loueur
                                        ? ExpenseCategory::where('loueur_id', $loueur->id)
                                            ->orWhere('is_default', true)
                                            ->pluck('name', 'id')
                                        : ExpenseCategory::where('is_default', true)->pluck('name', 'id')
                                    )
                                    ->searchable()
                                    ->required()
                                    ->helperText('Permet de filtrer et analyser par type'),
                                Forms\Components\Select::make('vehicle_id')
                                    ->label('Véhicule concerné')
                                    ->options(fn () => $loueur
                                        ? Vehicle::where('loueur_id', $loueur->id)->pluck('full_name', 'id')
                                        : []
                                    )
                                    ->searchable()
                                    ->placeholder('Optionnel')
                                    ->helperText('Lier à un véhicule = suivi de rentabilité'),
                            ]),
                    ]),
                Forms\Components\Section::make('Montant')
                    ->description('Détails financiers de la dépense')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('Montant')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->suffix('DA')
                                    ->helperText('Montant total de la dépense'),
                                Forms\Components\Select::make('currency')
                                    ->label('Devise')
                                    ->options([
                                        'DZD' => 'Dinar (DA)',
                                        'EUR' => 'Euro (€)',
                                    ])
                                    ->default('DZD')
                                    ->required()
                                    ->helperText('En quelle devise'),
                                Forms\Components\Select::make('payment_method')
                                    ->label('Méthode de paiement')
                                    ->options([
                                        'cash' => 'Espèces',
                                        'cib' => 'CIB',
                                        'dahabia' => 'Dahabia',
                                        'baridimob' => 'BaridiMob',
                                        'bank_transfer' => 'Virement',
                                    ])
                                    ->default('cash')
                                    ->helperText('Comment avez-vous payé'),
                            ]),
                    ]),
                Forms\Components\Section::make('Notes')
                    ->description('Informations complémentaires')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes additionnelles')
                            ->rows(2)
                            ->placeholder('Détails supplémentaires, numéro de facture, etc.')
                            ->helperText('Numéro de facture, nom du fournisseur...'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('expenseCategory.name')
                    ->label('Catégorie')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('vehicle.full_name')
                    ->label('Véhicule')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->formatStateUsing(fn ($record) => '-' . number_format($record->amount, 0, ',', ' ') . ' ' . ($record->currency === 'EUR' ? '€' : 'DA'))
                    ->color('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Méthode')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'cash' => 'Espèces',
                        'cib' => 'CIB',
                        'dahabia' => 'Dahabia',
                        'baridimob' => 'BaridiMob',
                        'bank_transfer' => 'Virement',
                        default => $state ?? '-',
                    })
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('expense_category_id')
                    ->label('Catégorie')
                    ->relationship('expenseCategory', 'name'),
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Véhicule')
                    ->relationship('vehicle', 'full_name'),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}

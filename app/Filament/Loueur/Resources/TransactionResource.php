<?php

namespace App\Filament\Loueur\Resources;

use App\Filament\Loueur\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\Booking;
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

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Finances';

    protected static ?string $navigationLabel = 'Entrées / Sorties';

    protected static ?string $modelLabel = 'Transaction';

    protected static ?string $pluralModelLabel = 'Transactions';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $loueur = Auth::user()->loueur;

        return parent::getEloquentQuery()
            ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id))
            ->orderBy('transaction_date', 'desc');
    }

    public static function form(Form $form): Form
    {
        $loueur = Auth::user()->loueur;

        return $form
            ->schema([
                Forms\Components\Section::make('Type de transaction')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'income' => '💰 Entrée (Revenu)',
                                        'expense' => '💸 Sortie (Dépense)',
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($set) => $set('expense_category_id', null)),
                                Forms\Components\DatePicker::make('transaction_date')
                                    ->label('Date')
                                    ->required()
                                    ->default(now()),
                            ]),
                    ]),
                Forms\Components\Section::make('Détails')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ex: Location VW Tiguan - Ahmed'),
                                Forms\Components\Select::make('expense_category_id')
                                    ->label('Catégorie')
                                    ->options(fn () => $loueur
                                        ? ExpenseCategory::where('loueur_id', $loueur->id)
                                            ->orWhere('is_default', true)
                                            ->pluck('name', 'id')
                                        : ExpenseCategory::where('is_default', true)->pluck('name', 'id')
                                    )
                                    ->visible(fn ($get) => $get('type') === 'expense')
                                    ->searchable(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('vehicle_id')
                                    ->label('Véhicule concerné')
                                    ->options(fn () => $loueur
                                        ? Vehicle::where('loueur_id', $loueur->id)->pluck('full_name', 'id')
                                        : []
                                    )
                                    ->searchable()
                                    ->placeholder('Optionnel'),
                                Forms\Components\Select::make('booking_id')
                                    ->label('Réservation liée')
                                    ->options(fn () => $loueur
                                        ? Booking::where('loueur_id', $loueur->id)
                                            ->orderBy('created_at', 'desc')
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(fn ($b) => [$b->id => $b->reference . ' - ' . $b->client_name])
                                        : []
                                    )
                                    ->searchable()
                                    ->placeholder('Optionnel'),
                            ]),
                    ]),
                Forms\Components\Section::make('Montant')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('amount')
                                    ->label('Montant')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0),
                                Forms\Components\Select::make('currency')
                                    ->label('Devise')
                                    ->options([
                                        'DZD' => 'Dinar (DA)',
                                        'EUR' => 'Euro (€)',
                                    ])
                                    ->default('DZD')
                                    ->required(),
                                Forms\Components\Select::make('payment_method')
                                    ->label('Méthode')
                                    ->options([
                                        'cash' => 'Espèces',
                                        'cib' => 'CIB',
                                        'dahabia' => 'Dahabia',
                                        'baridimob' => 'BaridiMob',
                                        'paypal' => 'PayPal',
                                        'bank_transfer' => 'Virement',
                                        'wise' => 'Wise',
                                    ])
                                    ->default('cash'),
                            ]),
                        Forms\Components\TextInput::make('payment_reference')
                            ->label('Référence paiement')
                            ->placeholder('Numéro de transaction, reçu, etc.')
                            ->helperText('Optionnel'),
                    ]),
                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes additionnelles')
                            ->rows(3),
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
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'income' => 'Entrée',
                        'expense' => 'Sortie',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('expenseCategory.name')
                    ->label('Catégorie')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('vehicle.full_name')
                    ->label('Véhicule')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->formatStateUsing(fn ($record) => $record->getFormattedAmount())
                    ->color(fn ($record) => $record->isIncome() ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Méthode')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'cash' => 'Espèces',
                        'cib' => 'CIB',
                        'dahabia' => 'Dahabia',
                        'baridimob' => 'BaridiMob',
                        'paypal' => 'PayPal',
                        'bank_transfer' => 'Virement',
                        'wise' => 'Wise',
                        default => $state ?? '-',
                    })
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'income' => 'Entrées',
                        'expense' => 'Sorties',
                    ]),
                Tables\Filters\SelectFilter::make('expense_category_id')
                    ->label('Catégorie')
                    ->relationship('expenseCategory', 'name'),
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Véhicule')
                    ->relationship('vehicle', 'full_name'),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Méthode')
                    ->options([
                        'cash' => 'Espèces',
                        'cib' => 'CIB',
                        'dahabia' => 'Dahabia',
                        'baridimob' => 'BaridiMob',
                        'paypal' => 'PayPal',
                        'bank_transfer' => 'Virement',
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
        return [
            //
        ];
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

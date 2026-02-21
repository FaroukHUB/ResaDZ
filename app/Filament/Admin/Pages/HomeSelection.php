<?php

namespace App\Filament\Admin\Pages;

use App\Models\Vehicle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class HomeSelection extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $navigationLabel = 'Sélection Accueil';

    protected static ?string $title = 'Notre sélection pour vous';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.admin.pages.home-selection';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Vehicle::query()
                    ->with(['brand', 'loueur'])
                    ->where('is_active', true)
                    ->where('status', 'available')
            )
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->size(50),

                TextColumn::make('full_name')
                    ->label('Véhicule')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->brand?->name),

                TextColumn::make('loueur.company_name')
                    ->label('Loueur')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price_per_day')
                    ->label('Prix/jour')
                    ->money('DZD', locale: 'fr')
                    ->sortable(),

                TextColumn::make('is_in_selection')
                    ->label('Dans sélection')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Oui' : 'Non')
                    ->color(fn (bool $state) => $state ? 'success' : 'gray'),

                TextInputColumn::make('selection_order')
                    ->label('Ordre')
                    ->rules(['nullable', 'integer', 'min:1'])
                    ->sortable(),
            ])
            ->defaultSort('selection_order')
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('is_in_selection')
                    ->label('Dans sélection')
                    ->trueLabel('Dans sélection')
                    ->falseLabel('Hors sélection')
                    ->placeholder('Tous'),
            ])
            ->actions([
                Action::make('addToSelection')
                    ->label('Ajouter')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_in_selection)
                    ->form([
                        TextInput::make('order')
                            ->label('Position')
                            ->numeric()
                            ->default(fn () => Vehicle::where('is_in_selection', true)->max('selection_order') + 1)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'is_in_selection' => true,
                            'selection_order' => $data['order'],
                        ]);

                        Notification::make()
                            ->title('Véhicule ajouté à la sélection')
                            ->success()
                            ->send();
                    }),

                Action::make('removeFromSelection')
                    ->label('Retirer')
                    ->icon('heroicon-o-minus')
                    ->color('danger')
                    ->visible(fn ($record) => $record->is_in_selection)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_in_selection' => false,
                            'selection_order' => null,
                        ]);

                        Notification::make()
                            ->title('Véhicule retiré de la sélection')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('addAllToSelection')
                    ->label('Ajouter à la sélection')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->action(function (Collection $records) {
                        $maxOrder = Vehicle::where('is_in_selection', true)->max('selection_order') ?? 0;

                        foreach ($records as $index => $record) {
                            $record->update([
                                'is_in_selection' => true,
                                'selection_order' => $maxOrder + $index + 1,
                            ]);
                        }

                        Notification::make()
                            ->title($records->count() . ' véhicules ajoutés')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('removeAllFromSelection')
                    ->label('Retirer de la sélection')
                    ->icon('heroicon-o-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->update([
                                'is_in_selection' => false,
                                'selection_order' => null,
                            ]);
                        }

                        Notification::make()
                            ->title($records->count() . ' véhicules retirés')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public function getSelectedVehiclesCount(): int
    {
        return Vehicle::where('is_in_selection', true)->count();
    }
}

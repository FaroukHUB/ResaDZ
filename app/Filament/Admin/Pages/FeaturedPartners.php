<?php

namespace App\Filament\Admin\Pages;

use App\Models\Loueur;
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

class FeaturedPartners extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $navigationLabel = 'Partenaires en vedette';

    protected static ?string $title = 'Loueurs partenaires en vedette';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.admin.pages.featured-partners';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loueur::query()
                    ->withCount('vehicles')
                    ->where('is_active', true)
            )
            ->columns([
                ImageColumn::make('logo')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=P&color=16a34a&background=dcfce7'),

                TextColumn::make('company_name')
                    ->label('Loueur')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->city ?? $record->wilaya ?? 'Algerie'),

                TextColumn::make('vehicles_count')
                    ->label('Vehicules')
                    ->sortable(),

                TextColumn::make('rating')
                    ->label('Note')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . '/5' : '-')
                    ->sortable(),

                TextColumn::make('is_featured_partner')
                    ->label('En vedette')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Oui' : 'Non')
                    ->color(fn (bool $state) => $state ? 'success' : 'gray'),

                TextInputColumn::make('partner_order')
                    ->label('Ordre')
                    ->rules(['nullable', 'integer', 'min:1'])
                    ->sortable(),
            ])
            ->defaultSort('partner_order')
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('is_featured_partner')
                    ->label('En vedette')
                    ->trueLabel('En vedette')
                    ->falseLabel('Non en vedette')
                    ->placeholder('Tous'),
            ])
            ->actions([
                Action::make('addToFeatured')
                    ->label('Mettre en avant')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_featured_partner)
                    ->form([
                        TextInput::make('order')
                            ->label('Position')
                            ->numeric()
                            ->default(fn () => Loueur::where('is_featured_partner', true)->max('partner_order') + 1)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'is_featured_partner' => true,
                            'partner_order' => $data['order'],
                        ]);

                        Notification::make()
                            ->title('Loueur mis en avant')
                            ->success()
                            ->send();
                    }),

                Action::make('removeFromFeatured')
                    ->label('Retirer')
                    ->icon('heroicon-o-minus')
                    ->color('danger')
                    ->visible(fn ($record) => $record->is_featured_partner)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_featured_partner' => false,
                            'partner_order' => null,
                        ]);

                        Notification::make()
                            ->title('Loueur retire de la mise en avant')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('addAllToFeatured')
                    ->label('Mettre en avant')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->action(function (Collection $records) {
                        $maxOrder = Loueur::where('is_featured_partner', true)->max('partner_order') ?? 0;

                        foreach ($records as $index => $record) {
                            $record->update([
                                'is_featured_partner' => true,
                                'partner_order' => $maxOrder + $index + 1,
                            ]);
                        }

                        Notification::make()
                            ->title($records->count() . ' loueurs mis en avant')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('removeAllFromFeatured')
                    ->label('Retirer de la mise en avant')
                    ->icon('heroicon-o-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->update([
                                'is_featured_partner' => false,
                                'partner_order' => null,
                            ]);
                        }

                        Notification::make()
                            ->title($records->count() . ' loueurs retires')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public function getFeaturedPartnersCount(): int
    {
        return Loueur::where('is_featured_partner', true)->count();
    }
}

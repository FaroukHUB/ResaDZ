<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NewsletterSubscriberResource\Pages;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Abonnés Newsletter';

    protected static ?string $modelLabel = 'Abonné';

    protected static ?string $pluralModelLabel = 'Abonnés Newsletter';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('name')
                            ->label('Nom'),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'active' => 'Actif',
                                'unsubscribed' => 'Désabonné',
                                'bounced' => 'Bounced',
                            ])
                            ->required()
                            ->default('active'),

                        Forms\Components\Select::make('source')
                            ->label('Source')
                            ->options([
                                'footer' => 'Footer',
                                'popup' => 'Popup',
                                'checkout' => 'Checkout',
                                'import' => 'Import',
                                'manual' => 'Manuel',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'active' => 'success',
                        'unsubscribed', 'bounced' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'active' => 'Actif',
                        'unsubscribed' => 'Désabonné',
                        'bounced' => 'Bounced',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('confirmed_at')
                    ->label('Confirmé le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'active' => 'Actif',
                        'unsubscribed' => 'Désabonné',
                        'bounced' => 'Bounced',
                    ]),

                Tables\Filters\SelectFilter::make('source')
                    ->label('Source')
                    ->options([
                        'footer' => 'Footer',
                        'popup' => 'Popup',
                        'checkout' => 'Checkout',
                        'import' => 'Import',
                    ]),

                Tables\Filters\Filter::make('confirmed')
                    ->label('Confirmés')
                    ->query(fn ($query) => $query->whereNotNull('confirmed_at')),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmer')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->confirm()),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('export')
                        ->label('Exporter CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $csv = "Email,Nom,Statut,Source,Date inscription\n";
                            foreach ($records as $sub) {
                                $csv .= "\"{$sub->email}\",\"{$sub->name}\",\"{$sub->status}\",\"{$sub->source}\",\"{$sub->created_at->format('d/m/Y H:i')}\"\n";
                            }

                            return response()->streamDownload(
                                fn () => print($csv),
                                'newsletter-subscribers-' . now()->format('Y-m-d') . '.csv'
                            );
                        }),

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activer')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each->confirm()),
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
            'index' => Pages\ListNewsletterSubscribers::route('/'),
            'create' => Pages\CreateNewsletterSubscriber::route('/create'),
        ];
    }
}

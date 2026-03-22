<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Avis';

    protected static ?string $modelLabel = 'Avis';

    protected static ?string $pluralModelLabel = 'Avis';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationBadgeTooltip = 'Avis en attente de moderation';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('is_approved', false)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations')
                    ->schema([
                        Forms\Components\Select::make('loueur_id')
                            ->label('Loueur')
                            ->relationship('loueur', 'company_name')
                            ->disabled(),

                        Forms\Components\Select::make('booking_id')
                            ->label('Réservation')
                            ->relationship('booking', 'reference')
                            ->disabled(),

                        Forms\Components\TextInput::make('type')
                            ->label('Type')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => $state === 'client_to_loueur' ? 'Client → Loueur' : 'Loueur → Client'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\TextInput::make('rating_overall')
                            ->label('Note globale')
                            ->disabled()
                            ->suffix('/5'),

                        Forms\Components\TextInput::make('rating_vehicle')
                            ->label('Véhicule')
                            ->disabled()
                            ->suffix('/5'),

                        Forms\Components\TextInput::make('rating_communication')
                            ->label('Communication')
                            ->disabled()
                            ->suffix('/5'),

                        Forms\Components\TextInput::make('rating_punctuality')
                            ->label('Ponctualité')
                            ->disabled()
                            ->suffix('/5'),

                        Forms\Components\TextInput::make('rating_cleanliness')
                            ->label('Propreté')
                            ->disabled()
                            ->suffix('/5'),
                    ])
                    ->columns(5),

                Forms\Components\Section::make('Commentaire')
                    ->schema([
                        Forms\Components\Textarea::make('comment')
                            ->label('Commentaire du client')
                            ->disabled()
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('response')
                            ->label('Réponse du loueur')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Modération')
                    ->schema([
                        Forms\Components\Toggle::make('is_public')
                            ->label('Visible publiquement')
                            ->default(true),

                        Forms\Components\Toggle::make('is_approved')
                            ->label('Approuvé')
                            ->default(true),

                        Forms\Components\Toggle::make('is_flagged')
                            ->label('Signalé')
                            ->default(false)
                            ->live(),

                        Forms\Components\Textarea::make('flag_reason')
                            ->label('Raison du signalement')
                            ->rows(2)
                            ->visible(fn ($get) => $get('is_flagged'))
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loueur.company_name')
                    ->label('Loueur')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('Auteur')
                    ->searchable(),

                Tables\Columns\TextColumn::make('rating_overall')
                    ->label('Note')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('comment')
                    ->label('Commentaire')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Approuvé')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\IconColumn::make('is_flagged')
                    ->label('Signalé')
                    ->boolean()
                    ->trueIcon('heroicon-o-flag')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('danger')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('response')
                    ->label('Réponse')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->response))
                    ->trueIcon('heroicon-o-chat-bubble-left-ellipsis')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Approuvé')
                    ->trueLabel('Approuvés')
                    ->falseLabel('En attente'),

                Tables\Filters\TernaryFilter::make('is_flagged')
                    ->label('Signalé')
                    ->trueLabel('Signalés')
                    ->falseLabel('Non signalés'),

                Tables\Filters\SelectFilter::make('rating_overall')
                    ->label('Note')
                    ->options([
                        1 => '⭐ 1 étoile',
                        2 => '⭐⭐ 2 étoiles',
                        3 => '⭐⭐⭐ 3 étoiles',
                        4 => '⭐⭐⭐⭐ 4 étoiles',
                        5 => '⭐⭐⭐⭐⭐ 5 étoiles',
                    ]),

                Tables\Filters\SelectFilter::make('loueur_id')
                    ->label('Loueur')
                    ->relationship('loueur', 'company_name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_approved)
                    ->action(fn ($record) => $record->update(['is_approved' => true, 'is_flagged' => false])),

                Tables\Actions\Action::make('reject')
                    ->label('Masquer')
                    ->icon('heroicon-o-eye-slash')
                    ->color('danger')
                    ->visible(fn ($record) => $record->is_approved)
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['is_approved' => false, 'is_public' => false])),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn ($records) => $records->each->update(['is_approved' => true])),

                Tables\Actions\BulkAction::make('reject')
                    ->label('Masquer')
                    ->icon('heroicon-o-eye-slash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($records) => $records->each->update(['is_approved' => false, 'is_public' => false])),

                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NewsletterResource\Pages;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Campagnes Email';

    protected static ?string $modelLabel = 'Campagne';

    protected static ?string $pluralModelLabel = 'Campagnes Email';

    protected static ?int $navigationSort = 45;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contenu')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('Sujet')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: Nouveaux véhicules disponibles !'),

                        Forms\Components\TextInput::make('preview_text')
                            ->label('Texte de prévisualisation')
                            ->maxLength(255)
                            ->placeholder('Texte visible dans la boîte de réception')
                            ->helperText('Affiché après le sujet dans certains clients email'),

                        Forms\Components\RichEditor::make('content')
                            ->label('Contenu')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'blockquote',
                            ]),
                    ]),

                Forms\Components\Section::make('Planification')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Statut')
                                    ->options([
                                        'draft' => 'Brouillon',
                                        'scheduled' => 'Planifié',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->live(),

                                Forms\Components\DateTimePicker::make('scheduled_at')
                                    ->label('Date d\'envoi')
                                    ->visible(fn ($get) => $get('status') === 'scheduled')
                                    ->required(fn ($get) => $get('status') === 'scheduled')
                                    ->minDate(now()),
                            ]),

                        Forms\Components\Placeholder::make('recipients_info')
                            ->label('Destinataires')
                            ->content(fn () => NewsletterSubscriber::active()->count() . ' abonnés actifs'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->label('Sujet')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'sending' => 'info',
                        'sent' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Brouillon',
                        'scheduled' => 'Planifié',
                        'sending' => 'En cours',
                        'sent' => 'Envoyé',
                        'cancelled' => 'Annulé',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Planifié')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Envoyé')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sent_count')
                    ->label('Envois')
                    ->numeric(),

                Tables\Columns\TextColumn::make('open_rate')
                    ->label('Ouvertures')
                    ->suffix('%')
                    ->color(fn ($state) => $state > 20 ? 'success' : ($state > 10 ? 'warning' : 'danger')),

                Tables\Columns\TextColumn::make('click_rate')
                    ->label('Clics')
                    ->suffix('%')
                    ->color(fn ($state) => $state > 5 ? 'success' : ($state > 2 ? 'warning' : 'danger')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'scheduled' => 'Planifié',
                        'sending' => 'En cours',
                        'sent' => 'Envoyé',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('duplicate')
                    ->label('Dupliquer')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function ($record) {
                        $new = $record->replicate();
                        $new->subject = $record->subject . ' (copie)';
                        $new->status = 'draft';
                        $new->scheduled_at = null;
                        $new->sent_at = null;
                        $new->recipients_count = 0;
                        $new->sent_count = 0;
                        $new->opened_count = 0;
                        $new->clicked_count = 0;
                        $new->save();
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => in_array($record->status, ['draft', 'scheduled'])),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->status === 'draft'),
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
            'index' => Pages\ListNewsletters::route('/'),
            'create' => Pages\CreateNewsletter::route('/create'),
            'edit' => Pages\EditNewsletter::route('/{record}/edit'),
        ];
    }
}

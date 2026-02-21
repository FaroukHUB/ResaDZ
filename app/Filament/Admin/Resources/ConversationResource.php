<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ConversationResource\Pages;
use App\Models\Conversation;
use App\Models\Loueur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Messages';

    protected static ?string $modelLabel = 'Conversation';

    protected static ?string $pluralModelLabel = 'Conversations';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('admin_unread', true)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Nouvelle conversation')
                    ->schema([
                        Forms\Components\Select::make('loueur_id')
                            ->label('Loueur')
                            ->options(Loueur::active()->pluck('company_name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('template')
                            ->label('Template rapide')
                            ->options(Conversation::getTemplateOptions())
                            ->placeholder('Choisir un template...')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $template = Conversation::getTemplate($state);
                                    if ($template) {
                                        $set('subject', $template['subject']);
                                        $set('initial_message', $template['message']);
                                        // Set category based on template
                                        $category = explode('.', $state)[0];
                                        $set('category', $category);
                                    }
                                }
                            })
                            ->helperText('Sélectionnez un template pour pré-remplir le message')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('subject')
                            ->label('Sujet')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category')
                            ->label('Catégorie')
                            ->options(Conversation::getCategories())
                            ->default('general'),

                        Forms\Components\Select::make('priority')
                            ->label('Priorité')
                            ->options(Conversation::getPriorities())
                            ->default('normal'),

                        Forms\Components\Textarea::make('initial_message')
                            ->label('Message')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull()
                            ->helperText('Vous pouvez modifier le message pré-rempli selon vos besoins'),
                    ])
                    ->columns(2),
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

                Tables\Columns\TextColumn::make('subject')
                    ->label('Sujet')
                    ->searchable()
                    ->limit(40)
                    ->weight(fn ($record) => $record->admin_unread ? 'bold' : 'normal'),

                Tables\Columns\BadgeColumn::make('category')
                    ->label('Catégorie')
                    ->formatStateUsing(fn ($state) => Conversation::getCategories()[$state] ?? $state)
                    ->colors([
                        'warning' => 'boost',
                        'success' => 'invoice',
                        'danger' => 'support',
                        'secondary' => 'general',
                    ]),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Priorité')
                    ->formatStateUsing(fn ($state) => Conversation::getPriorities()[$state] ?? $state)
                    ->colors([
                        'secondary' => 'low',
                        'primary' => 'normal',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn ($state) => Conversation::getStatuses()[$state] ?? $state)
                    ->colors([
                        'success' => 'open',
                        'secondary' => 'closed',
                        'warning' => 'archived',
                    ]),

                Tables\Columns\IconColumn::make('admin_unread')
                    ->label('Non lu')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope')
                    ->falseIcon('heroicon-o-envelope-open')
                    ->trueColor('danger')
                    ->falseColor('secondary'),

                Tables\Columns\TextColumn::make('messages_count')
                    ->label('Messages')
                    ->counts('messages')
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_message_at')
                    ->label('Dernier message')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('last_message_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(Conversation::getStatuses()),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options(Conversation::getCategories()),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Priorité')
                    ->options(Conversation::getPriorities()),

                Tables\Filters\TernaryFilter::make('admin_unread')
                    ->label('Non lus')
                    ->trueLabel('Non lus uniquement')
                    ->falseLabel('Lus uniquement'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => static::getUrl('view', ['record' => $record])),

                Tables\Actions\Action::make('close')
                    ->label('Fermer')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'open')
                    ->action(fn ($record) => $record->update(['status' => 'closed'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('close')
                    ->label('Fermer les sélectionnés')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->action(fn ($records) => $records->each->update(['status' => 'closed'])),

                Tables\Actions\BulkAction::make('markAsRead')
                    ->label('Marquer comme lu')
                    ->icon('heroicon-o-envelope-open')
                    ->action(fn ($records) => $records->each->markAsReadByAdmin()),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConversations::route('/'),
            'create' => Pages\CreateConversation::route('/create'),
            'view' => Pages\ViewConversation::route('/{record}'),
        ];
    }
}

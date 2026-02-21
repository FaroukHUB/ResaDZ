<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Loueur;
use App\Notifications\InvoiceSentNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Factures';

    protected static ?string $modelLabel = 'Facture';

    protected static ?string $pluralModelLabel = 'Factures';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('N° Facture')
                            ->disabled()
                            ->default(fn () => Invoice::generateInvoiceNumber()),

                        Forms\Components\Select::make('loueur_id')
                            ->label('Loueur')
                            ->options(Loueur::active()->pluck('company_name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $loueur = Loueur::find($state);
                                    if ($loueur) {
                                        $set('billing_name', $loueur->company_name);
                                        $set('billing_address', $loueur->address);
                                        $set('billing_city', $loueur->city);
                                        $set('billing_phone', $loueur->phone);
                                        $set('billing_email', $loueur->user?->email);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options(Invoice::getStatuses())
                            ->default('draft')
                            ->required(),

                        Forms\Components\DatePicker::make('issue_date')
                            ->label('Date d\'émission')
                            ->default(now())
                            ->required(),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('Date d\'échéance')
                            ->default(now()->addDays(30))
                            ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->label('Méthode de paiement')
                            ->options(Invoice::getPaymentMethods()),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Informations de facturation')
                    ->schema([
                        Forms\Components\TextInput::make('billing_name')
                            ->label('Nom / Raison sociale')
                            ->required(),

                        Forms\Components\TextInput::make('billing_address')
                            ->label('Adresse'),

                        Forms\Components\TextInput::make('billing_city')
                            ->label('Ville'),

                        Forms\Components\TextInput::make('billing_phone')
                            ->label('Téléphone'),

                        Forms\Components\TextInput::make('billing_email')
                            ->label('Email')
                            ->email(),

                        Forms\Components\TextInput::make('billing_nif')
                            ->label('NIF (Numéro fiscal)'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Lignes de facture')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Qté')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Prix unitaire (DA)')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\TextInput::make('discount')
                                    ->label('Remise (DA)')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\TextInput::make('total')
                                    ->label('Total')
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('Ajouter une ligne')
                            ->collapsible()
                            ->cloneable(),
                    ]),

                Forms\Components\Section::make('Totaux')
                    ->schema([
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Remise globale (DA)')
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('tax_rate')
                            ->label('TVA (%)')
                            ->numeric()
                            ->default(19),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes / Commentaires')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('N° Facture')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('loueur.company_name')
                    ->label('Loueur')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn ($state) => Invoice::getStatuses()[$state] ?? $state)
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'sent',
                        'success' => 'paid',
                        'danger' => fn ($state) => in_array($state, ['cancelled', 'overdue']),
                    ]),

                Tables\Columns\TextColumn::make('issue_date')
                    ->label('Date émission')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('DZD')
                    ->sortable(),

                Tables\Columns\IconColumn::make('sent_at')
                    ->label('Envoyée')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->sent_at !== null),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(Invoice::getStatuses()),

                Tables\Filters\SelectFilter::make('loueur_id')
                    ->label('Loueur')
                    ->relationship('loueur', 'company_name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('send')
                    ->label('Envoyer')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Envoyer la facture')
                    ->modalDescription('Un email avec la facture sera envoyé au loueur.')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->action(function ($record) {
                        $record->markAsSent();

                        // Send email
                        try {
                            if ($record->loueur->user) {
                                $record->loueur->user->notify(new InvoiceSentNotification($record));
                            }
                            Notification::make()
                                ->title('Facture envoyée')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Facture marquée comme envoyée')
                                ->body('Erreur envoi email: ' . $e->getMessage())
                                ->warning()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('markPaid')
                    ->label('Marquer payée')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => in_array($record->status, ['sent', 'overdue']))
                    ->action(fn ($record) => $record->markAsPaid()),

                Tables\Actions\Action::make('download')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('admin.invoices.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}

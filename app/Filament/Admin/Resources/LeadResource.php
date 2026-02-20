<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Contacts / Leads';

    protected static ?string $modelLabel = 'Contact';

    protected static ?string $pluralModelLabel = 'Contacts';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel(),

                        Forms\Components\Select::make('source')
                            ->label('Source')
                            ->options([
                                'popup' => 'Popup',
                                'footer' => 'Footer',
                                'landing' => 'Landing page',
                                'whatsapp_cta' => 'CTA WhatsApp',
                                'telegram_cta' => 'CTA Telegram',
                                'checkout' => 'Checkout',
                            ])
                            ->required(),
                    ]),

                Forms\Components\Section::make('Abonnements')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Toggle::make('subscribed_newsletter')
                                    ->label('Newsletter'),
                                Forms\Components\Toggle::make('subscribed_whatsapp')
                                    ->label('WhatsApp'),
                                Forms\Components\Toggle::make('subscribed_telegram')
                                    ->label('Telegram'),
                                Forms\Components\Toggle::make('subscribed_sms')
                                    ->label('SMS'),
                            ]),
                    ]),

                Forms\Components\Section::make('Tracking')
                    ->schema([
                        Forms\Components\TextInput::make('source_page')
                            ->label('Page source')
                            ->disabled(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('utm_source')
                                    ->label('UTM Source')
                                    ->disabled(),
                                Forms\Components\TextInput::make('utm_medium')
                                    ->label('UTM Medium')
                                    ->disabled(),
                                Forms\Components\TextInput::make('utm_campaign')
                                    ->label('UTM Campaign')
                                    ->disabled(),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'popup' => 'primary',
                        'footer' => 'success',
                        'landing' => 'warning',
                        'whatsapp_cta', 'telegram_cta' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('subscribed_newsletter')
                    ->label('NL')
                    ->boolean(),

                Tables\Columns\IconColumn::make('subscribed_whatsapp')
                    ->label('WA')
                    ->boolean(),

                Tables\Columns\IconColumn::make('subscribed_telegram')
                    ->label('TG')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->label('Source')
                    ->options([
                        'popup' => 'Popup',
                        'footer' => 'Footer',
                        'landing' => 'Landing page',
                        'whatsapp_cta' => 'CTA WhatsApp',
                        'telegram_cta' => 'CTA Telegram',
                    ]),

                Tables\Filters\TernaryFilter::make('subscribed_newsletter')
                    ->label('Newsletter'),

                Tables\Filters\TernaryFilter::make('subscribed_whatsapp')
                    ->label('WhatsApp'),

                Tables\Filters\Filter::make('has_email')
                    ->label('Avec email')
                    ->query(fn ($query) => $query->whereNotNull('email')),

                Tables\Filters\Filter::make('has_phone')
                    ->label('Avec téléphone')
                    ->query(fn ($query) => $query->whereNotNull('phone')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Exporter CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $csv = "Nom,Email,Téléphone,Source,Newsletter,WhatsApp,Telegram,Date\n";
                            foreach ($records as $lead) {
                                $csv .= "\"{$lead->name}\",\"{$lead->email}\",\"{$lead->phone}\",\"{$lead->source}\",";
                                $csv .= ($lead->subscribed_newsletter ? 'Oui' : 'Non') . ',';
                                $csv .= ($lead->subscribed_whatsapp ? 'Oui' : 'Non') . ',';
                                $csv .= ($lead->subscribed_telegram ? 'Oui' : 'Non') . ',';
                                $csv .= "\"{$lead->created_at->format('d/m/Y H:i')}\"\n";
                            }

                            return response()->streamDownload(
                                fn () => print($csv),
                                'leads-' . now()->format('Y-m-d') . '.csv'
                            );
                        }),
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
            'index' => Pages\ListLeads::route('/'),
        ];
    }
}

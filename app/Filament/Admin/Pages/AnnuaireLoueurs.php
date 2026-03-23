<?php

namespace App\Filament\Admin\Pages;

use App\Models\Loueur;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AnnuaireLoueurs extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-phone-arrow-up-right';

    protected static string $view = 'filament.admin.pages.annuaire-loueurs';

    protected static ?string $navigationLabel = 'Annuaire Loueurs';

    protected static ?string $title = 'Annuaire Loueurs — Contacts rapides';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Gestion';

    public static function getNavigationBadge(): ?string
    {
        return (string) Loueur::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loueur::query()
                    ->with(['user'])
                    ->where('is_active', true)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=R&background=3b82f6&color=fff'),

                Tables\Columns\TextColumn::make('company_name')
                    ->label('Agence / Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Loueur $r) => $r->account_type === 'taxi' ? 'Chauffeur/Taxi' : 'Loueur'),

                Tables\Columns\TextColumn::make('wilaya')
                    ->label('Wilaya')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email compte')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('email_contact')
                    ->label('Email contact')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->size('sm')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->size('sm')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->size('sm')
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Vérifié')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('account_type')
                    ->label('Type')
                    ->options([
                        'loueur' => 'Loueur',
                        'taxi'   => 'Chauffeur / Taxi',
                    ]),

                Tables\Filters\SelectFilter::make('wilaya')
                    ->label('Wilaya')
                    ->options(fn () => Loueur::whereNotNull('wilaya')
                        ->where('is_active', true)
                        ->distinct()
                        ->pluck('wilaya', 'wilaya')
                        ->sort()
                        ->toArray()
                    )
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Vérifié'),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (Loueur $r) => $r->whatsapp
                        ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $r->whatsapp) . '?text=' . urlencode('Bonjour ' . ($r->company_name ?? '') . ', je vous contacte depuis ResaDZ.')
                        : null
                    )
                    ->openUrlInNewTab()
                    ->visible(fn (Loueur $r) => !empty($r->whatsapp)),

                Tables\Actions\Action::make('whatsapp_phone')
                    ->label('WhatsApp (tél)')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (Loueur $r) => $r->phone
                        ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $r->phone) . '?text=' . urlencode('Bonjour ' . ($r->company_name ?? '') . ', je vous contacte depuis ResaDZ.')
                        : null
                    )
                    ->openUrlInNewTab()
                    ->visible(fn (Loueur $r) => empty($r->whatsapp) && !empty($r->phone)),

                Tables\Actions\Action::make('email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->url(fn (Loueur $r) => 'mailto:' . ($r->email_contact ?: $r->user?->email))
                    ->openUrlInNewTab()
                    ->visible(fn (Loueur $r) => !empty($r->email_contact) || !empty($r->user?->email)),

                Tables\Actions\Action::make('appeler')
                    ->label('Appeler')
                    ->icon('heroicon-o-phone')
                    ->color('warning')
                    ->url(fn (Loueur $r) => 'tel:' . $r->phone)
                    ->openUrlInNewTab()
                    ->visible(fn (Loueur $r) => !empty($r->phone)),

                Tables\Actions\Action::make('voir')
                    ->label('Fiche')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Loueur $r) => route('filament.admin.resources.loueurs.edit', $r->id))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('company_name')
            ->striped()
            ->paginated([25, 50, 100])
            ->poll('60s');
    }
}

<?php

namespace App\Filament\Admin\Pages;

use App\Models\Booking;
use App\Models\DeliveryBooking;
use App\Models\Loueur;
use App\Models\TransferBooking;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class Revenues extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Revenus & Commissions';
    protected static ?string $title = 'Gestion des Revenus';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.admin.pages.revenues';

    public function getViewData(): array
    {
        $currentMonth = now()->format('Y-m');

        // Stats globales - Locations (loueurs)
        $totalCommissionDueLoueurs = Booking::whereIn('status', ['confirmed', 'active', 'completed'])
            ->where('commission_paid', false)
            ->sum('commission_amount');

        $totalCommissionPaidLoueurs = Booking::where('commission_paid', true)
            ->whereMonth('commission_paid_at', now()->month)
            ->whereYear('commission_paid_at', now()->year)
            ->sum('commission_amount');

        // Stats globales - Transferts (chauffeurs)
        $totalCommissionDueTransfers = TransferBooking::whereIn('status', ['confirmed', 'completed'])
            ->where('commission_paid', false)
            ->sum('commission_amount');

        $totalCommissionPaidTransfers = TransferBooking::where('commission_paid', true)
            ->whereMonth('commission_paid_at', now()->month)
            ->whereYear('commission_paid_at', now()->year)
            ->sum('commission_amount');

        // Stats globales - Livraisons (chauffeurs)
        $totalCommissionDueDeliveries = DeliveryBooking::whereIn('status', ['confirmed', 'picked_up', 'in_transit', 'delivered'])
            ->where('commission_paid', false)
            ->sum('commission_amount');

        $totalCommissionPaidDeliveries = DeliveryBooking::where('commission_paid', true)
            ->whereMonth('commission_paid_at', now()->month)
            ->whereYear('commission_paid_at', now()->year)
            ->sum('commission_amount');

        // Totaux combinés
        $totalCommissionDue = $totalCommissionDueLoueurs + $totalCommissionDueTransfers + $totalCommissionDueDeliveries;
        $totalCommissionPaid = $totalCommissionPaidLoueurs + $totalCommissionPaidTransfers + $totalCommissionPaidDeliveries;

        $bookingsThisMonth = Booking::whereIn('status', ['confirmed', 'active', 'completed'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $transfersThisMonth = TransferBooking::whereIn('status', ['confirmed', 'completed'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $deliveriesThisMonth = DeliveryBooking::whereIn('status', ['confirmed', 'picked_up', 'in_transit', 'delivered'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $loueursInTrial = Loueur::inTrial()->count();
        $loueursActive = Loueur::active()->count();
        $loueursSuspended = Loueur::suspended()->count();

        // Nombre de taxis/chauffeurs
        $taxisActive = Loueur::where('account_type', 'taxi')->active()->count();

        return [
            'totalCommissionDue' => $totalCommissionDue,
            'totalCommissionPaid' => $totalCommissionPaid,
            'totalCommissionDueLoueurs' => $totalCommissionDueLoueurs,
            'totalCommissionDueTransfers' => $totalCommissionDueTransfers,
            'totalCommissionDueDeliveries' => $totalCommissionDueDeliveries,
            'bookingsThisMonth' => $bookingsThisMonth,
            'transfersThisMonth' => $transfersThisMonth,
            'deliveriesThisMonth' => $deliveriesThisMonth,
            'loueursInTrial' => $loueursInTrial,
            'loueursActive' => $loueursActive,
            'loueursSuspended' => $loueursSuspended,
            'taxisActive' => $taxisActive,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Loueur::query()->withCount([
                'bookings as total_bookings',
                'bookings as unpaid_bookings_count' => fn ($q) => $q
                    ->whereIn('status', ['confirmed', 'active', 'completed'])
                    ->where('commission_paid', false),
                'transferBookings as unpaid_transfers_count' => fn ($q) => $q
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->where('commission_paid', false),
                'deliveryBookings as unpaid_deliveries_count' => fn ($q) => $q
                    ->whereIn('status', ['confirmed', 'picked_up', 'in_transit', 'delivered'])
                    ->where('commission_paid', false),
            ])->withSum([
                'bookings as unpaid_commission_locations' => fn ($q) => $q
                    ->whereIn('status', ['confirmed', 'active', 'completed'])
                    ->where('commission_paid', false),
            ], 'commission_amount')
            ->withSum([
                'transferBookings as unpaid_commission_transfers' => fn ($q) => $q
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->where('commission_paid', false),
            ], 'commission_amount')
            ->withSum([
                'deliveryBookings as unpaid_commission_deliveries' => fn ($q) => $q
                    ->whereIn('status', ['confirmed', 'picked_up', 'in_transit', 'delivered'])
                    ->where('commission_paid', false),
            ], 'commission_amount'))
            ->columns([
                TextColumn::make('company_name')
                    ->label('Loueur/Chauffeur')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->isTaxi() ? 'Chauffeur/Taxi' : 'Loueur'),

                TextColumn::make('account_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => $state === 'taxi' ? 'warning' : 'info')
                    ->formatStateUsing(fn ($state) => $state === 'taxi' ? 'Taxi' : 'Loueur'),

                TextColumn::make('wilaya')
                    ->label('Wilaya')
                    ->searchable(),

                TextColumn::make('trial_ends_at')
                    ->label('Fin essai')
                    ->date('d/m/Y')
                    ->color(fn ($record) => $record->isInTrial() ? 'success' : ($record->isTrialExpired() ? 'warning' : 'gray'))
                    ->description(fn ($record) => $record->isInTrial() ? 'En cours' : ($record->trial_ends_at ? 'Expiré' : 'Non défini')),

                // Commission locations (loueurs) - taux dégressif
                TextColumn::make('unpaid_commission_locations')
                    ->label('Comm. Locations')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->color(fn ($state) => ($state ?? 0) > 0 ? 'danger' : 'success')
                    ->description(fn ($record) => $record->unpaid_bookings_count . ' résas'),

                // Commission transferts (chauffeurs) - 10% fixe
                TextColumn::make('unpaid_commission_transfers')
                    ->label('Comm. Transferts')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->color(fn ($state) => ($state ?? 0) > 0 ? 'warning' : 'success')
                    ->description(fn ($record) => ($record->unpaid_transfers_count ?? 0) . ' courses (10%)'),

                // Commission livraisons (chauffeurs) - 10% fixe
                TextColumn::make('unpaid_commission_deliveries')
                    ->label('Comm. Livraisons')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->color(fn ($state) => ($state ?? 0) > 0 ? 'warning' : 'success')
                    ->description(fn ($record) => ($record->unpaid_deliveries_count ?? 0) . ' livraisons (10%)'),

                // Total commission due
                TextColumn::make('total_unpaid')
                    ->label('TOTAL DÛ')
                    ->getStateUsing(fn ($record) =>
                        ($record->unpaid_commission_locations ?? 0) +
                        ($record->unpaid_commission_transfers ?? 0) +
                        ($record->unpaid_commission_deliveries ?? 0)
                    )
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', ' ') . ' DA')
                    ->color(fn ($state) => ($state ?? 0) > 0 ? 'danger' : 'success')
                    ->weight('bold'),

                IconColumn::make('is_suspended')
                    ->label('Statut')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->filters([
                SelectFilter::make('account_type')
                    ->label('Type de compte')
                    ->options([
                        'loueur' => 'Loueurs',
                        'taxi' => 'Taxis/Chauffeurs',
                    ]),

                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Actifs',
                        'trial' => 'En essai',
                        'trial_expired' => 'Essai expiré',
                        'suspended' => 'Suspendus',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value']) {
                            'active' => $query->where('is_active', true)->where('is_suspended', false),
                            'trial' => $query->inTrial(),
                            'trial_expired' => $query->trialExpired(),
                            'suspended' => $query->where('is_suspended', true),
                            default => $query,
                        };
                    }),

                Filter::make('has_unpaid')
                    ->label('Commission impayée')
                    ->query(fn (Builder $query) => $query->where(function ($q) {
                        $q->whereHas('bookings', fn ($bq) => $bq
                            ->whereIn('status', ['confirmed', 'active', 'completed'])
                            ->where('commission_paid', false)
                        )
                        ->orWhereHas('transferBookings', fn ($tq) => $tq
                            ->whereIn('status', ['confirmed', 'completed'])
                            ->where('commission_paid', false)
                        )
                        ->orWhereHas('deliveryBookings', fn ($dq) => $dq
                            ->whereIn('status', ['confirmed', 'picked_up', 'in_transit', 'delivered'])
                            ->where('commission_paid', false)
                        );
                    })),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('extend_trial')
                        ->label('Prolonger essai')
                        ->icon('heroicon-o-calendar-days')
                        ->color('success')
                        ->form([
                            DatePicker::make('trial_ends_at')
                                ->label('Nouvelle date de fin')
                                ->default(fn ($record) => $record->trial_ends_at ?? now()->addMonth())
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update(['trial_ends_at' => $data['trial_ends_at']]);
                            Notification::make()->success()->title('Essai prolongé')->send();
                        }),

                    Action::make('mark_paid')
                        ->label('Marquer tout payé')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Marquer les commissions comme payées')
                        ->modalDescription(fn ($record) => "Marquer TOUTES les commissions impayées (locations, transferts, livraisons) de {$record->company_name} comme payées ?")
                        ->action(function ($record) {
                            // Marquer les bookings (locations)
                            $record->bookings()
                                ->whereIn('status', ['confirmed', 'active', 'completed'])
                                ->where('commission_paid', false)
                                ->update([
                                    'commission_paid' => true,
                                    'commission_paid_at' => now(),
                                ]);

                            // Marquer les transferts
                            $record->transferBookings()
                                ->whereIn('status', ['confirmed', 'completed'])
                                ->where('commission_paid', false)
                                ->update([
                                    'commission_paid' => true,
                                    'commission_paid_at' => now(),
                                ]);

                            // Marquer les livraisons
                            $record->deliveryBookings()
                                ->whereIn('status', ['confirmed', 'picked_up', 'in_transit', 'delivered'])
                                ->where('commission_paid', false)
                                ->update([
                                    'commission_paid' => true,
                                    'commission_paid_at' => now(),
                                ]);

                            $record->update(['commission_paid_until' => now()]);
                            Notification::make()->success()->title('Toutes les commissions marquées payées')->send();
                        })
                        ->visible(fn ($record) =>
                            ($record->unpaid_commission_locations ?? 0) > 0 ||
                            ($record->unpaid_commission_transfers ?? 0) > 0 ||
                            ($record->unpaid_commission_deliveries ?? 0) > 0
                        ),

                    Action::make('view_bookings')
                        ->label('Voir réservations')
                        ->icon('heroicon-o-eye')
                        ->url(fn ($record) => route('filament.admin.resources.loueurs.view', $record))
                        ->openUrlInNewTab(),

                    Action::make('suspend')
                        ->label(fn ($record) => $record->is_suspended ? 'Réactiver' : 'Suspendre')
                        ->icon(fn ($record) => $record->is_suspended ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                        ->color(fn ($record) => $record->is_suspended ? 'success' : 'danger')
                        ->form([
                            Textarea::make('suspension_reason')
                                ->label('Raison')
                                ->visible(fn ($record) => !$record->is_suspended),
                        ])
                        ->action(function ($record, array $data) {
                            if ($record->is_suspended) {
                                $record->update([
                                    'is_suspended' => false,
                                    'suspension_reason' => null,
                                ]);
                                Notification::make()->success()->title('Compte réactivé')->send();
                            } else {
                                $record->update([
                                    'is_suspended' => true,
                                    'suspension_reason' => $data['suspension_reason'] ?? null,
                                ]);
                                Notification::make()->warning()->title('Compte suspendu')->send();
                            }
                        }),
                ]),
            ])
            ->defaultSort('company_name', 'asc')
            ->striped();
    }
}

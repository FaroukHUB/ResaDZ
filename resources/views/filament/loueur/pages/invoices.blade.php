<x-filament-panels::page>
    <div class="space-y-8" style="background: #F8FAFF; min-height: 100vh; margin: -1.5rem; padding: 1.5rem;">

        {{-- Hero Card --}}
        <div class="hero-card" style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); border-radius: 1.25rem; padding: 2.25rem; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -30px; right: -30px; width: 140px; height: 140px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -40px; right: 80px; width: 100px; height: 100px; background: rgba(255,255,255,0.07); border-radius: 50%;"></div>
            <div style="position: absolute; top: 50%; left: 80%; width: 60px; height: 60px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-4">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); backdrop-filter: blur(12px); border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 style="font-size: 1.625rem; font-weight: 800; margin: 0; letter-spacing: -0.025em;">Mes factures</h2>
                        <p style="color: rgba(255,255,255,0.9); font-size: 0.9375rem; margin-top: 0.375rem; font-weight: 500;">Consultez et téléchargez vos factures ResaDZ</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total factures --}}
            <div class="stat-card stat-card-indigo" style="background: white; border-radius: 1.25rem; padding: 1.75rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); transition: all 0.2s ease; border: 1px solid rgba(99,102,241,0.12);"
                 onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 32px rgba(99,102,241,0.14)'"
                 onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 24px rgba(99,102,241,0.08)'">
                <div class="flex items-center gap-4">
                    <div class="icon-container icon-container-lg" style="width: 60px; height: 60px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 0.8125rem; color: #64748B; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total factures</p>
                        <p style="font-size: 2.5rem; font-weight: 800; color: #1E293B; line-height: 1; margin-top: 0.25rem;">{{ $invoices->count() }}</p>
                    </div>
                </div>
            </div>

            {{-- En attente --}}
            <div class="stat-card stat-card-amber" style="background: white; border-radius: 1.25rem; padding: 1.75rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); transition: all 0.2s ease; border: 1px solid rgba(245,158,11,0.15);"
                 onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 32px rgba(99,102,241,0.14)'"
                 onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 24px rgba(99,102,241,0.08)'">
                <div class="flex items-center gap-4">
                    <div class="icon-container icon-container-lg" style="width: 60px; height: 60px; background: linear-gradient(135deg, #F59E0B, #D97706); border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 0.8125rem; color: #64748B; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">En attente</p>
                        <p style="font-size: 2rem; font-weight: 800; color: #F59E0B; line-height: 1; margin-top: 0.25rem;">{{ number_format($totalUnpaid, 0, ',', ' ') }} <span style="font-size: 0.875rem; font-weight: 600;">DA</span></p>
                    </div>
                </div>
            </div>

            {{-- Payé --}}
            <div class="stat-card stat-card-green" style="background: white; border-radius: 1.25rem; padding: 1.75rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); transition: all 0.2s ease; border: 1px solid rgba(16,185,129,0.15);"
                 onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 32px rgba(99,102,241,0.14)'"
                 onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 24px rgba(99,102,241,0.08)'">
                <div class="flex items-center gap-4">
                    <div class="icon-container icon-container-lg" style="width: 60px; height: 60px; background: linear-gradient(135deg, #10B981, #059669); border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 0.8125rem; color: #64748B; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Payé</p>
                        <p style="font-size: 2rem; font-weight: 800; color: #10B981; line-height: 1; margin-top: 0.25rem;">{{ number_format($totalPaid, 0, ',', ' ') }} <span style="font-size: 0.875rem; font-weight: 600;">DA</span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoices List - Card Layout --}}
        <div>
            <div class="flex items-center gap-3 mb-5">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1E293B;">Historique des factures</h3>
            </div>

            @forelse($invoices as $invoice)
                <div class="invoice-card card-modern" style="background: white; border-radius: 1.25rem; padding: 1.5rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); transition: all 0.2s ease; border: 1px solid rgba(226,232,240,0.6); margin-bottom: 1rem;"
                     onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 32px rgba(99,102,241,0.14)'"
                     onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 24px rgba(99,102,241,0.08)'">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        {{-- Left: Invoice info --}}
                        <div class="flex items-start gap-4 flex-1">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <p style="font-weight: 700; color: #1E293B; font-size: 0.9375rem;">{{ $invoice->invoice_number }}</p>
                                    {{-- Status badge --}}
                                    @php
                                        $badgeStyles = match($invoice->status) {
                                            'paid' => 'background: #ECFDF5; color: #059669; border: 1px solid rgba(16,185,129,0.2);',
                                            'sent' => 'background: #FFFBEB; color: #D97706; border: 1px solid rgba(245,158,11,0.2);',
                                            'cancelled' => 'background: #F1F5F9; color: #64748B; border: 1px solid rgba(100,116,139,0.2);',
                                            'overdue' => 'background: #FEF2F2; color: #DC2626; border: 1px solid rgba(239,68,68,0.2);',
                                            default => 'background: #F1F5F9; color: #64748B; border: 1px solid rgba(100,116,139,0.2);',
                                        };
                                        $badgeClass = match($invoice->status) {
                                            'paid' => 'badge-success',
                                            'sent' => 'badge-warning',
                                            'cancelled' => 'badge-info',
                                            'overdue' => 'badge-danger',
                                            default => 'badge-info',
                                        };
                                    @endphp
                                    <span class="badge-modern {{ $badgeClass }}" style="display: inline-flex; align-items: center; font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.75rem; border-radius: 999px; {{ $badgeStyles }}">
                                        {{ $statuses[$invoice->status] ?? $invoice->status }}
                                    </span>
                                    @if($invoice->due_date < now() && in_array($invoice->status, ['sent']))
                                        <span class="badge-modern badge-danger" style="display: inline-flex; align-items: center; font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.75rem; border-radius: 999px; background: #FEF2F2; color: #DC2626; border: 1px solid rgba(239,68,68,0.2);">
                                            En retard
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-1" style="font-size: 0.8125rem; color: #64748B;">
                                    <svg class="w-3.5 h-3.5" style="color: #94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $invoice->issue_date->format('d/m/Y') }}</span>
                                </div>
                                {{-- Description --}}
                                <p style="font-size: 0.875rem; color: #64748B; margin-top: 0.5rem; line-height: 1.5;">
                                    @if($invoice->items->count() > 0)
                                        {{ \Str::limit($invoice->items->first()->description, 40) }}
                                        @if($invoice->items->count() > 1)
                                            <span style="color: #94A3B8;">(+{{ $invoice->items->count() - 1 }})</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Right: Amount + PDF --}}
                        <div class="flex items-center gap-4 sm:flex-col sm:items-end sm:gap-3">
                            <p style="font-size: 1.375rem; font-weight: 800; color: #1E293B; white-space: nowrap;">{{ number_format($invoice->total, 2, ',', ' ') }} <span style="font-size: 0.875rem; font-weight: 600; color: #64748B;">DA</span></p>
                            <a href="{{ route('admin.invoices.pdf', $invoice) }}"
                               target="_blank"
                               class="btn-modern"
                               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.125rem; font-size: 0.8125rem; font-weight: 700; color: white; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 12px; text-decoration: none; transition: all 0.2s ease; white-space: nowrap; border: none;"
                               onmouseenter="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 16px rgba(99,102,241,0.35)'"
                               onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                PDF
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state card-modern" style="background: white; border-radius: 1.25rem; padding: 4rem 2rem; text-align: center; box-shadow: 0 4px 24px rgba(99,102,241,0.08);">
                    <div class="empty-state-icon" style="width: 80px; height: 80px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p style="color: #1E293B; font-weight: 700; font-size: 1.125rem;">Aucune facture pour le moment</p>
                    <p style="color: #64748B; font-size: 0.9375rem; margin-top: 0.5rem;">Vos factures apparaîtront ici après vos premières réservations</p>
                </div>
            @endforelse
        </div>

        {{-- Guide Section --}}
        <div class="card-modern" style="background: white; border-radius: 1.25rem; padding: 1.75rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.1);">
            <div class="flex items-start gap-4">
                <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p style="font-weight: 700; color: #1E293B; font-size: 1rem; margin-bottom: 0.75rem;">Comprendre vos factures</p>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem 1.5rem;">
                        <div class="flex items-start gap-2" style="font-size: 0.875rem; color: #64748B; line-height: 1.5;">
                            <span style="color: #6366F1; font-weight: 700; flex-shrink: 0;">Commission</span>
                            <span>: 10% prélevés sur chaque réservation complétée</span>
                        </div>
                        <div class="flex items-start gap-2" style="font-size: 0.875rem; color: #64748B; line-height: 1.5;">
                            <span style="color: #6366F1; font-weight: 700; flex-shrink: 0;">Boosts</span>
                            <span>: Factures séparées pour les mises en avant</span>
                        </div>
                        <div class="flex items-start gap-2" style="font-size: 0.875rem; color: #64748B; line-height: 1.5;">
                            <span style="color: #6366F1; font-weight: 700; flex-shrink: 0;">En attente</span>
                            <span>: Factures à régler avant la date d'échéance</span>
                        </div>
                        <div class="flex items-start gap-2" style="font-size: 0.875rem; color: #64748B; line-height: 1.5;">
                            <span style="color: #6366F1; font-weight: 700; flex-shrink: 0;">PDF</span>
                            <span>: Cliquez pour télécharger une version imprimable</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Instructions --}}
        <div class="card-modern" style="background: linear-gradient(135deg, #F8FAFF, #EEF2FF); border-radius: 1.25rem; padding: 2rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.12);">
            <div class="flex items-center gap-3 mb-5">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1E293B;">Modalités de paiement</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div style="background: white; border-radius: 14px; padding: 1.25rem; border: 1px solid rgba(99,102,241,0.1);">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5" style="color: #6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <p style="font-weight: 700; color: #1E293B; font-size: 0.9375rem;">Virement bancaire</p>
                    </div>
                    <p style="font-size: 0.875rem; color: #64748B; line-height: 1.5;">Contactez-nous pour les coordonnées bancaires</p>
                </div>
                <div style="background: white; border-radius: 14px; padding: 1.25rem; border: 1px solid rgba(99,102,241,0.1);">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5" style="color: #6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p style="font-weight: 700; color: #1E293B; font-size: 0.9375rem;">Paiement en espèces</p>
                    </div>
                    <p style="font-size: 0.875rem; color: #64748B; line-height: 1.5;">Rendez-vous dans nos locaux</p>
                </div>
            </div>
            <div style="margin-top: 1.25rem; background: white; border-radius: 12px; padding: 0.875rem 1.25rem; border: 1px solid rgba(99,102,241,0.1); display: flex; align-items: center; gap: 0.75rem;">
                <svg class="w-5 h-5" style="color: #6366F1; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p style="font-size: 0.875rem; color: #64748B; line-height: 1.5;">
                    Pour toute question concernant vos factures, contactez-nous via la messagerie.
                </p>
            </div>
        </div>
    </div>
</x-filament-panels::page>

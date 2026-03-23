<x-filament-panels::page>
    @php
        $daysArray = collect($days)->map(fn($d) => $d->format('Y-m-d'))->values()->toArray();

        $vehiclesArray = $vehicles->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->full_name,
            'price' => number_format($v->price_per_day, 0, ',', ' '),
            'image' => $v->image ? asset('storage/' . $v->image) : null,
        ])->values()->toArray();

        $firstDay = $days[0] ?? now()->startOfMonth();
        $startDow = ($firstDay->dayOfWeekIso - 1);

        $selectedVid = $vehicles->first()?->id ?? 0;
        $currentStats = $statsData[$selectedVid] ?? ['available' => 0, 'blocked' => 0, 'booked' => 0];
    @endphp

    <style>
        .cal-page { background: #F8FAFF; max-width: 900px; margin: 0 auto; }
        .cal-vehicle-select {
            appearance: none; background: white; border: 2px solid #E2E8F0;
            border-radius: 16px; padding: 14px 48px 14px 56px; font-size: 15px;
            font-weight: 600; color: #1E293B; width: 100%; cursor: pointer;
            transition: all 0.2s; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 16px center;
        }
        .cal-vehicle-select:focus { border-color: #FF6B2C; box-shadow: 0 0 0 3px rgba(255,107,44,0.15); outline: none; }
        .cal-vehicle-select:hover { border-color: #CBD5E1; }
        .cal-nav-btn {
            width: 40px; height: 40px; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; border: 1px solid #E2E8F0;
            background: #F8FAFC; color: #64748B; cursor: pointer; transition: all 0.2s;
        }
        .cal-nav-btn:hover { background: #FFF3ED; border-color: #FF6B2C; color: #FF6B2C; }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
        .cal-day-header-cell {
            text-align: center; padding: 8px 0; font-size: 12px;
            font-weight: 700; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.05em;
        }
        .cal-day {
            position: relative; min-height: 80px; border: 1px solid #E2E8F0;
            border-radius: 12px; padding: 8px; background: white; transition: all 0.15s;
            cursor: default; overflow: hidden;
        }
        .cal-day-num { font-size: 14px; font-weight: 500; color: #1E293B; line-height: 1; }
        .cal-day-icon { position: absolute; bottom: 6px; right: 8px; font-size: 12px; opacity: 0.7; }

        /* States — rendered server-side */
        .cal-day--empty { background: transparent; border-color: transparent; }
        .cal-day--past { background: #F1F5F9; border-color: #E2E8F0; }
        .cal-day--past .cal-day-num { color: #94A3B8; }
        .cal-day--available { cursor: pointer; }
        .cal-day--available:hover { background: #FFF3ED; border-color: #FF6B2C; box-shadow: 0 2px 8px rgba(255,107,44,0.1); transform: scale(1.02); }
        .cal-day--blocked { background: #DC2626; cursor: pointer; }
        .cal-day--blocked .cal-day-num { color: white; }
        .cal-day--blocked .cal-day-icon { color: white; opacity: 0.8; }
        .cal-day--blocked:hover { background: #B91C1C; }
        .cal-day--booked { background: #059669; }
        .cal-day--booked .cal-day-num { color: white; }
        .cal-day--booked .cal-day-icon { color: white; opacity: 0.8; }
        .cal-day--maintenance { background: #F59E0B; }
        .cal-day--maintenance .cal-day-num { color: white; }
        .cal-day--maintenance .cal-day-icon { color: white; opacity: 0.8; }
        .cal-day--unavailable { background: #F1F5F9; }
        .cal-day--unavailable .cal-day-num { color: #CBD5E1; }
        .cal-day--no-vehicle { background: #F8FAFF; cursor: not-allowed; }
        .cal-day--no-vehicle .cal-day-num { color: #CBD5E1; }

        /* Today */
        .cal-day--today .cal-day-num {
            background: #FF6B2C; color: white !important; width: 28px; height: 28px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
        }

        /* Range selection — Alpine overlay */
        .cal-day--in-range { background: #FFF3ED !important; border: 2px dashed #FF6B2C !important; }
        .cal-day--range-start { background: #FF6B2C !important; }
        .cal-day--range-start .cal-day-num { color: white !important; }
        .cal-day--selecting { cursor: crosshair !important; }

        /* Legend */
        .cal-legend-dot { width: 14px; height: 14px; border-radius: 4px; flex-shrink: 0; }

        /* Stats */
        .cal-stat {
            background: white; border-radius: 16px; padding: 20px; flex: 1;
            border: 1px solid #E2E8F0; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .cal-stat-num { font-size: 28px; font-weight: 800; line-height: 1; }
        .cal-stat-label { font-size: 13px; color: #64748B; margin-top: 4px; font-weight: 500; }

        /* Panel */
        .cal-panel {
            position: fixed; top: 0; right: 0; bottom: 0; width: 380px; max-width: 90vw;
            background: white; z-index: 100; box-shadow: -8px 0 40px rgba(0,0,0,0.12);
            transform: translateX(100%); transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
            overflow-y: auto;
        }
        .cal-panel--open { transform: translateX(0); }
        .cal-panel-backdrop {
            position: fixed; inset: 0; background: rgba(0,0,0,0.3);
            backdrop-filter: blur(2px); z-index: 99;
        }
        .cal-reason-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 24px; border: 2px solid #E2E8F0;
            background: white; font-size: 14px; font-weight: 500; color: #475569;
            cursor: pointer; transition: all 0.2s;
        }
        .cal-reason-pill:hover { border-color: #FF6B2C; color: #FF6B2C; }
        .cal-reason-pill--active { border-color: #FF6B2C; background: #FFF3ED; color: #FF6B2C; }

        .cal-slide-enter { animation: calSlideIn 0.25s ease-out; }
        @keyframes calSlideIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Dark mode */
        .dark .cal-page { background: #111827; }
        .dark .cal-vehicle-select { background: #1F2937; border-color: #374151; color: #F9FAFB; }
        .dark .cal-vehicle-select:focus { border-color: #FF6B2C; }
        .dark .cal-nav-btn { background: #1F2937; border-color: #374151; color: #9CA3AF; }
        .dark .cal-nav-btn:hover { background: #FF6B2C20; }
        .dark .cal-day { background: #1F2937; border-color: #374151; }
        .dark .cal-day-num { color: #E5E7EB; }
        .dark .cal-day--past { background: #111827; }
        .dark .cal-day--past .cal-day-num { color: #4B5563; }
        .dark .cal-day--available:hover { background: #FF6B2C15; border-color: #FF6B2C; }
        .dark .cal-day--blocked { background: #991B1B; }
        .dark .cal-day--blocked .cal-day-num { color: white; }
        .dark .cal-day--booked { background: #065F46; }
        .dark .cal-day--booked .cal-day-num { color: white; }
        .dark .cal-day--today .cal-day-num { background: #FF6B2C; }
        .dark .cal-day--in-range { background: #FF6B2C20 !important; border-color: #FF6B2C !important; }
        .dark .cal-stat { background: #1F2937; border-color: #374151; }
        .dark .cal-stat-label { color: #9CA3AF; }
        .dark .cal-panel { background: #1F2937; }
        .dark .cal-reason-pill { background: #111827; border-color: #374151; color: #D1D5DB; }

        @media (max-width: 640px) {
            .cal-day { min-height: 56px; padding: 4px; border-radius: 8px; }
            .cal-day-num { font-size: 12px; }
            .cal-day-icon { font-size: 10px; bottom: 2px; right: 4px; }
            .cal-stat { padding: 14px; }
            .cal-stat-num { font-size: 22px; }
            .cal-panel { width: 100%; max-width: 100%; }
        }
    </style>

    <div class="cal-page space-y-6"
        x-data="{
            selectedVehicleId: {{ $vehicles->first()?->id ?? 'null' }},
            vehicles: {{ Js::from($vehiclesArray) }},
            rangeStart: null,
            rangeEnd: null,
            hoverDate: null,
            selecting: false,
            showPanel: false,
            panelDates: { start: null, end: null },
            blockReason: '',
            blockNote: '',
            reasonOptions: [
                { key: 'entretien', label: 'Entretien', icon: '🔧' },
                { key: 'personnel', label: 'Usage personnel', icon: '🚗' },
                { key: 'hors_plateforme', label: 'Réservé hors plateforme', icon: '📦' },
                { key: 'autre', label: 'Autre', icon: '✏️' },
            ],
            get selectedVehicle() {
                return this.vehicles.find(v => v.id == this.selectedVehicleId) || null;
            },
            isInRange(dateStr) {
                if (!this.selecting || !this.rangeStart) return false;
                const end = this.hoverDate || this.rangeEnd;
                if (!end) return false;
                const s = this.rangeStart < end ? this.rangeStart : end;
                const e = this.rangeStart < end ? end : this.rangeStart;
                return dateStr >= s && dateStr <= e;
            },
            onDayClick(dateStr, status) {
                if (!this.selectedVehicleId) return;
                if (status === 'past' || status === 'booked' || status === 'maintenance' || status === 'unavailable' || status === 'no-vehicle') return;

                if (status === 'blocked') {
                    this.$wire.toggleBlock(parseInt(this.selectedVehicleId), dateStr);
                    return;
                }

                if (!this.selecting) {
                    this.rangeStart = dateStr;
                    this.selecting = true;
                    this.hoverDate = dateStr;
                } else {
                    this.rangeEnd = dateStr;
                    this.selecting = false;
                    const start = this.rangeStart < dateStr ? this.rangeStart : dateStr;
                    const end = this.rangeStart < dateStr ? dateStr : this.rangeStart;
                    if (start === end) {
                        this.$wire.toggleBlock(parseInt(this.selectedVehicleId), start);
                        this.resetRange();
                    } else {
                        this.panelDates = { start, end };
                        this.blockReason = '';
                        this.blockNote = '';
                        this.showPanel = true;
                    }
                }
            },
            onDayHover(dateStr) {
                if (this.selecting) this.hoverDate = dateStr;
            },
            cancelRange() { this.resetRange(); },
            resetRange() { this.rangeStart = null; this.rangeEnd = null; this.hoverDate = null; this.selecting = false; },
            confirmBlock() {
                if (!this.selectedVehicleId || !this.panelDates.start || !this.panelDates.end) return;
                let reason = '';
                const found = this.reasonOptions.find(r => r.key === this.blockReason);
                if (found) reason = found.label;
                if (this.blockNote) reason += (reason ? ' — ' : '') + this.blockNote;
                this.$wire.blockRange(parseInt(this.selectedVehicleId), this.panelDates.start, this.panelDates.end, reason || 'Bloqué manuellement');
                this.showPanel = false;
                this.resetRange();
            },
            cancelBlock() { this.showPanel = false; this.resetRange(); },
            formatDate(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr + 'T00:00:00');
                return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
            },
        }"
        @keyup.escape.window="if(selecting) cancelRange(); if(showPanel) cancelBlock();"
    >

        {{-- Vehicle selector --}}
        <div class="relative">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H6.375c-.621 0-1.125-.504-1.125-1.125V11.25" /></svg>
            </div>
            <select x-model="selectedVehicleId" class="cal-vehicle-select">
                @if($vehicles->count() > 1)
                    <option value="">Choisir un véhicule...</option>
                @endif
                @foreach($vehicles as $v)
                    <option value="{{ $v->id }}">{{ $v->full_name }} — {{ number_format($v->price_per_day, 0, ',', ' ') }} DA/j</option>
                @endforeach
                @if($vehicles->isEmpty())
                    <option value="" disabled>Aucun véhicule actif</option>
                @endif
            </select>
        </div>

        {{-- Month navigation --}}
        <div class="flex items-center justify-between">
            <button wire:click="previousMonth" class="cal-nav-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <div class="text-center">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white capitalize">{{ $monthName }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="goToToday" class="px-4 py-2 text-sm font-semibold rounded-full text-white transition" style="background: linear-gradient(135deg, #FF6B2C, #F59E0B);">
                    Aujourd'hui
                </button>
                <button wire:click="nextMonth" class="cal-nav-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- Selection hint --}}
        <div x-show="selecting" x-transition class="flex items-center gap-3 px-5 py-3 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700">
            <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zM12 2.25V4.5m5.834.166l-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243l-1.59-1.59"/></svg>
            <p class="text-sm text-orange-700 dark:text-orange-300 font-medium">
                Cliquez sur la date de fin pour bloquer la plage, ou recliquez la même date pour bloquer un seul jour. <button @click="cancelRange()" class="underline ml-1">Annuler</button>
            </p>
        </div>

        {{-- Calendar grid --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700 cal-slide-enter" wire:key="cal-{{ $currentMonth }}-{{ $currentYear }}">
            <div class="cal-grid">
                {{-- Day headers --}}
                <div class="cal-day-header-cell">L</div>
                <div class="cal-day-header-cell">M</div>
                <div class="cal-day-header-cell">M</div>
                <div class="cal-day-header-cell">J</div>
                <div class="cal-day-header-cell">V</div>
                <div class="cal-day-header-cell" style="color: #EF4444;">S</div>
                <div class="cal-day-header-cell" style="color: #EF4444;">D</div>

                {{-- Empty cells before first day --}}
                @for($i = 0; $i < $startDow; $i++)
                    <div class="cal-day cal-day--empty"></div>
                @endfor

                {{-- Day cells — status from PHP --}}
                @foreach($days as $day)
                    @php
                        $dateStr = $day->format('Y-m-d');
                        $dayNum = $day->format('j');
                        $isToday = ($dateStr === $today);

                        // Get status for the first vehicle (server-rendered default)
                        $defaultKey = $selectedVid . '_' . $dateStr;
                        $status = $statusMap[$defaultKey] ?? 'available';
                        if ($isToday && $status === 'past') $status = 'available';

                        $booking = $bookingInfo[$defaultKey] ?? null;
                        $block = $blockInfo[$defaultKey] ?? null;
                    @endphp

                    <div
                        class="cal-day cal-day--{{ $status }} @if($isToday) cal-day--today @endif"
                        :class="{
                            'cal-day--in-range': isInRange('{{ $dateStr }}') && '{{ $status }}' === 'available',
                            'cal-day--range-start': rangeStart === '{{ $dateStr }}' && selecting,
                            'cal-day--selecting': selecting && '{{ $status }}' === 'available',
                        }"
                        @click="onDayClick('{{ $dateStr }}', '{{ $status }}')"
                        @mouseenter="onDayHover('{{ $dateStr }}')"
                        @if($status === 'booked' && $booking)
                            title="Réservé par {{ $booking['client_name'] }} ({{ $booking['start'] }} → {{ $booking['end'] }})"
                        @elseif($status === 'blocked')
                            title="Bloqué — Cliquez pour débloquer"
                        @elseif($status === 'maintenance')
                            title="En maintenance{{ $block ? ' — ' . $block['reason'] : '' }}"
                        @endif
                        wire:key="day-{{ $dateStr }}"
                    >
                        <div class="cal-day-num">{{ $dayNum }}</div>

                        @if($status === 'blocked')
                            <div class="cal-day-icon">🔒</div>
                        @elseif($status === 'booked')
                            <div class="cal-day-icon">✓</div>
                        @elseif($status === 'maintenance')
                            <div class="cal-day-icon">🔧</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Legend --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Légende</p>
            <div class="flex flex-wrap items-center gap-6 text-sm">
                <div class="flex items-center gap-2.5">
                    <div class="cal-legend-dot" style="background: white; border: 2px solid #E2E8F0;"></div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Disponible</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="cal-legend-dot" style="background: #DC2626;"></div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Bloqué par vous</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="cal-legend-dot" style="background: #059669;"></div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Réservé</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="w-5 h-5 rounded-full" style="background: #FF6B2C;"></div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Aujourd'hui</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="cal-legend-dot" style="background: #F1F5F9; border: 1px solid #E2E8F0;"></div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Passé</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="cal-legend-dot" style="background: #F59E0B;"></div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Maintenance</span>
                </div>
            </div>
        </div>

        {{-- Monthly stats --}}
        @if($vehicles->isNotEmpty())
            <div class="flex gap-4">
                <div class="cal-stat">
                    <div class="cal-stat-num text-emerald-500">{{ $currentStats['available'] }}</div>
                    <div class="cal-stat-label">Jours disponibles</div>
                </div>
                <div class="cal-stat">
                    <div class="cal-stat-num text-red-500">{{ $currentStats['blocked'] }}</div>
                    <div class="cal-stat-label">Jours bloqués</div>
                </div>
                <div class="cal-stat">
                    <div class="cal-stat-num text-orange-500">{{ $currentStats['booked'] }}</div>
                    <div class="cal-stat-label">Jours réservés</div>
                </div>
            </div>
        @endif

        {{-- Toolbar --}}
        <div class="flex flex-wrap gap-3" x-data="{ showImportModal: false, importVehicleId: null, importUrl: '', copied: false }">
            <button @click="showImportModal = true" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition bg-white dark:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Importer un calendrier
            </button>
            <button @click="navigator.clipboard.writeText('{{ $icalUrl }}'); copied = true; setTimeout(() => copied = false, 2000)" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition bg-white dark:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <span x-text="copied ? 'Lien copié !' : 'Copier lien iCal'"></span>
            </button>

            {{-- Import modal --}}
            <template x-teleport="body">
                <div x-show="showImportModal" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none;">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showImportModal = false"></div>
                    <div x-show="showImportModal" x-transition class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Importer un calendrier</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Importer les blocages depuis un calendrier externe</p>
                            </div>
                            <button @click="showImportModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Véhicule</label>
                                <select x-model="importVehicleId" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 px-4 py-3">
                                    <option value="">-- Sélectionnez un véhicule --</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}">{{ $v->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Lien iCal</label>
                                <input x-model="importUrl" type="url" placeholder="https://calendar.google.com/calendar/ical/..." class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 px-4 py-3" />
                            </div>
                        </div>
                        <div class="flex gap-3 pt-1">
                            <button @click="showImportModal = false" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">Annuler</button>
                            <button @click="if(importUrl && importVehicleId) { $wire.importGoogleCalendar(parseInt(importVehicleId), importUrl); showImportModal = false; }" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition shadow-sm" :class="{ 'opacity-50 cursor-not-allowed': !importUrl || !importVehicleId }" :disabled="!importUrl || !importVehicleId">Importer</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Block panel (slide-in) --}}
        <template x-teleport="body">
            <div x-show="showPanel" x-transition.opacity class="cal-panel-backdrop" @click="cancelBlock()" style="display: none;"></div>
            <div class="cal-panel" :class="{ 'cal-panel--open': showPanel }" style="display: block;">
                <div class="p-6 space-y-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Bloquer des dates</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="'Du ' + formatDate(panelDates.start) + ' au ' + formatDate(panelDates.end)"></p>
                        </div>
                        <button @click="cancelBlock()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl" x-show="selectedVehicle">
                        <template x-if="selectedVehicle?.image">
                            <img :src="selectedVehicle.image" class="w-10 h-10 rounded-lg object-cover" />
                        </template>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedVehicle?.name"></div>
                            <div class="text-xs text-gray-500" x-text="selectedVehicle?.price + ' DA/jour'"></div>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Raison du blocage <span class="font-normal text-gray-400">(optionnel)</span></p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="opt in reasonOptions" :key="opt.key">
                                <button class="cal-reason-pill" :class="{ 'cal-reason-pill--active': blockReason === opt.key }" @click="blockReason = blockReason === opt.key ? '' : opt.key">
                                    <span x-text="opt.icon"></span>
                                    <span x-text="opt.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Note interne <span class="font-normal text-gray-400">(optionnel)</span></label>
                        <input x-model="blockNote" type="text" placeholder="Ex: Révision chez le garagiste" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-sm px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" />
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button @click="cancelBlock()" class="flex-1 px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">Annuler</button>
                        <button @click="confirmBlock()" class="flex-1 px-4 py-3 text-sm font-semibold text-white rounded-xl transition shadow-sm" style="background: linear-gradient(135deg, #EF4444, #FF6B2C);">Bloquer ces dates</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Loading overlay --}}
    <div wire:loading.flex class="fixed inset-0 z-[110] items-center justify-center bg-black/20 backdrop-blur-[2px]">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl px-8 py-5 flex items-center gap-4">
            <svg class="animate-spin h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Mise à jour...</span>
        </div>
    </div>
</x-filament-panels::page>

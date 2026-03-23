<x-filament-panels::page>
    @php
        // Build JSON maps for Alpine
        $daysArray = collect($days)->map(fn($d) => $d->format('Y-m-d'))->values()->toArray();

        $vehiclesArray = $vehicles->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->full_name,
            'price' => number_format($v->price_per_day, 0, ',', ' '),
            'image' => $v->image ? asset('storage/' . $v->image) : null,
        ])->values()->toArray();

        // Booking map: key => { status, client_name, start, end, id }
        $bookingMapJson = [];
        foreach ($bookingMap as $key => $b) {
            $bookingMapJson[$key] = [
                'status' => $b->status,
                'client_name' => $b->client_name ?? 'Client',
                'start' => $b->start_date->format('d/m'),
                'end' => $b->end_date->format('d/m'),
                'id' => $b->id,
            ];
        }

        // Block map: key => { type, reason }
        $blockMapJson = [];
        foreach ($blockMap as $key => $b) {
            $blockMapJson[$key] = [
                'type' => $b->type,
                'reason' => $b->reason ?? '',
            ];
        }

        // Unavailable map: key => true
        $unavailableMapJson = [];
        foreach ($unavailableMap as $key => $val) {
            $unavailableMapJson[$key] = true;
        }

        // First day of month → weekday offset (Monday=0 .. Sunday=6)
        $firstDay = $days[0] ?? now()->startOfMonth();
        $startDow = ($firstDay->dayOfWeekIso - 1); // 0=Mon, 6=Sun
    @endphp

    <style>
        /* ── Base ── */
        .cal-page { background: #F8FAFF; max-width: 900px; margin: 0 auto; }

        /* ── Vehicle selector ── */
        .cal-vehicle-select {
            appearance: none; background: white; border: 2px solid #E2E8F0;
            border-radius: 16px; padding: 14px 48px 14px 56px; font-size: 15px;
            font-weight: 600; color: #1E293B; width: 100%; cursor: pointer;
            transition: all 0.2s; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 16px center;
        }
        .cal-vehicle-select:focus { border-color: #FF6B2C; box-shadow: 0 0 0 3px rgba(255,107,44,0.15); outline: none; }
        .cal-vehicle-select:hover { border-color: #CBD5E1; }

        /* ── Month nav ── */
        .cal-nav-btn {
            width: 40px; height: 40px; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; border: 1px solid #E2E8F0;
            background: #F8FAFC; color: #64748B; cursor: pointer; transition: all 0.2s;
        }
        .cal-nav-btn:hover { background: #FFF3ED; border-color: #FF6B2C; color: #FF6B2C; }

        /* ── Grid ── */
        .cal-grid {
            display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px;
        }
        .cal-day-header-cell {
            text-align: center; padding: 8px 0; font-size: 12px;
            font-weight: 700; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.05em;
        }
        .cal-day {
            position: relative; min-height: 80px; border: 1px solid #E2E8F0;
            border-radius: 12px; padding: 8px; background: white; transition: all 0.2s;
            cursor: default; overflow: hidden;
        }
        .cal-day-num {
            font-size: 14px; font-weight: 500; color: #1E293B; line-height: 1;
        }
        .cal-day-icon { position: absolute; bottom: 6px; right: 8px; font-size: 12px; opacity: 0.7; }

        /* ── States ── */
        .cal-day--empty { background: transparent; border-color: transparent; }
        .cal-day--past { background: #F8FAFF; }
        .cal-day--past .cal-day-num { color: #CBD5E1; }
        .cal-day--available { cursor: pointer; }
        .cal-day--available:hover { background: #FFF3ED; border-color: #FF6B2C; box-shadow: 0 2px 8px rgba(255,107,44,0.1); }
        .cal-day--blocked { background: #FEE2E2; cursor: pointer; }
        .cal-day--blocked .cal-day-num { color: #EF4444; }
        .cal-day--blocked:hover { background: #FECACA; }
        .cal-day--booked { background: #DCFCE7; }
        .cal-day--booked .cal-day-num { color: #16A34A; }
        .cal-day--maintenance { background: #FEF3C7; }
        .cal-day--maintenance .cal-day-num { color: #D97706; }
        .cal-day--unavailable { background: #F1F5F9; }
        .cal-day--unavailable .cal-day-num { color: #CBD5E1; }
        .cal-day--today .cal-day-num {
            background: #FF6B2C; color: white !important; width: 28px; height: 28px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
        }
        .cal-day--today { background: #FFF7ED; }
        .cal-day--in-range { background: #FFF3ED; border: 2px dashed #FF6B2C; }
        .cal-day--range-start { background: #FF6B2C !important; }
        .cal-day--range-start .cal-day-num { color: white !important; }
        .cal-day--selecting { cursor: crosshair !important; }
        .cal-day--no-vehicle { background: #F8FAFF; cursor: not-allowed; }
        .cal-day--no-vehicle .cal-day-num { color: #CBD5E1; }

        /* ── Legend ── */
        .cal-legend-dot { width: 14px; height: 14px; border-radius: 4px; flex-shrink: 0; }

        /* ── Stats ── */
        .cal-stat {
            background: white; border-radius: 16px; padding: 20px; flex: 1;
            border: 1px solid #E2E8F0; text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .cal-stat-num { font-size: 28px; font-weight: 800; line-height: 1; }
        .cal-stat-label { font-size: 13px; color: #64748B; margin-top: 4px; font-weight: 500; }

        /* ── Panel ── */
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

        /* ── Slide animation ── */
        .cal-slide-enter { animation: calSlideIn 0.25s ease-out; }
        @keyframes calSlideIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* ── Dark mode ── */
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
        .dark .cal-day--blocked { background: #7F1D1D30; }
        .dark .cal-day--blocked .cal-day-num { color: #FCA5A5; }
        .dark .cal-day--booked { background: #14532D30; }
        .dark .cal-day--booked .cal-day-num { color: #86EFAC; }
        .dark .cal-day--today { background: #FF6B2C15; }
        .dark .cal-day--in-range { background: #FF6B2C20; border-color: #FF6B2C; }
        .dark .cal-stat { background: #1F2937; border-color: #374151; }
        .dark .cal-stat-label { color: #9CA3AF; }
        .dark .cal-panel { background: #1F2937; }
        .dark .cal-reason-pill { background: #111827; border-color: #374151; color: #D1D5DB; }

        /* ── Responsive ── */
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
        x-data="calendarLoueur({
            initialVehicle: {{ $vehicles->first()?->id ?? 'null' }},
            vehicles: {{ Js::from($vehiclesArray) }},
            bookingMap: {{ Js::from($bookingMapJson) }},
            blockMap: {{ Js::from($blockMapJson) }},
            unavailableMap: {{ Js::from($unavailableMapJson) }},
            today: '{{ $today }}',
            days: {{ Js::from($daysArray) }},
        })"
        x-init="init()"
        @keyup.escape.window="if(selecting) cancelRange(); if(showPanel) cancelBlock();"
    >

        {{-- ═══ Vehicle selector ═══ --}}
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

        {{-- ═══ Month navigation ═══ --}}
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

        {{-- ═══ Selection hint ═══ --}}
        <div x-show="selecting" x-transition class="flex items-center gap-3 px-5 py-3 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700">
            <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zM12 2.25V4.5m5.834.166l-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243l-1.59-1.59"/></svg>
            <p class="text-sm text-orange-700 dark:text-orange-300 font-medium">
                Cliquez sur la date de fin pour bloquer la plage. <button @click="cancelRange()" class="underline ml-1">Annuler</button>
            </p>
        </div>

        {{-- ═══ Calendar grid ═══ --}}
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

                {{-- Day cells --}}
                @foreach($days as $day)
                    @php
                        $dateStr = $day->format('Y-m-d');
                        $key = ($vehicles->first()?->id ?? 0) . '_' . $dateStr;
                        $dayNum = $day->format('j');
                    @endphp

                    <div
                        class="cal-day"
                        :class="{
                            'cal-day--past': getDayStatus('{{ $dateStr }}') === 'past',
                            'cal-day--available': getDayStatus('{{ $dateStr }}') === 'available',
                            'cal-day--blocked': getDayStatus('{{ $dateStr }}') === 'blocked',
                            'cal-day--booked': getDayStatus('{{ $dateStr }}') === 'booked',
                            'cal-day--maintenance': getDayStatus('{{ $dateStr }}') === 'maintenance',
                            'cal-day--unavailable': getDayStatus('{{ $dateStr }}') === 'unavailable',
                            'cal-day--no-vehicle': getDayStatus('{{ $dateStr }}') === 'no-vehicle',
                            'cal-day--today': isToday('{{ $dateStr }}'),
                            'cal-day--in-range': isInRange('{{ $dateStr }}') && getDayStatus('{{ $dateStr }}') === 'available',
                            'cal-day--range-start': rangeStart === '{{ $dateStr }}' && selecting,
                            'cal-day--selecting': selecting && getDayStatus('{{ $dateStr }}') === 'available',
                        }"
                        @click="onDayClick('{{ $dateStr }}')"
                        @mouseenter="onDayHover('{{ $dateStr }}')"
                        :title="
                            getDayStatus('{{ $dateStr }}') === 'booked'
                                ? 'Réservé — ' + (getBookingInfo('{{ $dateStr }}')?.client_name || '')
                                : (getDayStatus('{{ $dateStr }}') === 'blocked' ? 'Bloqué — Cliquez pour débloquer' : '')
                        "
                        wire:key="day-{{ $dateStr }}"
                    >
                        <div class="cal-day-num">{{ $dayNum }}</div>

                        {{-- Status icons --}}
                        <template x-if="getDayStatus('{{ $dateStr }}') === 'blocked'">
                            <div class="cal-day-icon">🔒</div>
                        </template>
                        <template x-if="getDayStatus('{{ $dateStr }}') === 'booked'">
                            <div class="cal-day-icon">✓</div>
                        </template>
                        <template x-if="getDayStatus('{{ $dateStr }}') === 'maintenance'">
                            <div class="cal-day-icon">🔧</div>
                        </template>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ═══ Legend ═══ --}}
        <div class="flex flex-wrap items-center gap-5 text-sm px-1">
            <div class="flex items-center gap-2">
                <div class="cal-legend-dot" style="background: white; border: 1px solid #E2E8F0;"></div>
                <span class="text-gray-600 dark:text-gray-400">Disponible</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="cal-legend-dot" style="background: #FEE2E2;"></div>
                <span class="text-gray-600 dark:text-gray-400">Bloqué par vous</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="cal-legend-dot" style="background: #DCFCE7;"></div>
                <span class="text-gray-600 dark:text-gray-400">Réservé</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="cal-legend-dot" style="background: #FF6B2C; border-radius: 50%;"></div>
                <span class="text-gray-600 dark:text-gray-400">Aujourd'hui</span>
            </div>
        </div>

        {{-- ═══ Monthly stats ═══ --}}
        <div class="flex gap-4" x-show="selectedVehicleId">
            <div class="cal-stat">
                <div class="cal-stat-num text-emerald-500" x-text="stats.available"></div>
                <div class="cal-stat-label">Jours disponibles</div>
            </div>
            <div class="cal-stat">
                <div class="cal-stat-num text-red-500" x-text="stats.blocked"></div>
                <div class="cal-stat-label">Jours bloqués</div>
            </div>
            <div class="cal-stat">
                <div class="cal-stat-num text-orange-500" x-text="stats.booked"></div>
                <div class="cal-stat-label">Jours réservés</div>
            </div>
        </div>

        {{-- ═══ Toolbar (import + iCal) ═══ --}}
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

        {{-- ═══ Block panel (slide-in) ═══ --}}
        <template x-teleport="body">
            {{-- Backdrop --}}
            <div x-show="showPanel" x-transition.opacity class="cal-panel-backdrop" @click="cancelBlock()" style="display: none;"></div>
            {{-- Panel --}}
            <div class="cal-panel" :class="{ 'cal-panel--open': showPanel }" style="display: block;">
                <div class="p-6 space-y-6">
                    {{-- Header --}}
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Bloquer des dates</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="'Du ' + formatDate(panelDates.start) + ' au ' + formatDate(panelDates.end)"></p>
                        </div>
                        <button @click="cancelBlock()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Vehicle --}}
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl" x-show="selectedVehicle">
                        <template x-if="selectedVehicle?.image">
                            <img :src="selectedVehicle.image" class="w-10 h-10 rounded-lg object-cover" />
                        </template>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedVehicle?.name"></div>
                            <div class="text-xs text-gray-500" x-text="selectedVehicle?.price + ' DA/jour'"></div>
                        </div>
                    </div>

                    {{-- Reason pills --}}
                    <div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Raison du blocage <span class="font-normal text-gray-400">(optionnel)</span></p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="opt in reasonOptions" :key="opt.key">
                                <button
                                    class="cal-reason-pill"
                                    :class="{ 'cal-reason-pill--active': blockReason === opt.key }"
                                    @click="blockReason = blockReason === opt.key ? '' : opt.key"
                                >
                                    <span x-text="opt.icon"></span>
                                    <span x-text="opt.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Note --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Note interne <span class="font-normal text-gray-400">(optionnel)</span></label>
                        <input x-model="blockNote" type="text" placeholder="Ex: Révision chez le garagiste" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-sm px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" />
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        <button @click="cancelBlock()" class="flex-1 px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Annuler
                        </button>
                        <button @click="confirmBlock()" class="flex-1 px-4 py-3 text-sm font-semibold text-white rounded-xl transition shadow-sm" style="background: linear-gradient(135deg, #EF4444, #FF6B2C);">
                            Bloquer ces dates
                        </button>
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
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Mise à jour du calendrier...</span>
        </div>
    </div>

    <script>
        // Register Alpine component — works whether Alpine is already init or not
        (function() {
            function register() {
                if (typeof Alpine === 'undefined') return setTimeout(register, 50);
                if (Alpine.Components && Alpine.Components.has('calendarLoueur')) return;
                Alpine.data('calendarLoueur', (config) => ({
                    selectedVehicleId: config.initialVehicle || null,
                    vehicles: config.vehicles || [],
                    bookingMap: config.bookingMap || {},
                    blockMap: config.blockMap || {},
                    unavailableMap: config.unavailableMap || {},
                    today: config.today || '',
                    days: config.days || [],
                    rangeStart: null, rangeEnd: null, hoverDate: null, selecting: false,
                    showPanel: false,
                    panelDates: { start: null, end: null },
                    blockReason: '', blockNote: '',
                    reasonOptions: [
                        { key: 'entretien', label: 'Entretien', icon: '🔧' },
                        { key: 'personnel', label: 'Usage personnel', icon: '🚗' },
                        { key: 'hors_plateforme', label: 'Réservé hors plateforme', icon: '📦' },
                        { key: 'autre', label: 'Autre', icon: '✏️' },
                    ],
                    init() {
                        if (this.vehicles.length === 1 && !this.selectedVehicleId) {
                            this.selectedVehicleId = this.vehicles[0].id;
                        }
                    },
                    get selectedVehicle() {
                        return this.vehicles.find(v => v.id == this.selectedVehicleId) || null;
                    },
                    get stats() {
                        if (!this.selectedVehicleId) return { available: 0, blocked: 0, booked: 0 };
                        let available = 0, blocked = 0, booked = 0;
                        this.days.forEach(d => {
                            const key = this.selectedVehicleId + '_' + d;
                            if (this.bookingMap[key]) booked++;
                            else if (this.blockMap[key]) blocked++;
                            else if (!this.unavailableMap[key] && !this.isPast(d)) available++;
                        });
                        return { available, blocked, booked };
                    },
                    isPast(dateStr) { return dateStr < this.today; },
                    isToday(dateStr) { return dateStr === this.today; },
                    getDayStatus(dateStr) {
                        if (!this.selectedVehicleId) return 'no-vehicle';
                        const key = this.selectedVehicleId + '_' + dateStr;
                        if (this.bookingMap[key]) return 'booked';
                        if (this.blockMap[key] && this.blockMap[key].type === 'maintenance') return 'maintenance';
                        if (this.blockMap[key]) return 'blocked';
                        if (this.unavailableMap[key]) return 'unavailable';
                        if (this.isPast(dateStr) && !this.isToday(dateStr)) return 'past';
                        return 'available';
                    },
                    getBookingInfo(dateStr) {
                        if (!this.selectedVehicleId) return null;
                        return this.bookingMap[this.selectedVehicleId + '_' + dateStr] || null;
                    },
                    isInRange(dateStr) {
                        if (!this.selecting || !this.rangeStart) return false;
                        const end = this.hoverDate || this.rangeEnd;
                        if (!end) return false;
                        const s = this.rangeStart < end ? this.rangeStart : end;
                        const e = this.rangeStart < end ? end : this.rangeStart;
                        return dateStr >= s && dateStr <= e;
                    },
                    onDayClick(dateStr) {
                        if (!this.selectedVehicleId) return;
                        const status = this.getDayStatus(dateStr);
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
                    onDayHover(dateStr) { if (this.selecting) this.hoverDate = dateStr; },
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
                }));
            }
            register();
        })();
    </script>
</x-filament-panels::page>

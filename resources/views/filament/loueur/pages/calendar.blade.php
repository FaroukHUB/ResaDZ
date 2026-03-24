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

        /* Legend */
        .cal-legend-dot { width: 14px; height: 14px; border-radius: 4px; flex-shrink: 0; }

        /* Stats */
        .cal-stat {
            background: white; border-radius: 16px; padding: 20px; flex: 1;
            border: 1px solid #E2E8F0; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .cal-stat-num { font-size: 28px; font-weight: 800; line-height: 1; }
        .cal-stat-label { font-size: 13px; color: #64748B; margin-top: 4px; font-weight: 500; }

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
        .dark .cal-stat { background: #1F2937; border-color: #374151; }
        .dark .cal-stat-label { color: #9CA3AF; }

        @media (max-width: 640px) {
            .cal-day { min-height: 56px; padding: 4px; border-radius: 8px; }
            .cal-day-num { font-size: 12px; }
            .cal-day-icon { font-size: 10px; bottom: 2px; right: 4px; }
            .cal-stat { padding: 14px; }
            .cal-stat-num { font-size: 22px; }
        }
    </style>

    <div class="cal-page space-y-6"
        x-data="{
            selectedVehicleId: {{ $vehicles->first()?->id ?? 'null' }},
            onDayClick(dateStr, status) {
                if (!this.selectedVehicleId) return;
                if (status === 'past' || status === 'booked' || status === 'maintenance' || status === 'unavailable' || status === 'no-vehicle') return;
                this.$wire.toggleBlock(parseInt(this.selectedVehicleId), dateStr);
            },
        }"
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

        {{-- Hint: clic simple pour bloquer/débloquer --}}
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-400 dark:text-gray-500">Cliquez directement sur un jour pour le bloquer/débloquer</span>
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
                        @click="onDayClick('{{ $dateStr }}', '{{ $status }}')"
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

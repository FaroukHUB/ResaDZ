<x-filament-panels::page>
    <div class="space-y-8" style="background: #F8FAFF; min-height: 100vh; margin: -1.5rem; padding: 1.5rem;">

        {{-- Hero Card --}}
        <div class="ch-hero" style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); border-radius: 1.25rem; padding: 2.25rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -30px; right: -30px; width: 140px; height: 140px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -40px; right: 80px; width: 100px; height: 100px; background: rgba(255,255,255,0.07); border-radius: 50%;"></div>
            <div class="relative z-10 w-full">
                <div class="flex items-center gap-4">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); backdrop-filter: blur(12px); border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 style="font-size: 1.625rem; font-weight: 800; margin: 0; letter-spacing: -0.025em; color: #fff;">Votre réputation ⭐</h2>
                        <p style="color: rgba(255,255,255,0.9); font-size: 0.9375rem; margin-top: 0.375rem; font-weight: 500;">Les clients lisent vos avis avant de réserver</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Note moyenne --}}
            <div class="ch-stat-card" style="background: white; border-radius: 1.25rem; padding: 1.75rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); transition: all 0.2s ease; border: 1px solid rgba(245,158,11,0.15);"
                 onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 32px rgba(99,102,241,0.14)'"
                 onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 24px rgba(99,102,241,0.08)'">
                <div class="flex items-center gap-4">
                    <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #F59E0B, #D97706); border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 0.8125rem; color: #64748B; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Note moyenne</p>
                        <p style="font-size: 2.5rem; font-weight: 800; color: #1E293B; line-height: 1; margin-top: 0.25rem;">{{ $stats['average'] }}<span style="font-size: 1.125rem; color: #64748B; font-weight: 500;">/5</span></p>
                    </div>
                </div>
            </div>

            {{-- Total avis --}}
            <div class="stat-card stat-card-indigo" style="background: white; border-radius: 1.25rem; padding: 1.75rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); transition: all 0.2s ease; border: 1px solid rgba(99,102,241,0.12);"
                 onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 32px rgba(99,102,241,0.14)'"
                 onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 24px rgba(99,102,241,0.08)'">
                <div class="flex items-center gap-4">
                    <div class="icon-container icon-container-lg" style="width: 60px; height: 60px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 0.8125rem; color: #64748B; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total avis</p>
                        <p style="font-size: 2.5rem; font-weight: 800; color: #1E293B; line-height: 1; margin-top: 0.25rem;">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Sans réponse --}}
            <div class="stat-card {{ $stats['without_response'] > 0 ? 'stat-card-red' : 'stat-card-green' }}" style="background: white; border-radius: 1.25rem; padding: 1.75rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); transition: all 0.2s ease; border: 2px solid {{ $stats['without_response'] > 0 ? 'rgba(239,68,68,0.35)' : 'rgba(16,185,129,0.15)' }};"
                 onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 32px rgba(99,102,241,0.14)'"
                 onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 24px rgba(99,102,241,0.08)'">
                <div class="flex items-center gap-4">
                    <div class="icon-container icon-container-lg" style="width: 60px; height: 60px; background: linear-gradient(135deg, {{ $stats['without_response'] > 0 ? '#EF4444, #DC2626' : '#10B981, #059669' }}); border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($stats['without_response'] > 0)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @endif
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 0.8125rem; color: #64748B; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Sans réponse</p>
                        <p style="font-size: 2.5rem; font-weight: 800; color: {{ $stats['without_response'] > 0 ? '#EF4444' : '#10B981' }}; line-height: 1; margin-top: 0.25rem;">{{ $stats['without_response'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Star Distribution --}}
        <div class="card-modern" style="background: white; border-radius: 1.25rem; padding: 2rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08);">
            <div class="flex items-center gap-3 mb-5">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1E293B;">Distribution des notes</h3>
            </div>
            <div class="space-y-4" x-data="{ shown: false }" x-init="setTimeout(() => shown = true, 200)">
                @foreach($stats['distribution'] as $rating => $data)
                    @php
                        $barColors = [
                            5 => '#6366F1',
                            4 => '#F59E0B',
                            3 => '#FBBF24',
                            2 => '#FB923C',
                            1 => '#EF4444',
                        ];
                        $barColor = $barColors[$rating] ?? '#6366F1';
                    @endphp
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2" style="min-width: 70px;">
                            <span style="font-size: 0.9375rem; font-weight: 700; color: #1E293B; min-width: 14px; text-align: right;">{{ $rating }}</span>
                            <svg class="w-5 h-5" style="color: {{ $barColor }};" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div class="flex-1 progress-modern" style="height: 12px; background: #F1F5F9; border-radius: 999px; overflow: hidden;">
                            <div class="progress-modern-bar rating-bar-{{ $rating }}"
                                 x-show="shown"
                                 x-transition:enter="transition-all ease-out duration-700"
                                 style="height: 100%; border-radius: 999px; background: {{ $barColor }}; width: {{ $data['percentage'] }}%; transition: width 0.8s ease-out;"></div>
                        </div>
                        <span style="font-size: 0.9375rem; font-weight: 700; color: #64748B; min-width: 36px; text-align: right;">{{ $data['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Reviews List --}}
        <div>
            <div class="flex items-center gap-3 mb-5">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366F1, #8B5CF6); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1E293B;">Tous les avis</h3>
            </div>

            @if($reviews->isEmpty())
                <div class="empty-state card-modern" style="background: white; border-radius: 1.25rem; padding: 4rem 2rem; text-align: center; box-shadow: 0 4px 24px rgba(99,102,241,0.08);">
                    <div class="empty-state-icon" style="width: 80px; height: 80px; background: linear-gradient(135deg, #6366F1, #818CF8); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <p style="color: #1E293B; font-weight: 700; font-size: 1.125rem;">Aucun avis pour le moment</p>
                    <p style="color: #64748B; font-size: 0.9375rem; margin-top: 0.5rem;">Les avis de vos clients apparaîtront ici</p>
                </div>
            @else
                <div class="space-y-5">
                    @foreach($reviews as $review)
                        <div class="card-modern animate-slide-up" style="background: white; border-radius: 1.25rem; padding: 1.75rem; box-shadow: 0 4px 24px rgba(99,102,241,0.08); transition: all 0.2s ease; border: 1px solid rgba(226,232,240,0.6);"
                             onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 32px rgba(99,102,241,0.14)'"
                             onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 24px rgba(99,102,241,0.08)'">

                            {{-- Header --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #6366F1, #818CF8); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="color: white; font-weight: 700; font-size: 1.125rem;">
                                            {{ $review->reviewer ? strtoupper(substr($review->reviewer->name, 0, 1)) : '?' }}
                                        </span>
                                    </div>
                                    <div>
                                        <p style="font-weight: 700; color: #1E293B; font-size: 0.9375rem;">
                                            {{ $review->reviewer?->name ?? 'Client' }}
                                        </p>
                                        <div class="flex items-center gap-2" style="font-size: 0.8125rem; color: #64748B; margin-top: 0.125rem;">
                                            <svg class="w-3.5 h-3.5" style="color: #94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>{{ $review->created_at->format('d/m/Y') }}</span>
                                            @if($review->booking && $review->booking->vehicle)
                                                <span style="color: #CBD5E1;">|</span>
                                                <span style="color: #6366F1; font-weight: 500;">{{ $review->booking->vehicle->full_name ?? '' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1" style="background: linear-gradient(135deg, #FFF7ED, #FFFBEB); padding: 0.5rem 0.875rem; border-radius: 999px; border: 1px solid rgba(255,107,44,0.15);">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4" style="color: {{ $i <= $review->rating_overall ? '#6366F1' : '#E2E8F0' }};" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    <span style="font-weight: 700; color: #6366F1; font-size: 0.8125rem; margin-left: 0.375rem;">{{ $review->rating_overall }}/5</span>
                                </div>
                            </div>

                            {{-- Comment --}}
                            @if($review->comment)
                                <div style="background: #F8FAFF; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1rem;">
                                    <p style="color: #334155; line-height: 1.75; font-size: 0.9375rem;">{{ $review->comment }}</p>
                                </div>
                            @endif

                            {{-- Detail ratings --}}
                            @if($review->rating_vehicle || $review->rating_communication || $review->rating_punctuality || $review->rating_cleanliness)
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach([
                                        'rating_vehicle' => 'Véhicule',
                                        'rating_communication' => 'Communication',
                                        'rating_punctuality' => 'Ponctualité',
                                        'rating_cleanliness' => 'Propreté',
                                    ] as $field => $label)
                                        @if($review->$field)
                                            <span class="badge-modern badge-info" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; color: #64748B; background: #F1F5F9; padding: 0.4375rem 0.875rem; border-radius: 999px; border: 1px solid #E2E8F0;">
                                                {{ $label }}
                                                <span style="font-weight: 700; color: #6366F1;">{{ $review->$field }}/5</span>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            {{-- Existing Response --}}
                            @if($review->response)
                                <div style="background: linear-gradient(135deg, #EEF2FF, #E0E7FF); border-radius: 14px; padding: 1.25rem; border-left: 4px solid #6366F1; margin-top: 0.5rem;">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div style="width: 28px; height: 28px; background: linear-gradient(135deg, #6366F1, #818CF8); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                        </div>
                                        <span style="font-size: 0.8125rem; font-weight: 700; color: #6366F1;">Votre réponse</span>
                                        <span style="font-size: 0.75rem; color: #94A3B8; margin-left: auto;">{{ $review->responded_at?->format('d/m/Y') }}</span>
                                    </div>
                                    <p style="font-size: 0.9375rem; color: #334155; line-height: 1.7;">{{ $review->response }}</p>
                                </div>
                            @elseif($respondingToId === $review->id)
                                {{-- Response Form --}}
                                <div style="background: #F8FAFF; border-radius: 14px; padding: 1.5rem; margin-top: 1rem; border: 1.5px solid #E2E8F0;">
                                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 700; color: #1E293B; margin-bottom: 0.75rem;">
                                        <svg class="w-4 h-4" style="color: #6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                        Votre réponse
                                    </label>
                                    <textarea
                                        wire:model="responseText"
                                        rows="3"
                                        style="width: 100%; padding: 0.875rem 1.125rem; background: white; border: 1.5px solid #E2E8F0; border-radius: 14px; color: #1E293B; font-size: 0.9375rem; resize: vertical; outline: none; transition: all 0.2s ease; line-height: 1.6;"
                                        onfocus="this.style.borderColor='#6366F1'; this.style.boxShadow='0 0 0 3px rgba(255,107,44,0.1)'"
                                        onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'"
                                        placeholder="Remerciez le client ou apportez des précisions..."
                                    ></textarea>
                                    <div class="flex items-center justify-end gap-3 mt-4">
                                        <button
                                            type="button"
                                            wire:click="cancelResponse"
                                            style="padding: 0.625rem 1.25rem; font-size: 0.875rem; color: #64748B; font-weight: 600; background: white; border: 1.5px solid #E2E8F0; border-radius: 12px; cursor: pointer; transition: all 0.2s ease;"
                                            onmouseenter="this.style.borderColor='#CBD5E1'; this.style.color='#1E293B'"
                                            onmouseleave="this.style.borderColor='#E2E8F0'; this.style.color='#64748B'"
                                        >
                                            Annuler
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="submitResponse"
                                            class="btn-modern btn-gradient-primary"
                                            style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); padding: 0.625rem 1.5rem; font-size: 0.875rem; font-weight: 700; color: white; border: none; border-radius: 12px; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.5rem;"
                                            onmouseenter="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 16px rgba(255,107,44,0.35)'"
                                            onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                            Publier la réponse
                                        </button>
                                    </div>
                                </div>
                            @else
                                {{-- Response Button --}}
                                <button
                                    type="button"
                                    wire:click="startResponding({{ $review->id }})"
                                    class="btn-modern btn-outline-orange"
                                    style="margin-top: 0.75rem; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #6366F1; font-weight: 600; background: none; border: 1.5px solid #6366F1; padding: 0.625rem 1.25rem; border-radius: 12px; cursor: pointer; transition: all 0.2s ease;"
                                    onmouseenter="this.style.background='#6366F1'; this.style.color='white'; this.style.boxShadow='0 4px 12px rgba(255,107,44,0.25)'"
                                    onmouseleave="this.style.background='none'; this.style.color='#6366F1'; this.style.boxShadow='none'"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    Répondre à cet avis
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <style>
        .boost-hero {
            background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
            border-radius: 1.5rem;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            color: white;
            box-shadow: 0 8px 32px rgba(99,102,241,0.18);
        }
        .boost-hero::before {
            content: '';
            position: absolute;
            top: -60%;
            right: -15%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }
        .boost-hero::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: -8%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        @keyframes rocketBounce {
            0%, 100% { transform: translateY(0) rotate(-10deg); }
            50% { transform: translateY(-10px) rotate(-10deg); }
        }
        .animate-rocket {
            animation: rocketBounce 2s ease-in-out infinite;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-up {
            animation: slideUp 0.5s ease-out forwards;
        }
        .card-modern {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(99,102,241,0.08);
            border: 1px solid rgba(99,102,241,0.08);
            transition: all 0.2s ease;
        }
        .card-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(99,102,241,0.14);
        }
        .dark .card-modern {
            background: rgb(31 41 55);
            border-color: rgb(55 65 81);
        }
        .boost-active-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.75rem;
            border: 1px solid rgba(99,102,241,0.10);
            box-shadow: 0 4px 24px rgba(99,102,241,0.08);
            transition: all 0.2s ease;
        }
        .boost-active-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(99,102,241,0.16);
        }
        .dark .boost-active-card {
            background: rgb(31 41 55);
            border-color: rgb(55 65 81);
        }
        .boost-package-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            border: 1px solid rgba(99,102,241,0.10);
            box-shadow: 0 4px 24px rgba(99,102,241,0.08);
            transition: all 0.2s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .boost-package-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(99,102,241,0.18);
        }
        .dark .boost-package-card {
            background: rgb(31 41 55);
            border-color: rgb(55 65 81);
        }
        .boost-package-card.recommended {
            border: 2px solid #FF6B2C;
            box-shadow: 0 8px 32px rgba(255,107,44,0.15);
        }
        .recommended-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: linear-gradient(135deg, #FF6B2C, #F59E0B);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.35rem 1.25rem;
            border-radius: 0 1.5rem 0 1rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .progress-modern {
            height: 8px;
            background: #e5e7eb;
            border-radius: 9999px;
            overflow: hidden;
        }
        .dark .progress-modern {
            background: rgb(55 65 81);
        }
        .progress-modern-bar {
            height: 100%;
            border-radius: 9999px;
            background: linear-gradient(90deg, #FF6B2C, #F59E0B);
            transition: width 0.6s ease;
        }
        .guide-step {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FF6B2C, #F59E0B);
            color: white;
            font-size: 0.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(255,107,44,0.25);
        }
        .btn-boost-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.85rem 2.25rem;
            background: linear-gradient(135deg, #FF6B2C, #F59E0B);
            color: white;
            font-weight: 700;
            border-radius: 0.85rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 1rem;
            box-shadow: 0 4px 16px rgba(255,107,44,0.25);
        }
        .btn-boost-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(255,107,44,0.35);
        }
        .benefit-check {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
        }
        .badge-modern {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
        }
        .badge-success {
            background: rgba(16,185,129,0.12);
            color: #059669;
        }
        .dark .badge-success {
            background: rgba(16,185,129,0.2);
            color: #34d399;
        }
        .badge-warning {
            background: rgba(245,158,11,0.12);
            color: #D97706;
        }
        .dark .badge-warning {
            background: rgba(245,158,11,0.2);
            color: #FBBF24;
        }
    </style>

    {{-- Hero Card --}}
    <div class="boost-hero mb-8 animate-slide-up">
        <div class="relative z-10">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center animate-rocket">
                        <x-heroicon-o-rocket-launch class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h2 class="text-2xl md:text-3xl font-black tracking-tight">Boostez votre visibilite</h2>
                        <p class="text-white/80 text-sm mt-1">Apparaissez en premier et recevez plus de demandes de location</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-5 py-2.5">
                    <x-heroicon-o-sparkles class="w-5 h-5 text-yellow-300" />
                    <span class="text-sm font-semibold text-white/90">+40% de clics en moyenne</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Guide Section --}}
    <div class="mb-8 rounded-2xl p-6 border border-orange-200 dark:border-orange-800/50" style="background: linear-gradient(135deg, rgba(255,107,44,0.06) 0%, rgba(245,158,11,0.06) 100%);">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #FF6B2C, #F59E0B); box-shadow: 0 4px 12px rgba(255,107,44,0.25);">
                <x-heroicon-o-light-bulb class="w-6 h-6 text-white" />
            </div>
            <div class="flex-1">
                <p class="font-bold text-gray-900 dark:text-white text-lg mb-3">Comment ca marche ?</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="guide-step p-3 rounded-xl bg-white/60 dark:bg-gray-800/40">
                        <div class="step-number">1</div>
                        <span class="text-sm text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Choisissez un vehicule</strong> parmi votre flotte active</span>
                    </div>
                    <div class="guide-step p-3 rounded-xl bg-white/60 dark:bg-gray-800/40">
                        <div class="step-number">2</div>
                        <span class="text-sm text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Selectionnez une duree</strong> : 3, 7 ou 30 jours selon votre budget</span>
                    </div>
                    <div class="guide-step p-3 rounded-xl bg-white/60 dark:bg-gray-800/40">
                        <div class="step-number">3</div>
                        <span class="text-sm text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">Effet immediat</strong> : Votre vehicule apparait en tete avec un badge "Sponsorise"</span>
                    </div>
                    <div class="guide-step p-3 rounded-xl bg-white/60 dark:bg-gray-800/40">
                        <div class="step-number">4</div>
                        <span class="text-sm text-gray-700 dark:text-gray-300"><strong class="text-gray-900 dark:text-white">+40% de clics en moyenne</strong> grace a la mise en avant prioritaire</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Boosts --}}
    @if($this->activeBoosts->count() > 0)
        <div class="mb-8 animate-slide-up" style="animation-delay: 0.1s;">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #10B981, #059669); box-shadow: 0 4px 12px rgba(16,185,129,0.25);">
                    <x-heroicon-o-bolt class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Boosts actifs</h3>
                <span class="badge-modern badge-success">{{ $this->activeBoosts->count() }} actif(s)</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($this->activeBoosts as $boost)
                    <div class="boost-active-card">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366F1, #8B5CF6); box-shadow: 0 4px 12px rgba(99,102,241,0.25);">
                                <x-heroicon-o-rocket-launch class="w-5 h-5 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-900 dark:text-white truncate">
                                    {{ $boost->vehicle->brand->name }} {{ $boost->vehicle->model }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $boost->boostPackage->name }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-500 dark:text-gray-400">Temps restant</span>
                            <span class="font-bold" style="color: #FF6B2C;">
                                {{ $boost->remaining_days }} jour(s)
                            </span>
                        </div>
                        <div class="progress-modern">
                            @php
                                $totalDays = $boost->boostPackage->duration_days;
                                $remaining = $boost->remaining_days;
                                $percentage = ($remaining / $totalDays) * 100;
                            @endphp
                            <div class="progress-modern-bar" style="width: {{ $percentage }}%"></div>
                        </div>
                        <div class="mt-3 flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500">
                            <x-heroicon-o-clock class="w-3.5 h-3.5" />
                            <span>Expire dans {{ $boost->remaining_days }} jour(s)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Pending Boosts --}}
    @if($this->pendingBoosts->count() > 0)
        <div class="mb-8 animate-slide-up" style="animation-delay: 0.15s;">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #F59E0B, #D97706); box-shadow: 0 4px 12px rgba(245,158,11,0.25);">
                    <x-heroicon-o-clock class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Boosts en attente de paiement</h3>
            </div>
            <div class="rounded-2xl p-6 border-l-4" style="border-left-color: #F59E0B; background: linear-gradient(135deg, rgba(245,158,11,0.06) 0%, rgba(251,191,36,0.04) 100%); box-shadow: 0 4px 24px rgba(99,102,241,0.08); border-radius: 1.5rem;">
                <div class="space-y-0">
                    @foreach($this->pendingBoosts as $boost)
                        <div class="flex items-center justify-between py-3.5 {{ !$loop->last ? 'border-b border-amber-200/50 dark:border-amber-800/30' : '' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                                    <x-heroicon-o-clock class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ $boost->vehicle->brand->name }} {{ $boost->vehicle->model }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $boost->boostPackage->name }} - {{ $boost->boostPackage->formatted_price }}
                                    </p>
                                </div>
                            </div>
                            <span class="badge-modern badge-warning">
                                En attente
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 flex items-center gap-2 text-sm text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 rounded-xl p-3.5">
                    <x-heroicon-o-information-circle class="w-5 h-5 flex-shrink-0" />
                    <span>Contactez-nous pour effectuer le paiement et activer vos boosts.</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Boost Form --}}
    <div class="card-modern p-6 mb-8 animate-slide-up" style="animation-delay: 0.2s;">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #FF6B2C, #F59E0B); box-shadow: 0 4px 12px rgba(255,107,44,0.25);">
                <x-heroicon-o-rocket-launch class="w-6 h-6 text-white" />
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white">Nouveau boost</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Mettez votre vehicule en avant pour plus de visibilite
                </p>
            </div>
        </div>

        {{-- Benefits --}}
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-start gap-3 p-4 rounded-xl" style="background: linear-gradient(135deg, rgba(16,185,129,0.08), rgba(16,185,129,0.03));">
                <div class="benefit-check" style="background: #10B981; box-shadow: 0 2px 8px rgba(16,185,129,0.3);">
                    <x-heroicon-o-arrow-trending-up class="w-3 h-3 text-white" />
                </div>
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">Plus de visibilite</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Apparaissez en premier dans les recherches</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-xl" style="background: linear-gradient(135deg, rgba(255,107,44,0.08), rgba(255,107,44,0.03));">
                <div class="benefit-check" style="background: #FF6B2C; box-shadow: 0 2px 8px rgba(255,107,44,0.3);">
                    <x-heroicon-o-star class="w-3 h-3 text-white" />
                </div>
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">Badge "Sponsorise"</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Attirez l'attention des visiteurs</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-xl" style="background: linear-gradient(135deg, rgba(99,102,241,0.08), rgba(99,102,241,0.03));">
                <div class="benefit-check" style="background: #6366F1; box-shadow: 0 2px 8px rgba(99,102,241,0.3);">
                    <x-heroicon-o-cursor-arrow-rays class="w-3 h-3 text-white" />
                </div>
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white text-sm">Plus de clics</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Augmentez vos chances de location</p>
                </div>
            </div>
        </div>

        <form wire:submit="submit">
            {{ $this->form }}

            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" size="lg">
                    <x-heroicon-o-rocket-launch class="w-5 h-5 mr-2" />
                    Acheter le boost
                </x-filament::button>
            </div>
        </form>
    </div>

    {{-- Available Packages --}}
    @if($this->availablePackages->count() > 0)
        <div class="mt-2 animate-slide-up" style="animation-delay: 0.25s;">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366F1, #8B5CF6); box-shadow: 0 4px 12px rgba(99,102,241,0.25);">
                    <x-heroicon-o-cube-transparent class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nos packs boost</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($this->availablePackages as $package)
                    <div class="boost-package-card {{ $loop->iteration === 2 ? 'recommended' : '' }}">
                        @if($loop->iteration === 2)
                            <div class="recommended-badge">Recommande</div>
                        @endif
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, {{ $loop->iteration === 1 ? '#6366F1, #8B5CF6' : ($loop->iteration === 2 ? '#FF6B2C, #F59E0B' : '#10B981, #059669') }}); box-shadow: 0 4px 16px {{ $loop->iteration === 1 ? 'rgba(99,102,241,0.3)' : ($loop->iteration === 2 ? 'rgba(255,107,44,0.3)' : 'rgba(16,185,129,0.3)') }};">
                            @if($loop->iteration === 1)
                                <x-heroicon-o-bolt class="w-8 h-8 text-white" />
                            @elseif($loop->iteration === 2)
                                <x-heroicon-o-fire class="w-8 h-8 text-white" />
                            @else
                                <x-heroicon-o-trophy class="w-8 h-8 text-white" />
                            @endif
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">{{ $package->name }}</h4>
                        <p class="text-3xl font-black mt-2 mb-1" style="color: #FF6B2C;">
                            {{ $package->formatted_price }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                            {{ $package->description ?? 'Boost de ' . $package->duration_days . ' jours' }}
                        </p>
                        <div class="space-y-2.5 text-left mb-6">
                            <div class="flex items-center gap-2.5 text-sm text-gray-700 dark:text-gray-300">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background: #10B981; box-shadow: 0 2px 6px rgba(16,185,129,0.3);">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span>{{ $package->duration_days }} jours de mise en avant</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-sm text-gray-700 dark:text-gray-300">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background: #6366F1; box-shadow: 0 2px 6px rgba(99,102,241,0.3);">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span>Badge "Sponsorise" visible</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-sm text-gray-700 dark:text-gray-300">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background: #FF6B2C; box-shadow: 0 2px 6px rgba(255,107,44,0.3);">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span>Position prioritaire en recherche</span>
                            </div>
                        </div>
                        <button class="btn-boost-cta w-full">
                            <x-heroicon-o-rocket-launch class="w-5 h-5" />
                            Choisir ce pack
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-filament-panels::page>

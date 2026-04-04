@php
    $loueur = $vehicle->loueur;
    $badges = $vehicle->getBadges();
    $degressivePricing = $vehicle->degressive_pricing;
    $showSelectionBorder = $showSelectionBorder ?? false;
    $hasGoldenBorder = $vehicle->is_boosted || $showSelectionBorder;
@endphp

<article class="rounded-lg overflow-hidden flex flex-col {{ $hasGoldenBorder ? 'border-2 border-amber-500' : 'border border-neutral-800' }} bg-black">

    {{-- Header: Brand + Model --}}
    <div class="px-4 py-3 flex items-center gap-3 border-b border-neutral-800">
        @if($vehicle->brand && $vehicle->brand->logo)
            <img src="{{ asset('storage/' . $vehicle->brand->logo) }}" alt="{{ $vehicle->brand->name }}" class="w-8 h-8 object-contain bg-white rounded-full p-1" loading="lazy">
        @else
            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                <span class="text-black text-xs font-black">{{ strtoupper(substr($vehicle->brand->name ?? 'V', 0, 2)) }}</span>
            </div>
        @endif
        <div class="flex items-baseline gap-2 min-w-0">
            <span class="text-white/60 text-sm font-medium">{{ $vehicle->brand->name ?? '' }}</span>
            <span class="text-white font-bold truncate">{{ $vehicle->model ?? $vehicle->full_name }}</span>
            @if($vehicle->year)
                <span class="text-white font-bold">{{ $vehicle->year }}</span>
            @endif
        </div>
    </div>

    {{-- Image Section --}}
    <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="block relative border-b border-neutral-800">
        <div class="aspect-[16/10] overflow-hidden">
            @if($vehicle->display_image)
                <img src="{{ asset('storage/' . $vehicle->display_image) }}"
                     alt="{{ $vehicle->full_name }}"
                     class="w-full h-full object-cover"
                     loading="lazy"
                     decoding="async"
                     width="400"
                     height="250"
                     sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px">
            @else
                <div class="w-full h-full flex items-center justify-center bg-neutral-900">
                    <svg class="w-16 h-16 text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                </div>
            @endif
        </div>

        {{-- Sponsored Badge --}}
        @if($vehicle->is_boosted)
            <div class="absolute top-3 left-3 bg-gradient-to-r from-amber-500 to-orange-500 px-3 py-1 rounded">
                <span class="text-[10px] font-black text-white uppercase tracking-wider">Sponsorisé</span>
            </div>
        @endif

        {{-- Special Offer Badge --}}
        @if($vehicle->activeOffer)
            <div class="absolute top-3 right-3 bg-gradient-to-r from-red-500 to-pink-500 px-3 py-1 rounded animate-pulse">
                <span class="text-[10px] font-black text-white uppercase tracking-wider">{{ $vehicle->activeOffer->badge_text }}</span>
            </div>
        @endif

        {{-- En location Badge --}}
        @if($vehicle->status === 'reserved')
            <div class="absolute top-3 left-3 bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-1 rounded z-10">
                <span class="text-[10px] font-black text-white uppercase tracking-wider">En location</span>
            </div>
        @endif

        {{-- Haute saison Badge --}}
        @php
            $activeSeasonCard = $vehicle->relationLoaded('seasonalRates')
                ? $vehicle->seasonalRates->filter(fn ($r) => $r->is_active && $r->start_date->lte(now()) && $r->end_date->gte(now()))->first()
                : null;
        @endphp
        @if($activeSeasonCard)
            <div class="absolute bottom-3 left-3 bg-amber-500/90 backdrop-blur-sm px-2.5 py-1 rounded">
                <span class="text-[10px] font-black text-white uppercase tracking-wider">🌞 Haute saison</span>
            </div>
        @endif

        {{-- Loueur name overlay --}}
        @if($loueur && $loueur->company_name)
            <div class="absolute bottom-3 right-3" style="perspective: 200px;">
                <span class="block bg-black/60 backdrop-blur-sm px-3 py-1 rounded text-white text-xs font-bold tracking-wide uppercase"
                      style="transform: rotateY(-8deg) rotateX(3deg); text-shadow: 0 1px 4px rgba(0,0,0,0.5);">
                    {{ $loueur->company_name }}
                </span>
            </div>
        @endif
    </a>

    {{-- Price Section --}}
    <div class="px-4 py-4 border-b border-neutral-800">
        <div class="flex items-baseline gap-2">
            <span class="text-2xl font-black text-white">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }} DA</span>
            @if($vehicle->price_per_day_eur)
                <span class="text-white/60 text-lg font-semibold">/ {{ number_format($vehicle->price_per_day_eur, 0) }} €</span>
            @endif
        </div>
        <p class="text-white/60 text-sm mt-1">Prix / jour</p>
    </div>

    {{-- National availability badge --}}
    @if($loueur && $loueur->disponible_national)
        <div class="px-4 py-2 border-b border-neutral-800">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-600/20 border border-green-500/30 rounded text-xs font-semibold text-green-400">
                🇩🇿 Disponible partout en Algérie
            </span>
        </div>
    @endif

    {{-- Badges Section (only if loueur has badges configured) --}}
    @if(count($badges) > 0)
        <div class="px-4 py-3 space-y-2 border-b border-neutral-800">
            @foreach($badges as $badge)
                @php
                    $emoji = match($badge['icon'] ?? '') {
                        'check' => '✅',
                        'truck' => '🚚',
                        'plane' => '✈️',
                        'arrow-down' => '📉',
                        'infinity' => '♾️',
                        default => '⭐',
                    };
                @endphp
                <div class="flex items-center gap-2">
                    <span class="bg-white/10 rounded px-2 py-1 text-sm">
                        <span>{{ $emoji }}</span>
                        <span class="text-white ml-1">{{ $badge['text'] ?? $badge }}</span>
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Action Button --}}
    <div class="p-4 mt-auto">
        @if($vehicle->status === 'reserved')
            <a href="{{ route('vehicles.show', $vehicle->slug) }}"
               data-track="view_details"
               data-vehicle-id="{{ $vehicle->id }}"
               data-loueur-id="{{ $loueur?->id }}"
               class="flex items-center justify-center gap-2 h-12 bg-gray-500 text-white text-sm font-bold rounded w-full cursor-default">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Actuellement en location
            </a>
        @else
            <a href="{{ route('vehicles.show', $vehicle->slug) }}"
               data-track="view_details"
               data-vehicle-id="{{ $vehicle->id }}"
               data-loueur-id="{{ $loueur?->id }}"
               class="flex items-center justify-center gap-2 h-12 bg-green-600 text-white text-sm font-bold rounded hover:bg-green-500 transition w-full">
                Réserver
            </a>
        @endif
    </div>
</article>

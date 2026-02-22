@php
    $loueur = $vehicle->loueur;
    $badges = $loueur ? $loueur->getBadges() : [];
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
            @if($vehicle->image)
                <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->full_name }}"
                     class="w-full h-full object-cover" loading="lazy">
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

        {{-- Loueur name overlay --}}
        @if($loueur && $loueur->company_name)
            <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm px-3 py-1 rounded">
                <span class="text-white text-xs">{{ $loueur->company_name }}</span>
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
        <p class="text-white/50 text-sm mt-1">Prix / jour</p>
    </div>

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
        <a href="{{ route('vehicles.show', $vehicle->slug) }}"
           data-track="view_details"
           data-vehicle-id="{{ $vehicle->id }}"
           data-loueur-id="{{ $loueur?->id }}"
           class="flex items-center justify-center gap-2 h-12 bg-emerald-500 text-white text-sm font-bold rounded hover:bg-emerald-400 transition w-full">
            Réserver
        </a>
    </div>
</article>

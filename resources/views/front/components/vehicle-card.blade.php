@php
    $loueur = $vehicle->loueur;
    $badges = $loueur ? $loueur->getBadges() : [];
    $degressivePricing = $vehicle->degressive_pricing;
    $showSelectionBorder = $showSelectionBorder ?? false;
    $showSelectionBadge = $showSelectionBadge ?? false;
    $showNewBadge = $showNewBadge ?? false;
    $hasGoldenBorder = $vehicle->is_boosted || $showSelectionBorder;
    $loueurRating = $loueur ? $loueur->rating : null;
    $loueurReviews = $loueur ? $loueur->total_reviews : 0;
    $loueurWilaya = $loueur ? ($loueur->wilaya ?? null) : null;
    $isNew = $vehicle->created_at && $vehicle->created_at->gt(now()->subDays(7));
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
                <img src="{{ asset('storage/' . $vehicle->image) }}"
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

        {{-- Selection Badge --}}
        @if($showSelectionBadge)
            <div class="absolute top-3 left-3 bg-green-600 px-2.5 py-1 rounded-full z-10">
                <span class="text-[10px] font-bold text-white">⭐ Sélection ResaDZ</span>
            </div>
        @elseif($vehicle->is_boosted)
            {{-- Sponsored Badge --}}
            <div class="absolute top-3 left-3 bg-gradient-to-r from-amber-500 to-orange-500 px-2.5 py-1 rounded-full z-10">
                <span class="text-[10px] font-bold text-white">🚀 Mis en avant</span>
            </div>
        @endif

        {{-- New Badge --}}
        @if($showNewBadge && $isNew)
            <div class="absolute top-3 right-3 bg-blue-600 px-2.5 py-1 rounded-full z-10">
                <span class="text-[10px] font-bold text-white">🆕 Nouveau</span>
            </div>
        @elseif($vehicle->activeOffer)
            {{-- Special Offer Badge --}}
            <div class="absolute top-3 right-3 bg-gradient-to-r from-red-500 to-pink-500 px-3 py-1 rounded animate-pulse z-10">
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
        <p class="text-white/60 text-sm mt-1">Prix / jour</p>
    </div>

    {{-- Wilaya + Rating --}}
    @if($loueurWilaya || ($loueurRating && $loueurReviews > 0))
    <div class="px-4 py-2 border-b border-neutral-800 flex items-center gap-3 flex-wrap">
        @if($loueurWilaya)
            <span class="flex items-center gap-1 text-xs text-white/60">
                <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                {{ $loueurWilaya }}
            </span>
        @endif
        @if($loueurRating && $loueurReviews > 0)
            <span class="flex items-center gap-1 text-xs text-white/60">
                <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                {{ number_format($loueurRating, 1) }}
            </span>
        @endif
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
        <a href="{{ route('vehicles.show', $vehicle->slug) }}"
           data-track="view_details"
           data-vehicle-id="{{ $vehicle->id }}"
           data-loueur-id="{{ $loueur?->id }}"
           class="flex items-center justify-center gap-2 h-12 bg-green-600 text-white text-sm font-bold rounded hover:bg-green-500 transition w-full">
            Réserver
        </a>
    </div>
</article>

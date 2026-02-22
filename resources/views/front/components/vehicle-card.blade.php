@php
    $loueur = $vehicle->loueur;
    $badges = $loueur ? $loueur->getBadges() : [];
    $degressivePricing = $vehicle->degressive_pricing;
    $showSelectionBorder = $showSelectionBorder ?? false;
    $hasGoldenBorder = $vehicle->is_boosted || $showSelectionBorder;
@endphp

<article class="rounded-3xl overflow-hidden flex flex-col group transition-all duration-500 hover:scale-[1.02] {{ $hasGoldenBorder ? 'ring-2 ring-amber-400 ring-offset-2 ring-offset-black' : '' }}" style="background: linear-gradient(145deg, #1a1a1a 0%, #0d0d0d 100%);">

    {{-- Image Section --}}
    <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="block relative">
        <div class="aspect-[16/10] overflow-hidden">
            @if($vehicle->image)
                <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->full_name }}"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center bg-neutral-900">
                    <svg class="w-16 h-16 text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                </div>
            @endif
            {{-- Dark gradient overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
        </div>

        {{-- Brand badge top left --}}
        <div class="absolute top-3 left-3 flex items-center gap-2 bg-black/70 backdrop-blur-xl rounded-full pl-1 pr-3 py-1">
            @if($vehicle->brand && $vehicle->brand->logo)
                <img src="{{ asset('storage/' . $vehicle->brand->logo) }}" alt="{{ $vehicle->brand->name }}" class="w-6 h-6 object-contain bg-white rounded-full p-0.5" loading="lazy">
            @else
                <div class="w-6 h-6 bg-white rounded-full flex items-center justify-center">
                    <span class="text-black text-[10px] font-black">{{ strtoupper(substr($vehicle->brand->name ?? 'V', 0, 2)) }}</span>
                </div>
            @endif
            <span class="text-white text-xs font-semibold">{{ $vehicle->brand->name ?? '' }}</span>
        </div>

        {{-- Year badge top right --}}
        @if($vehicle->year)
            <div class="absolute top-3 right-3 bg-white/10 backdrop-blur-xl rounded-full px-3 py-1">
                <span class="text-white text-xs font-bold">{{ $vehicle->year }}</span>
            </div>
        @endif

        {{-- Sponsored Badge --}}
        @if($vehicle->is_boosted)
            <div class="absolute top-12 left-3 bg-gradient-to-r from-amber-500 to-orange-500 px-3 py-1 rounded-full">
                <span class="text-[10px] font-black text-white uppercase tracking-wider">Sponsorisé</span>
            </div>
        @endif

        {{-- Special Offer Badge --}}
        @if($vehicle->activeOffer)
            <div class="absolute top-12 right-3 bg-gradient-to-r from-red-500 to-pink-500 px-3 py-1 rounded-full animate-pulse">
                <span class="text-[10px] font-black text-white uppercase tracking-wider">{{ $vehicle->activeOffer->badge_text }}</span>
            </div>
        @endif

        {{-- Model name overlay bottom --}}
        <div class="absolute bottom-0 left-0 right-0 p-4">
            <h4 class="text-white text-lg font-bold leading-tight drop-shadow-lg">
                {{ $vehicle->model ?? $vehicle->full_name }}
            </h4>
            @if($loueur && $loueur->company_name)
                <p class="text-white/60 text-xs mt-1">{{ $loueur->company_name }}</p>
            @endif
        </div>
    </a>

    {{-- Price Section --}}
    <div class="px-5 py-4 flex items-end justify-between border-b border-white/5">
        <div>
            <span class="text-3xl font-black text-white tracking-tight">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }}</span>
            <span class="text-white/40 text-sm ml-1">DA/j</span>
        </div>
        @if($vehicle->price_per_day_eur)
            <span class="text-white/50 text-sm font-medium">{{ number_format($vehicle->price_per_day_eur, 0) }}&euro;</span>
        @endif
    </div>

    {{-- Specs Icons Row --}}
    <div class="px-5 py-4 flex items-center justify-between gap-2">
        {{-- Transmission --}}
        <div class="flex flex-col items-center gap-1.5 flex-1">
            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center">
                @if($vehicle->transmission === 'automatic')
                    {{-- Auto/A icon --}}
                    <span class="text-white font-black text-sm">A</span>
                @else
                    {{-- Manual/M icon --}}
                    <span class="text-white font-black text-sm">M</span>
                @endif
            </div>
            <span class="text-white/50 text-[10px] font-medium uppercase tracking-wide">{{ $vehicle->transmission === 'automatic' ? 'Auto' : 'Manuel' }}</span>
        </div>

        {{-- Fuel --}}
        <div class="flex flex-col items-center gap-1.5 flex-1">
            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center">
                @if(strtolower($vehicle->fuel_type ?? 'diesel') === 'diesel')
                    <svg class="w-5 h-5 text-amber-400" viewBox="0 0 24 24" fill="currentColor"><path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33 0 1.38 1.12 2.5 2.5 2.5.36 0 .69-.08 1-.21v7.21c0 .55-.45 1-1 1s-1-.45-1-1V14c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v16h10v-7.5h1.5v5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V9c0-.69-.28-1.32-.73-1.77zM12 10H6V5h6v5zm6 0c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg>
                @elseif(strtolower($vehicle->fuel_type ?? '') === 'essence')
                    <svg class="w-5 h-5 text-green-400" viewBox="0 0 24 24" fill="currentColor"><path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33 0 1.38 1.12 2.5 2.5 2.5.36 0 .69-.08 1-.21v7.21c0 .55-.45 1-1 1s-1-.45-1-1V14c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v16h10v-7.5h1.5v5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V9c0-.69-.28-1.32-.73-1.77zM12 10H6V5h6v5zm6 0c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg>
                @else
                    <svg class="w-5 h-5 text-blue-400" viewBox="0 0 24 24" fill="currentColor"><path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33 0 1.38 1.12 2.5 2.5 2.5.36 0 .69-.08 1-.21v7.21c0 .55-.45 1-1 1s-1-.45-1-1V14c0-1.1-.9-2-2-2h-1V5c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v16h10v-7.5h1.5v5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V9c0-.69-.28-1.32-.73-1.77zM12 10H6V5h6v5zm6 0c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg>
                @endif
            </div>
            <span class="text-white/50 text-[10px] font-medium uppercase tracking-wide">{{ ucfirst($vehicle->fuel_type ?? 'Diesel') }}</span>
        </div>

        {{-- Seats --}}
        <div class="flex flex-col items-center gap-1.5 flex-1">
            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center">
                <svg class="w-5 h-5 text-white/80" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <span class="text-white/50 text-[10px] font-medium uppercase tracking-wide">{{ $vehicle->seats ?? 5 }} places</span>
        </div>

        {{-- Air Conditioning --}}
        <div class="flex flex-col items-center gap-1.5 flex-1">
            <div class="w-10 h-10 rounded-xl {{ $vehicle->has_air_conditioning ? 'bg-cyan-500/20' : 'bg-white/5' }} flex items-center justify-center">
                <svg class="w-5 h-5 {{ $vehicle->has_air_conditioning ? 'text-cyan-400' : 'text-white/30' }}" viewBox="0 0 24 24" fill="currentColor"><path d="M22 11h-4.17l3.24-3.24-1.41-1.42L15 11h-2V9l4.66-4.66-1.42-1.41L13 6.17V2h-2v4.17L7.76 2.93 6.34 4.34 11 9v2H9L4.34 6.34 2.93 7.76 6.17 11H2v2h4.17l-3.24 3.24 1.41 1.42L9 13h2v2l-4.66 4.66 1.42 1.41L11 17.83V22h2v-4.17l3.24 3.24 1.42-1.41L13 15v-2h2l4.66 4.66 1.41-1.42L17.83 13H22z"/></svg>
            </div>
            <span class="text-[10px] font-medium uppercase tracking-wide {{ $vehicle->has_air_conditioning ? 'text-cyan-400' : 'text-white/30' }}">Clim</span>
        </div>
    </div>

    {{-- Features Pills --}}
    @php
        $features = [];
        if($vehicle->mileage_limit_per_day) {
            $features[] = ['text' => $vehicle->mileage_limit_per_day . ' km/j', 'color' => 'white/10'];
        } else {
            $features[] = ['text' => 'Km illimité', 'color' => 'emerald'];
        }
        if(!empty($degressivePricing) && is_array($degressivePricing) && count($degressivePricing) > 0) {
            $features[] = ['text' => 'Prix dégressif', 'color' => 'amber'];
        }
    @endphp

    @if(count($features) > 0 || count($badges) > 0)
        <div class="px-5 pb-4 flex flex-wrap gap-2">
            @foreach($features as $feature)
                @php
                    $pillClass = match($feature['color']) {
                        'emerald' => 'bg-emerald-500/20 text-emerald-400',
                        'amber' => 'bg-amber-500/20 text-amber-400',
                        default => 'bg-white/10 text-white/70',
                    };
                @endphp
                <span class="inline-flex items-center gap-1 {{ $pillClass }} text-[10px] font-semibold px-2.5 py-1 rounded-full uppercase tracking-wide">
                    {{ $feature['text'] }}
                </span>
            @endforeach
            @foreach($badges as $badge)
                @php
                    $badgeClass = match($badge['color'] ?? 'gray') {
                        'green' => 'bg-emerald-500/20 text-emerald-400',
                        'blue' => 'bg-blue-500/20 text-blue-400',
                        'amber' => 'bg-amber-500/20 text-amber-400',
                        'red' => 'bg-red-500/20 text-red-400',
                        default => 'bg-white/10 text-white/70',
                    };
                @endphp
                <span class="inline-flex items-center gap-1 {{ $badgeClass }} text-[10px] font-semibold px-2.5 py-1 rounded-full uppercase tracking-wide">
                    {{ $badge['text'] ?? $badge }}
                </span>
            @endforeach
        </div>
    @endif

    {{-- Action --}}
    <div class="p-4 mt-auto">
        <a href="{{ route('vehicles.show', $vehicle->slug) }}"
           data-track="view_details"
           data-vehicle-id="{{ $vehicle->id }}"
           data-loueur-id="{{ $loueur?->id }}"
           class="flex items-center justify-center gap-2 h-12 bg-white text-black text-sm font-bold rounded-xl hover:bg-white/90 transition-all w-full">
            Réserver
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</article>

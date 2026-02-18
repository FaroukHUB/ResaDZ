@php
    $loueur = $vehicle->loueur;
    $badges = $loueur ? $loueur->getBadges() : [];
    $degressivePricing = $vehicle->degressive_pricing;
@endphp

<article class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col group border border-gray-100">
    {{-- Head: Brand logo + Model name + Year --}}
    <div class="flex items-center justify-between px-4 py-3 bg-gray-900">
        <div class="flex items-center gap-3 min-w-0">
            @if($vehicle->brand && $vehicle->brand->logo)
                <img src="{{ asset('storage/' . $vehicle->brand->logo) }}" alt="{{ $vehicle->brand->name }}" class="w-8 h-8 object-contain bg-white rounded-full p-1 shrink-0" loading="lazy">
            @elseif($vehicle->brand)
                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center shrink-0">
                    <span class="text-gray-900 text-xs font-bold">{{ strtoupper(substr($vehicle->brand->name, 0, 2)) }}</span>
                </div>
            @endif
            <h4 class="text-white text-sm font-semibold truncate">
                @if($vehicle->brand){{ $vehicle->brand->name }}@endif
                {{ $vehicle->model ?? '' }}
            </h4>
        </div>
        @if($vehicle->year)
            <span class="text-xs text-gray-400 bg-gray-800 px-2 py-1 rounded-full shrink-0">{{ $vehicle->year }}</span>
        @endif
    </div>

    {{-- Photo --}}
    <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="block relative">
        <div class="aspect-[16/10] bg-gray-100 overflow-hidden">
            @if($vehicle->image)
                <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->full_name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                </div>
            @endif
        </div>
        {{-- Loueur watermark --}}
        @if($loueur && $loueur->company_name)
            <div class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-sm px-2 py-1 rounded text-xs text-white font-medium">
                {{ $loueur->company_name }}
            </div>
        @endif
        {{-- Category badge --}}
        @if($vehicle->category)
            <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-full text-xs text-gray-700 font-medium">
                {{ $vehicle->category->name }}
            </div>
        @endif
    </a>

    {{-- Price bar --}}
    <div class="px-4 py-3 bg-gray-900 flex items-baseline justify-between">
        <div>
            <span class="text-2xl font-black text-white">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }}</span>
            <span class="text-sm text-gray-400 ml-1">DA/jour</span>
        </div>
        @if($vehicle->price_per_day_eur)
            <span class="text-sm font-semibold text-gray-400">{{ number_format($vehicle->price_per_day_eur, 0) }} &euro;/j</span>
        @endif
    </div>

    {{-- Specs grid --}}
    <div class="grid grid-cols-2 gap-px bg-gray-100 text-sm">
        {{-- Boîte --}}
        <div class="flex items-center gap-2 px-3 py-2.5 bg-white">
            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <div class="min-w-0">
                <div class="text-xs text-gray-400">Boîte</div>
                <div class="text-gray-800 font-medium truncate">{{ $vehicle->transmission === 'automatic' ? 'Automatique' : 'Manuelle' }}</div>
            </div>
        </div>

        {{-- Carburant --}}
        <div class="flex items-center gap-2 px-3 py-2.5 bg-white">
            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
            <div class="min-w-0">
                <div class="text-xs text-gray-400">Carburant</div>
                <div class="text-gray-800 font-medium truncate">{{ ucfirst($vehicle->fuel_type ?? 'Diesel') }}</div>
            </div>
        </div>

        {{-- Places --}}
        <div class="flex items-center gap-2 px-3 py-2.5 bg-white">
            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <div class="min-w-0">
                <div class="text-xs text-gray-400">Capacité</div>
                <div class="text-gray-800 font-medium">{{ $vehicle->seats ?? 5 }} places</div>
            </div>
        </div>

        {{-- Km/jour --}}
        <div class="flex items-center gap-2 px-3 py-2.5 bg-white">
            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            <div class="min-w-0">
                <div class="text-xs text-gray-400">Km/jour</div>
                <div class="text-gray-800 font-medium">
                    @if($vehicle->mileage_limit_per_day)
                        {{ $vehicle->mileage_limit_per_day }} km
                    @else
                        <span class="text-green-600">Illimité</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Badges (loueur features) --}}
    @if(count($badges) > 0)
        <div class="px-4 py-3 flex flex-wrap gap-1.5 bg-white border-t border-gray-100">
            @foreach($badges as $badge)
                @php
                    $pillBg = match($badge['color'] ?? 'gray') {
                        'green' => 'bg-green-50 text-green-700',
                        'blue' => 'bg-blue-50 text-blue-700',
                        'amber' => 'bg-amber-50 text-amber-700',
                        'red' => 'bg-red-50 text-red-700',
                        default => 'bg-gray-50 text-gray-700',
                    };
                @endphp
                <span class="inline-flex items-center gap-1 {{ $pillBg }} text-xs font-medium px-2 py-1 rounded-full">
                    @if(($badge['icon'] ?? '') === 'check')
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    @elseif(($badge['icon'] ?? '') === 'plane')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>
                    @elseif(($badge['icon'] ?? '') === 'truck')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8M8 17H5.236a2 2 0 01-1.789-1.106l-.894-1.788A2 2 0 013 13.382V7a2 2 0 012-2h10a2 2 0 012 2v6m-9 4h8m0 0h2.764a2 2 0 001.789-1.106l.894-1.788A2 2 0 0021 13.382V11a2 2 0 00-2-2h-2"/></svg>
                    @elseif(($badge['icon'] ?? '') === 'arrow-down')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    @elseif(($badge['icon'] ?? '') === 'infinity')
                        <span class="text-xs font-bold">&infin;</span>
                    @elseif(($badge['icon'] ?? '') === 'star')
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endif
                    {{ $badge['text'] ?? $badge }}
                </span>
            @endforeach
        </div>
    @endif

    {{-- Degressive pricing hint --}}
    @if(!empty($degressivePricing) && is_array($degressivePricing) && count($degressivePricing) > 0)
        <div class="px-4 py-2 bg-amber-50 border-t border-amber-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            <span class="text-xs text-amber-700 font-medium">Prix dégressif disponible</span>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center gap-2 px-4 py-3 bg-white border-t border-gray-100 mt-auto">
        @if($loueur && $loueur->phone)
            <a href="tel:{{ $loueur->phone }}" class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                Appeler
            </a>
        @endif
        <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
            Réserver
        </a>
    </div>
</article>

@php
    $loueur = $vehicle->loueur;
    $badges = $loueur ? $loueur->getBadges() : [];
@endphp

<article class="bg-gray-900 rounded-xl overflow-hidden border-2 border-amber-500/60 shadow-lg shadow-amber-500/10 hover:shadow-amber-500/25 transition-all duration-300 flex flex-col">
    {{-- Head: Brand logo + Model name --}}
    <div class="flex items-center gap-3 px-4 py-3 border-b-2 border-amber-500/60">
        @if($vehicle->brand && $vehicle->brand->logo)
            <img src="{{ asset('storage/' . $vehicle->brand->logo) }}" alt="{{ $vehicle->brand->name }}" class="w-6 h-6 object-contain" loading="lazy">
        @elseif($vehicle->brand)
            <div class="w-6 h-6 bg-amber-500/20 rounded flex items-center justify-center">
                <span class="text-amber-400 text-xs font-bold">{{ strtoupper(substr($vehicle->brand->name, 0, 1)) }}</span>
            </div>
        @endif
        <h4 class="text-white text-sm font-semibold truncate">
            @if($vehicle->brand)<span class="text-amber-400">{{ $vehicle->brand->name }}</span> @endif{{ $vehicle->model ?? '' }}
        </h4>
    </div>

    {{-- Photo --}}
    <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="block">
        <div class="aspect-[16/10] bg-gray-950 overflow-hidden">
            @if($vehicle->image)
                <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->full_name }}"
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                </div>
            @endif
        </div>
    </a>

    {{-- Body: Price + Badges --}}
    <div class="px-4 pt-4 pb-2 flex-1">
        {{-- Price --}}
        <div class="mb-3">
            <span class="text-2xl font-extrabold text-white">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }} DA</span>
            <span class="block text-xs text-gray-400 mt-0.5">Prix / jour</span>
            @if($vehicle->price_per_day_eur)
                <span class="text-xs text-amber-400/70">~ {{ number_format($vehicle->price_per_day_eur, 0) }} &euro;/jour</span>
            @endif
        </div>

        {{-- Badges --}}
        @if(count($badges) > 0)
            <div class="flex flex-wrap gap-1.5">
                @foreach($badges as $badge)
                    <span class="text-xs text-gray-300 bg-gray-800 border border-gray-700 rounded-full px-2.5 py-1">{{ $badge }}</span>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Loueur info --}}
    @if($loueur)
        <div class="px-4 py-2 text-xs text-gray-500 border-t border-gray-800">
            {{ $loueur->company_name }}@if($loueur->city || $loueur->wilaya) &middot; {{ $loueur->city ?? $loueur->wilaya }}@endif
            @if($loueur->is_verified)
                <span class="text-green-500 ml-1" title="Vérifié">&#10003;</span>
            @endif
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center gap-2 px-4 py-3 border-t border-gray-800">
        @if($loueur && $loueur->phone)
            <a href="tel:{{ $loueur->phone }}" class="flex-1 flex items-center justify-center gap-1.5 py-2.5 bg-gradient-to-br from-gray-800 to-gray-900 text-white text-sm font-semibold rounded-lg hover:brightness-110 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                Appeler
            </a>
        @endif
        <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="flex-1 flex items-center justify-center gap-1.5 py-2.5 bg-gray-900 text-amber-400 text-sm font-semibold rounded-lg border border-amber-500/60 hover:bg-amber-500/10 transition shadow-md shadow-amber-500/10">
            Voir l'offre
        </a>
    </div>
</article>

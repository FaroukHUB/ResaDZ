@php
    $loueur = $vehicle->loueur;
    $badges = $loueur ? $loueur->getBadges() : [];
@endphp

<article class="bg-black rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col group">
    {{-- Head: Brand logo + Model name --}}
    <div class="flex items-center gap-3 px-4 py-3 bg-black">
        @if($vehicle->brand && $vehicle->brand->logo)
            <img src="{{ asset('storage/' . $vehicle->brand->logo) }}" alt="{{ $vehicle->brand->name }}" class="w-8 h-8 object-contain bg-white rounded-full p-1" loading="lazy">
        @elseif($vehicle->brand)
            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                <span class="text-black text-xs font-bold">{{ strtoupper(substr($vehicle->brand->name, 0, 2)) }}</span>
            </div>
        @endif
        <h4 class="text-white text-sm font-semibold">
            @if($vehicle->brand)
                <span class="text-white">{{ $vehicle->brand->name }}</span>
            @endif
            <span class="text-gray-400">{{ $vehicle->model ?? '' }}</span>
            @if($vehicle->year)
                <span class="text-gray-500">{{ $vehicle->year }}</span>
            @endif
        </h4>
    </div>

    {{-- Photo --}}
    <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="block relative">
        <div class="aspect-[16/10] bg-gray-900 overflow-hidden">
            @if($vehicle->image)
                <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->full_name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gray-800">
                    <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                </div>
            @endif
        </div>
        {{-- Loueur watermark --}}
        @if($loueur && $loueur->company_name)
            <div class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-sm px-2 py-1 rounded text-xs text-white font-medium">
                {{ $loueur->company_name }}
            </div>
        @endif
    </a>

    {{-- Conditions button --}}
    @if($loueur)
        <div class="flex justify-center -mt-4 relative z-10">
            <a href="{{ route('vehicles.show', $vehicle->slug) }}#conditions" class="px-4 py-2 bg-white text-black text-xs font-semibold rounded-full border-2 border-gray-300 hover:border-gray-400 transition shadow-lg">
                Voir nos conditions
            </a>
        </div>
    @endif

    {{-- Body: Price + Badges --}}
    <div class="px-4 pt-4 pb-3 flex-1 bg-black">
        {{-- Price --}}
        <div class="mb-4">
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-white">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }} DA</span>
                @if($vehicle->price_per_day_eur)
                    <span class="text-gray-400">/</span>
                    <span class="text-lg font-bold text-gray-400">{{ number_format($vehicle->price_per_day_eur, 0) }} &euro;</span>
                @endif
            </div>
            <span class="text-xs text-gray-500">Prix / jour</span>
        </div>

        {{-- Badges --}}
        @if(count($badges) > 0)
            <div class="space-y-2">
                @foreach($badges as $badge)
                    @php
                        $bgColor = match($badge['color'] ?? 'gray') {
                            'green' => 'bg-green-900/30',
                            'blue' => 'bg-blue-900/30',
                            'amber' => 'bg-amber-900/30',
                            'red' => 'bg-red-900/30',
                            default => 'bg-gray-800',
                        };
                        $textColor = match($badge['color'] ?? 'gray') {
                            'green' => 'text-green-400',
                            'blue' => 'text-blue-400',
                            'amber' => 'text-amber-400',
                            'red' => 'text-red-400',
                            default => 'text-gray-300',
                        };
                    @endphp
                    <div class="flex items-center gap-2 {{ $bgColor }} rounded-lg px-3 py-2">
                        @if(($badge['icon'] ?? '') === 'check')
                            <span class="text-green-400 text-sm">&#10003;</span>
                        @elseif(($badge['icon'] ?? '') === 'plane')
                            <span class="text-blue-400 text-sm">&#9992;</span>
                        @elseif(($badge['icon'] ?? '') === 'truck')
                            <span class="text-blue-400 text-sm">&#128666;</span>
                        @elseif(($badge['icon'] ?? '') === 'arrow-down')
                            <span class="text-amber-400 text-sm">&#10549;</span>
                        @elseif(($badge['icon'] ?? '') === 'infinity')
                            <span class="text-green-400 text-sm">&#8734;</span>
                        @elseif(($badge['icon'] ?? '') === 'star')
                            <span class="text-amber-400 text-sm">&#9733;</span>
                        @endif
                        <span class="{{ $textColor }} text-sm font-medium">{{ $badge['text'] ?? $badge }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3 px-4 py-4 bg-black border-t border-gray-800">
        @if($loueur && $loueur->phone)
            <a href="tel:{{ $loueur->phone }}" class="flex-1 flex items-center justify-center gap-2 py-3 bg-white text-black text-sm font-bold rounded-lg hover:bg-gray-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                Nous appeler
            </a>
        @endif
        <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="flex-1 flex items-center justify-center gap-2 py-3 bg-transparent text-red-500 text-sm font-bold rounded-lg border-2 border-red-500 hover:bg-red-500 hover:text-white transition">
            Faire une demande
        </a>
    </div>
</article>

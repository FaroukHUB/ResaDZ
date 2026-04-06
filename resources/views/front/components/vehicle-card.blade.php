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
            <div class="absolute top-3 right-3 z-10" style="animation: offerBounce 2s ease-in-out infinite;">
                <div style="background: linear-gradient(135deg, #22c55e, #16a34a); padding: 6px 14px; border-radius: 8px; box-shadow: 0 4px 15px rgba(34,197,94,0.5);">
                    <span style="color: white; font-size: 13px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase;">{{ $vehicle->activeOffer->badge_text }}</span>
                </div>
            </div>
        @endif

        {{-- En location - Ruban diagonal --}}
        @if($vehicle->status === 'reserved')
            <div class="absolute inset-0 z-10 overflow-hidden pointer-events-none">
                <div style="position:absolute;top:28px;left:-35px;width:170px;text-align:center;padding:6px 0;background:linear-gradient(135deg,#dc2626,#991b1b);color:white;font-size:11px;font-weight:900;letter-spacing:1.5px;text-transform:uppercase;transform:rotate(-45deg);box-shadow:0 2px 8px rgba(0,0,0,0.3);">
                    INDISPONIBLE
                </div>
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
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-white">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }} DA</span>
                    @if($vehicle->price_per_day_eur)
                        <span class="text-white/60 text-lg font-semibold">/ {{ number_format($vehicle->price_per_day_eur, 0) }} €</span>
                    @endif
                </div>
                <p class="text-white/60 text-sm mt-1">Prix / jour</p>
            </div>
            {{-- Favori + Partager --}}
            <div class="flex items-center gap-2">
                <button type="button"
                        x-data="{ liked: localStorage.getItem('fav_{{ $vehicle->id }}') === '1' }"
                        x-on:click.stop="liked = !liked; liked ? localStorage.setItem('fav_{{ $vehicle->id }}', '1') : localStorage.removeItem('fav_{{ $vehicle->id }}')"
                        class="w-9 h-9 rounded-full flex items-center justify-center transition"
                        :class="liked ? 'bg-red-500/20 text-red-500' : 'bg-white/10 text-white/50 hover:text-red-400'"
                        title="Favori">
                    <svg class="w-5 h-5" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                </button>
                <div x-data="{ open: false }" class="relative">
                    <button type="button" x-on:click.stop="open = !open"
                            class="w-9 h-9 rounded-full flex items-center justify-center transition"
                            style="background: rgba(59,130,246,0.2); color: #60a5fa;"
                            title="Partager">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/></svg>
                    </button>
                    <div x-show="open" x-on:click.away="open = false" x-transition
                         class="absolute bottom-full right-0 mb-2 rounded-2xl shadow-2xl z-50 overflow-hidden" style="background: white; width: 220px; border: 1px solid #e5e7eb;">
                        <div style="padding: 12px 16px; background: linear-gradient(135deg, #1e293b, #334155); color: white;">
                            <p style="font-weight: 700; font-size: 13px;">Partager ce véhicule</p>
                        </div>
                        @php $shareUrl = route('vehicles.show', $vehicle->slug); $shareText = $vehicle->full_name . ' - ' . number_format($vehicle->price_per_day, 0, ',', ' ') . ' DA/jour sur ResaDZ'; @endphp
                        <div style="padding: 8px;">
                            <a href="https://wa.me/?text={{ urlencode($shareText . ' ' . $shareUrl) }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 text-gray-800 text-sm font-medium transition">
                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                WhatsApp
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 text-gray-800 text-sm font-medium transition">
                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                Facebook
                            </a>
                            <a href="https://t.me/share/url?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareText) }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 text-gray-800 text-sm font-medium transition">
                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="#0088CC"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                Telegram
                            </a>
                            <button type="button" x-on:click="if(navigator.share){navigator.share({title:'{{ addslashes($vehicle->full_name) }}',text:'{{ addslashes($shareText) }}',url:'{{ $shareUrl }}'})}else{window.open('https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}')}"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 text-gray-800 text-sm font-medium transition w-full text-left">
                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="#6366f1"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-4h2v2h-2v-2zm0-2h2V7h-2v7z"/><path d="M4 12l1.41 1.41L12 6.83l6.59 6.58L20 12l-8-8-8 8z"/></svg>
                                Autres apps
                            </button>
                            <div style="height: 1px; background: #e5e7eb; margin: 4px 0;"></div>
                            <button type="button" x-on:click="navigator.clipboard.writeText('{{ $shareUrl }}'); open = false; $dispatch('notify', {message: 'Lien copié !'})"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 text-gray-800 text-sm font-medium transition w-full text-left">
                                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                Copier le lien
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                Indisponible
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

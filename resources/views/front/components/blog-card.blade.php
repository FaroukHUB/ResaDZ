@php
    $link = $post->cta_link ?: route('blog.show', $post->slug);
    $catColors = [
        'promo' => 'bg-amber-500/20 text-amber-400',
        'guide' => 'bg-blue-500/20 text-blue-400',
        'news' => 'bg-green-500/20 text-green-400',
        'destination' => 'bg-purple-500/20 text-purple-400',
    ];
    $catLabels = [
        'promo' => 'Promo',
        'guide' => 'Guide',
        'news' => 'Actu',
        'destination' => 'Destination',
    ];
@endphp

<a href="{{ $link }}" class="block group rounded-2xl overflow-hidden bg-gradient-to-br from-neutral-800/50 to-neutral-900/80 border border-white/10 hover:border-white/20 transition-all duration-300 hover:shadow-xl hover:shadow-green-500/5 hover:-translate-y-1">
    {{-- Image avec badge overlay --}}
    <div class="relative aspect-[16/10] overflow-hidden">
        @if($post->featured_image)
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
        @else
            <div class="w-full h-full bg-gradient-to-br from-green-900/30 via-neutral-800 to-neutral-900 flex items-center justify-center">
                <svg class="w-12 h-12 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            </div>
        @endif

        {{-- Badge overlay --}}
        @if($post->badge_text || $post->badge_value)
            <div class="absolute bottom-4 left-4">
                <div class="border-2 border-white/80 px-4 py-3 text-center bg-black/60 backdrop-blur-md rounded-lg">
                    @if($post->badge_text)
                        <span class="block text-white text-[10px] font-bold uppercase tracking-wider">{{ $post->badge_text }}</span>
                    @endif
                    @if($post->badge_value)
                        <span class="block text-white text-2xl font-black leading-tight">{{ $post->badge_value }}</span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Category tag --}}
        @if($post->category)
            <div class="absolute top-4 left-4">
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider backdrop-blur-sm {{ $catColors[$post->category] ?? 'bg-green-500/20 text-green-400' }}">
                    {{ $catLabels[$post->category] ?? $post->category }}
                </span>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-6">
        <h3 class="text-white font-bold text-lg leading-tight group-hover:text-green-400 transition-colors duration-300 line-clamp-2">{{ $post->title }}</h3>
        @if($post->excerpt)
            <p class="text-white/50 text-sm mt-3 line-clamp-2 leading-relaxed">{{ $post->excerpt }}</p>
        @endif

        <div class="mt-5 flex items-center justify-between">
            <span class="inline-flex items-center gap-2 text-green-400 text-sm font-semibold group-hover:gap-3 transition-all">
                {{ $post->cta_text ?: 'Lire l\'article' }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </span>
            @if($post->read_time)
                <span class="text-white/30 text-xs">{{ $post->read_time }} min</span>
            @endif
        </div>
    </div>
</a>

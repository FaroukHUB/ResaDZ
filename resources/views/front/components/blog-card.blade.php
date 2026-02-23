@php $link = $post->cta_link ?: route('blog.show', $post->slug); @endphp

<a href="{{ $link }}" class="block group rounded-lg overflow-hidden bg-black border border-neutral-800 hover:border-neutral-600 transition">
    {{-- Image avec badge overlay --}}
    <div class="relative aspect-[16/10] overflow-hidden">
        @if($post->featured_image)
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
        @else
            <div class="w-full h-full bg-gradient-to-br from-neutral-800 to-neutral-900"></div>
        @endif

        {{-- Badge overlay --}}
        @if($post->badge_text || $post->badge_value)
            <div class="absolute bottom-4 left-4">
                <div class="border-2 border-white px-4 py-3 text-center bg-black/50 backdrop-blur-sm">
                    @if($post->badge_text)
                        <span class="block text-white text-[10px] font-bold uppercase tracking-wider">{{ $post->badge_text }}</span>
                    @endif
                    @if($post->badge_value)
                        <span class="block text-white text-2xl font-black leading-tight">{{ $post->badge_value }}</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-5">
        <h3 class="text-white font-black text-lg uppercase leading-tight group-hover:text-gray-300 transition">{{ $post->title }}</h3>
        @if($post->excerpt)
            <p class="text-white/60 text-sm mt-2">{{ $post->excerpt }}</p>
        @endif

        <div class="mt-4">
            <span class="inline-flex items-center px-5 py-2 bg-white text-black text-sm font-bold rounded-full hover:bg-gray-200 transition">
                {{ $post->cta_text ?: 'Lire' }}
            </span>
        </div>
    </div>
</a>

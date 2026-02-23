@extends('front.layouts.app')

@section('title', ($post->meta_title ?: $post->title) . ' | Actualités ResaDZ')
@section('meta_description', $post->meta_description ?: $post->excerpt)

@section('meta_extra')
<meta property="og:title" content="{{ $post->title }}">
<meta property="og:description" content="{{ $post->excerpt }}">
@if($post->featured_image)
    <meta property="og:image" content="{{ asset('storage/' . $post->featured_image) }}">
@endif
<meta property="og:type" content="article">
<meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
@endsection

@section('content')

    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-b from-neutral-900 via-neutral-900 to-neutral-800">
        {{-- Featured Image --}}
        @if($post->featured_image)
            <div class="relative h-64 md:h-80 lg:h-96">
                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-neutral-900/30 via-transparent to-neutral-900"></div>

                {{-- Badge sur l'image --}}
                @if($post->badge_text || $post->badge_value)
                    <div class="absolute top-6 left-6 md:top-8 md:left-8">
                        <div class="border-2 border-white px-4 py-3 text-center bg-black/70 backdrop-blur-sm rounded-lg">
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
        @endif

        {{-- Title Section --}}
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            {{-- Breadcrumb --}}
            <nav class="flex flex-wrap items-center gap-2 text-sm text-white/50 mb-6">
                <a href="{{ route('home') }}" class="hover:text-white transition">Accueil</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('blog.index') }}" class="hover:text-white transition">Actualités</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-white/70">{{ Str::limit($post->title, 30) }}</span>
            </nav>

            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-3 mb-5">
                @if($post->category)
                    @php
                        $catColors = [
                            'promo' => 'bg-amber-500 text-black',
                            'guide' => 'bg-blue-500 text-white',
                            'news' => 'bg-green-500 text-white',
                            'destination' => 'bg-purple-500 text-white',
                        ];
                        $catLabels = [
                            'promo' => 'Promotion',
                            'guide' => 'Guide',
                            'news' => 'Actualité',
                            'destination' => 'Destination',
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $catColors[$post->category] ?? 'bg-green-500 text-white' }}">{{ $catLabels[$post->category] ?? $post->category }}</span>
                @endif
                @if($post->published_at)
                    <span class="text-white/60 text-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $post->published_at->translatedFormat('d F Y') }}
                    </span>
                @endif
                <span class="text-white/60 text-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $post->read_time }} min
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-2xl md:text-4xl lg:text-5xl font-black text-white leading-tight">{{ $post->title }}</h1>

            @if($post->excerpt)
                <p class="text-lg md:text-xl text-white/60 mt-4 leading-relaxed">{{ $post->excerpt }}</p>
            @endif
        </div>
    </section>

    {{-- Article Content --}}
    <article class="bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            {{-- Content --}}
            <div class="prose prose-lg max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-p:text-gray-600 prose-a:text-green-600 prose-a:font-semibold prose-a:underline prose-a:decoration-2 hover:prose-a:text-green-500 prose-img:rounded-xl prose-img:shadow-lg prose-blockquote:border-l-green-500 prose-blockquote:bg-green-50 prose-blockquote:py-1 prose-blockquote:px-6 prose-blockquote:rounded-r-lg">
                {!! $post->content !!}
            </div>

            {{-- CTA --}}
            @if($post->cta_text && $post->cta_link)
                <div class="mt-12 p-8 bg-gradient-to-r from-green-600 to-green-700 rounded-xl text-center">
                    <a href="{{ $post->cta_link }}" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-green-700 font-bold rounded-full hover:bg-green-50 transition text-lg shadow-lg shadow-green-900/20">
                        {{ $post->cta_text }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            @endif

            {{-- Tags --}}
            @if($post->tags && count($post->tags) > 0)
                <div class="mt-10 pt-8 border-t border-gray-200">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-gray-400 text-sm font-medium">Tags:</span>
                        @foreach($post->tags as $tag)
                            <span class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm rounded-full transition cursor-default">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Share buttons --}}
            <div class="flex items-center justify-center gap-4 mt-10 pt-8 border-t border-gray-200">
                <span class="text-gray-400 text-sm">Partager:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-blue-500 hover:text-white text-gray-500 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/></svg>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-black hover:text-white text-gray-500 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . request()->url()) }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-green-500 hover:text-white text-gray-500 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
            </div>
        </div>
    </article>

    {{-- Related Posts --}}
    @if($relatedPosts->count() > 0)
        <section class="relative bg-gradient-to-b from-neutral-900 via-neutral-950 to-black py-20 overflow-hidden">
            {{-- Decorative --}}
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-1/2 left-0 w-96 h-96 bg-green-500/20 rounded-full blur-3xl transform -translate-x-1/2 -translate-y-1/2"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-1 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded-full"></div>
                    <h2 class="text-2xl font-bold text-white">Articles similaires</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($relatedPosts as $relatedPost)
                        @include('front.components.blog-card', ['post' => $relatedPost])
                    @endforeach
                </div>

                <div class="mt-12 text-center">
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-full transition backdrop-blur-sm border border-white/10">
                        Voir toutes les actualités
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
        </section>
    @endif

@endsection

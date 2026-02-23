@extends('front.layouts.app')

@section('title', ($post->meta_title ?: $post->title) . ' | Blog ResaDZ')
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

    {{-- Hero Image --}}
    @if($post->featured_image)
        <div class="relative h-64 md:h-96 bg-black">
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                 class="w-full h-full object-cover opacity-80">
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>

            @if($post->badge_text || $post->badge_value)
                <div class="absolute bottom-8 left-8">
                    <div class="border-2 border-white px-5 py-4 text-center bg-black/50 backdrop-blur-sm">
                        @if($post->badge_text)
                            <span class="block text-white text-xs font-bold uppercase tracking-wider">{{ $post->badge_text }}</span>
                        @endif
                        @if($post->badge_value)
                            <span class="block text-white text-3xl font-black leading-tight">{{ $post->badge_value }}</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Article --}}
    <article class="bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
                <a href="{{ route('home') }}" class="hover:text-gray-600 transition">Accueil</a>
                <span>/</span>
                <a href="{{ route('blog.index') }}" class="hover:text-gray-600 transition">Blog</a>
                <span>/</span>
                <span class="text-gray-600">{{ Str::limit($post->title, 40) }}</span>
            </nav>

            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-4 mb-6">
                @if($post->category)
                    @php
                        $catLabels = [
                            'promo' => 'Promotions',
                            'guide' => 'Guides & Conseils',
                            'news' => 'Actualités',
                            'destination' => 'Destinations',
                        ];
                    @endphp
                    <span class="text-amber-600 text-sm font-semibold uppercase tracking-wider">{{ $catLabels[$post->category] ?? $post->category }}</span>
                @endif
                @if($post->published_at)
                    <span class="text-gray-400 text-sm">{{ $post->published_at->translatedFormat('d F Y') }}</span>
                @endif
                <span class="text-gray-400 text-sm">{{ $post->read_time }} min de lecture</span>
            </div>

            {{-- Title --}}
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 leading-tight">{{ $post->title }}</h1>

            @if($post->excerpt)
                <p class="text-xl text-gray-500 mt-4">{{ $post->excerpt }}</p>
            @endif

            {{-- Content --}}
            <div class="mt-10 prose prose-lg max-w-none prose-headings:font-bold prose-a:text-green-600 prose-a:font-semibold prose-a:underline prose-a:decoration-2 hover:prose-a:text-green-500 prose-img:rounded-lg">
                {!! $post->content !!}
            </div>

            {{-- CTA --}}
            @if($post->cta_text && $post->cta_link)
                <div class="mt-12 text-center">
                    <a href="{{ $post->cta_link }}" class="inline-flex items-center px-8 py-4 bg-green-600 text-white font-bold rounded-lg hover:bg-green-500 transition text-lg">
                        {{ $post->cta_text }}
                    </a>
                </div>
            @endif

            {{-- Tags --}}
            @if($post->tags && count($post->tags) > 0)
                <div class="mt-10 flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </article>

    {{-- Related Posts --}}
    @if($relatedPosts->count() > 0)
        <section class="bg-neutral-950 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-white mb-8">Articles similaires</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $relatedPost)
                        @include('front.components.blog-card', ['post' => $relatedPost])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection

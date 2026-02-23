@extends('front.layouts.app')

@section('title', 'Blog - Actualités & Conseils Location | ResaDZ')
@section('meta_description', 'Retrouvez nos actualités, promotions et conseils pour la location de voitures en Algérie.')

@section('content')

    {{-- Hero --}}
    <section class="bg-black py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-black text-white">Blog</h1>
            <p class="text-white/60 mt-3 text-lg">Actualités, promotions et conseils</p>
        </div>
    </section>

    {{-- Featured Post --}}
    @if($featuredPost)
        <section class="bg-neutral-950 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <a href="{{ $featuredPost->cta_link ?: route('blog.show', $featuredPost->slug) }}" class="block group rounded-lg overflow-hidden bg-black border border-neutral-800 hover:border-neutral-600 transition">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <div class="relative aspect-[16/10] lg:aspect-auto overflow-hidden">
                            @if($featuredPost->featured_image)
                                <img src="{{ asset('storage/' . $featuredPost->featured_image) }}" alt="{{ $featuredPost->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full min-h-[300px] bg-gradient-to-br from-neutral-800 to-neutral-900"></div>
                            @endif
                            @if($featuredPost->badge_text || $featuredPost->badge_value)
                                <div class="absolute bottom-6 left-6">
                                    <div class="border-2 border-white px-5 py-4 text-center bg-black/50 backdrop-blur-sm">
                                        @if($featuredPost->badge_text)
                                            <span class="block text-white text-xs font-bold uppercase tracking-wider">{{ $featuredPost->badge_text }}</span>
                                        @endif
                                        @if($featuredPost->badge_value)
                                            <span class="block text-white text-3xl font-black leading-tight">{{ $featuredPost->badge_value }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="p-8 lg:p-12 flex flex-col justify-center">
                            @if($featuredPost->category)
                                <span class="text-amber-400 text-sm font-semibold uppercase tracking-wider">{{ $featuredPost->category }}</span>
                            @endif
                            <h2 class="text-white font-black text-2xl md:text-3xl uppercase leading-tight mt-2 group-hover:text-gray-300 transition">{{ $featuredPost->title }}</h2>
                            @if($featuredPost->excerpt)
                                <p class="text-white/60 mt-4 text-lg">{{ $featuredPost->excerpt }}</p>
                            @endif
                            <div class="mt-6">
                                <span class="inline-flex items-center px-6 py-3 bg-white text-black text-sm font-bold rounded-full">
                                    {{ $featuredPost->cta_text ?: 'Lire l\'article' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </section>
    @endif

    {{-- All Posts --}}
    <section class="bg-neutral-950 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($posts->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($posts as $post)
                        @include('front.components.blog-card', ['post' => $post])
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <p class="text-white/50 text-lg">Aucun article pour le moment.</p>
                </div>
            @endif
        </div>
    </section>

@endsection

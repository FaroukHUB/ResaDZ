@extends('front.layouts.app')

@section('title', 'Actualités - Promotions & Conseils Location | ResaDZ')
@section('meta_description', 'Retrouvez nos actualités, promotions et conseils pour la location de voitures en Algérie.')

@section('content')

    {{-- Hero with gradient mesh --}}
    <section class="relative bg-gradient-to-br from-neutral-900 via-black to-neutral-900 py-20 overflow-hidden">
        {{-- Decorative elements --}}
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-green-500/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
        </div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMtOS45NDEgMC0xOCA4LjA1OS0xOCAxOHM4LjA1OSAxOCAxOCAxOGM5Ljk0MSAwIDE4LTguMDU5IDE4LTE4cy04LjA1OS0xOC0xOC0xOHptMCAzMmMtNy43MzIgMC0xNC02LjI2OC0xNC0xNHM2LjI2OC0xNCAxNC0xNCAxNCA2LjI2OCAxNCAxNC02LjI2OCAxNC0xNCAxNHoiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iLjAyIi8+PC9nPjwvc3ZnPg==')] opacity-40"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-green-400 text-sm font-medium mb-6">
                ResaDZ Magazine
            </span>
            <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight">Actualités</h1>
            <p class="text-white/50 mt-4 text-lg max-w-2xl mx-auto">Découvrez nos dernières promotions, guides pratiques et conseils pour votre prochaine location</p>
        </div>
    </section>

    {{-- Featured Post --}}
    @if($featuredPost)
        <section class="relative bg-gradient-to-b from-neutral-900 to-neutral-950 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-1 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded-full"></div>
                    <h2 class="text-xl font-bold text-white">À la une</h2>
                </div>

                <a href="{{ $featuredPost->cta_link ?: route('blog.show', $featuredPost->slug) }}" class="block group relative rounded-2xl overflow-hidden bg-gradient-to-br from-neutral-800/50 to-neutral-900/50 backdrop-blur border border-white/10 hover:border-white/20 transition-all duration-500 hover:shadow-2xl hover:shadow-green-500/10">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <div class="relative aspect-[16/10] lg:aspect-[4/3] overflow-hidden">
                            @if($featuredPost->featured_image)
                                <img src="{{ asset('storage/' . $featuredPost->featured_image) }}" alt="{{ $featuredPost->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-r from-black/50 via-transparent to-transparent lg:bg-gradient-to-t lg:from-black/60 lg:via-transparent"></div>
                            @else
                                <div class="w-full h-full min-h-[300px] bg-gradient-to-br from-green-900/30 via-neutral-800 to-neutral-900"></div>
                            @endif
                            @if($featuredPost->badge_text || $featuredPost->badge_value)
                                <div class="absolute bottom-6 left-6">
                                    <div class="border-2 border-white/80 px-5 py-4 text-center bg-black/60 backdrop-blur-md rounded-lg">
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
                                @php
                                    $catColors = [
                                        'promo' => 'text-amber-400',
                                        'guide' => 'text-blue-400',
                                        'news' => 'text-green-400',
                                        'destination' => 'text-purple-400',
                                    ];
                                    $catLabels = [
                                        'promo' => 'Promotion',
                                        'guide' => 'Guide',
                                        'news' => 'Actualité',
                                        'destination' => 'Destination',
                                    ];
                                @endphp
                                <span class="{{ $catColors[$featuredPost->category] ?? 'text-green-400' }} text-sm font-semibold uppercase tracking-wider">{{ $catLabels[$featuredPost->category] ?? $featuredPost->category }}</span>
                            @endif
                            <h2 class="text-white font-black text-2xl md:text-4xl leading-tight mt-3 group-hover:text-green-400 transition-colors duration-300">{{ $featuredPost->title }}</h2>
                            @if($featuredPost->excerpt)
                                <p class="text-white/60 mt-4 text-lg leading-relaxed">{{ $featuredPost->excerpt }}</p>
                            @endif
                            <div class="mt-8 flex items-center gap-4">
                                <span class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-500 text-white text-sm font-bold rounded-full transition-colors">
                                    {{ $featuredPost->cta_text ?: 'Lire l\'article' }}
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </span>
                                @if($featuredPost->read_time)
                                    <span class="text-white/40 text-sm">{{ $featuredPost->read_time }} min de lecture</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </section>
    @endif

    {{-- All Posts --}}
    <section class="relative bg-gradient-to-b from-neutral-950 via-neutral-900 to-neutral-950 py-20">
        {{-- Subtle pattern --}}
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-10">
                <div class="w-1 h-8 bg-gradient-to-b from-amber-400 to-amber-600 rounded-full"></div>
                <h2 class="text-xl font-bold text-white">Tous les articles</h2>
            </div>

            @if($posts->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        @include('front.components.blog-card', ['post' => $post])
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-white/5 flex items-center justify-center">
                        <svg class="w-10 h-10 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    <p class="text-white/40 text-lg">Aucun article pour le moment</p>
                    <p class="text-white/30 text-sm mt-2">Revenez bientôt pour découvrir nos actualités</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Newsletter CTA --}}
    <section class="relative bg-gradient-to-r from-green-900 via-green-800 to-green-900 py-16 overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-black/20 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-white">Ne manquez aucune offre</h2>
            <p class="text-white/70 mt-3">Recevez nos meilleures promotions et conseils directement dans votre boîte mail</p>
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center max-w-lg mx-auto">
                <input type="email" placeholder="Votre email" class="flex-1 px-5 py-3 rounded-full bg-white/10 border border-white/20 text-white placeholder-white/50 focus:outline-none focus:border-white/40 focus:bg-white/15 transition">
                <button class="px-8 py-3 bg-white text-green-800 font-bold rounded-full hover:bg-green-50 transition-colors">
                    S'abonner
                </button>
            </div>
        </div>
    </section>

@endsection

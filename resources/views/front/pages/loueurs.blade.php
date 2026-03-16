@extends('front.layouts.app')

@section('title', 'Nos loueurs partenaires - Location de voitures en Algerie | ResaDZ')
@section('meta_description', 'Decouvrez tous nos loueurs partenaires verifies en Algerie. Location de voitures, transferts et services de qualite.')

@section('content')

    <!-- Hero -->
    <section class="bg-gradient-to-br from-green-600 via-green-700 to-green-800 py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <h1 class="text-3xl lg:text-5xl font-black text-white mb-4">Nos loueurs partenaires</h1>
            <p class="text-lg text-green-100 max-w-2xl mx-auto">Des professionnels verifies a votre service partout en Algerie</p>
            <div class="mt-6 flex items-center justify-center gap-6 text-green-100">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $loueurs->count() }} partenaires</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Verifies</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Grid -->
    <section class="py-12 lg:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($featuredLoueurs->count() > 0)
            <!-- Featured Partners -->
            <div class="mb-12">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Partenaires en vedette</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($featuredLoueurs as $loueur)
                        @include('front.pages.partials.loueur-card', ['loueur' => $loueur, 'featured' => true])
                    @endforeach
                </div>
            </div>
            @endif

            <!-- All Partners -->
            @if($loueurs->count() > $featuredLoueurs->count())
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Tous nos partenaires</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($loueurs as $loueur)
                        @if(!$loueur->is_featured_partner)
                            @include('front.pages.partials.loueur-card', ['loueur' => $loueur, 'featured' => false])
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @if($loueurs->count() === 0)
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun partenaire</h3>
                <p class="text-gray-500">Aucun loueur partenaire n'est disponible pour le moment.</p>
            </div>
            @endif
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">Vous etes loueur de voitures ?</h2>
            <p class="text-gray-600 mb-8 max-w-2xl mx-auto">Rejoignez ResaDZ et developpez votre activite en ligne. Gerez vos vehicules, reservations et finances depuis un seul tableau de bord.</p>
            <a href="{{ route('register') }}?type=loueur" class="inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-600/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Devenir partenaire
            </a>
        </div>
    </section>

@endsection

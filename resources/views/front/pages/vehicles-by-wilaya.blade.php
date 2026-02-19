@extends('front.layouts.app')

@section('title', 'Location voiture ' . $wilayaName . ' - ResaDZ')
@section('meta_description', 'Location de voitures à ' . $wilayaName . ', Algérie. Comparez ' . $totalVehicles . ' véhicules disponibles auprès de loueurs vérifiés. Réservez en ligne sur ResaDZ.')
@section('canonical', route('vehicles.by-wilaya', \Illuminate\Support\Str::slug($wilayaName)))

@section('meta_extra')
@php
$schemaData = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Location de voitures à ' . $wilayaName,
    'description' => 'Véhicules disponibles à la location à ' . $wilayaName . ', Algérie',
    'numberOfItems' => $totalVehicles,
];
@endphp
<script type="application/ld+json">{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')

    {{-- Hero --}}
    <section class="bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-400 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Accueil</a>
                <span class="mx-2">/</span>
                <a href="{{ route('vehicles.index') }}" class="hover:text-white">Véhicules</a>
                <span class="mx-2">/</span>
                <span class="text-white">{{ $wilayaName }}</span>
            </nav>
            <h1 class="text-3xl lg:text-4xl font-black text-white">
                Location voiture <span class="text-red-500">{{ $wilayaName }}</span>
            </h1>
            <p class="mt-3 text-gray-300 text-lg">
                {{ $totalVehicles }} véhicule{{ $totalVehicles > 1 ? 's' : '' }} disponible{{ $totalVehicles > 1 ? 's' : '' }}
                auprès de {{ $loueurs->count() }} loueur{{ $loueurs->count() > 1 ? 's' : '' }}
            </p>
        </div>
    </section>

    {{-- Loueurs in this wilaya --}}
    @if($loueurs->count() > 0)
    <section class="bg-gray-50 border-b border-gray-200 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Loueurs à {{ $wilayaName }}</p>
            <div class="flex flex-wrap gap-3">
                @foreach($loueurs as $loueur)
                    <a href="{{ route('loueur.show', $loueur->slug) }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-full px-4 py-2 hover:border-red-300 hover:shadow-sm transition text-sm">
                        @if($loueur->logo)
                            <img src="{{ asset('storage/' . $loueur->logo) }}" alt="" class="w-6 h-6 rounded-full object-cover">
                        @endif
                        <span class="font-medium text-gray-700">{{ $loueur->company_name }}</span>
                        <span class="text-gray-400">({{ $loueur->vehicles_count }})</span>
                        @if($loueur->rating > 0)
                            <span class="flex items-center gap-0.5 text-yellow-500">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-xs font-medium">{{ number_format($loueur->rating, 1) }}</span>
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Vehicles --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if($vehicles->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($vehicles as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>
            <div class="mt-10">{{ $vehicles->links() }}</div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500 text-lg">Aucun véhicule disponible à {{ $wilayaName }} pour le moment.</p>
                <a href="{{ route('vehicles.index') }}" class="mt-4 inline-block text-red-600 font-semibold hover:underline">Voir tous les véhicules</a>
            </div>
        @endif
    </section>

    {{-- SEO Content --}}
    <section class="bg-white border-t border-gray-100 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Location de voiture à {{ $wilayaName }}</h2>
            <div class="prose prose-gray text-gray-600">
                <p>Trouvez les meilleures offres de location de voitures à {{ $wilayaName }} sur ResaDZ. Notre plateforme vous permet de comparer les offres de loueurs vérifiés et de réserver en toute confiance.</p>
                <p>Que vous ayez besoin d'une voiture pour un déplacement professionnel, des vacances ou un événement, nos partenaires à {{ $wilayaName }} proposent des véhicules pour tous les budgets : berlines, SUV, utilitaires et plus encore.</p>
            </div>
        </div>
    </section>

@endsection

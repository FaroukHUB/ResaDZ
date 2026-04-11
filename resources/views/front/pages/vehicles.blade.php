@extends('front.layouts.app')

@section('title', 'Location de voitures en Algérie — Tous les véhicules disponibles | ResaDZ')
@section('meta_description', 'Trouvez et louez une voiture en Algérie parmi notre sélection de véhicules vérifiés. Filtrez par ville, marque, prix et réservez en ligne. Paiement sécurisé par CB.')

@section('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .resadz-marker { background: none !important; border: none !important; }
    .leaflet-popup-content-wrapper { border-radius: 12px !important; }
</style>
@endsection

@section('content')

    <!-- Header -->
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900">Véhicules disponibles</h1>
            <p class="mt-2 text-gray-500">
                {{ $vehicles->total() }} véhicule{{ $vehicles->total() > 1 ? 's' : '' }} trouvé{{ $vehicles->total() > 1 ? 's' : '' }}
                @if($pickupDate && $returnDate)
                    <span class="text-red-600 font-medium">du {{ $pickupDate->format('d/m/Y') }} au {{ $returnDate->format('d/m/Y') }}</span>
                @endif
                @if(request('wilaya'))
                    <span class="text-gray-600">à {{ request('wilaya') }}</span>
                @endif
            </p>
        </div>
    </section>

    <!-- Carte interactive -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8" x-data="{ showMap: false }">
        <button @click="showMap = !showMap"
                class="flex items-center gap-2 text-sm font-semibold text-gray-700 hover:text-amber-600 transition mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            <span x-text="showMap ? 'Masquer la carte' : 'Voir la carte des véhicules par wilaya'"></span>
            <svg class="w-4 h-4 transition-transform" :class="showMap && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="showMap" x-collapse x-cloak>
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-6">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-gray-900">Carte des véhicules par wilaya</h2>
                        <p class="text-sm text-gray-500 mt-0.5">
                            <span id="map-total-vehicles" class="font-semibold text-amber-600">...</span> véhicules dans
                            <span id="map-total-wilayas" class="font-semibold text-amber-600">...</span> wilayas
                        </p>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-500">
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span> &lt;5</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-600 inline-block"></span> 5-9</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-orange-600 inline-block"></span> 10-19</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-600 inline-block"></span> 20+</span>
                    </div>
                </div>
                <div id="resadz-map" style="height: 450px; width: 100%;"></div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- Filters Sidebar -->
            <aside class="w-full lg:w-72 shrink-0">
                <form action="{{ route('vehicles.index') }}" method="GET" class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5 sticky top-24">
                    <h3 class="font-bold text-gray-900 text-lg">Filtres</h3>

                    {{-- Dates de réservation --}}
                    <div class="p-4 bg-gray-50 rounded-xl space-y-3">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Disponibilité</div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">Date de départ</label>
                            <input type="date" name="pickup_date" value="{{ request('pickup_date', $pickupDate?->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">Date de retour</label>
                            <input type="date" name="return_date" value="{{ request('return_date', $returnDate?->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    {{-- Wilaya --}}
                    @if(isset($wilayas) && $wilayas->count() > 0)
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1">Wilaya</label>
                        <select name="wilaya" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">Toutes</option>
                            @foreach($wilayas as $wilaya)
                                <option value="{{ $wilaya }}" {{ request('wilaya') == $wilaya ? 'selected' : '' }}>{{ $wilaya }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1">Marque</label>
                        <select name="brand" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">Toutes</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1">Catégorie</label>
                        <select name="category" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">Toutes</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1">Transmission</label>
                        <select name="transmission" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">Toutes</option>
                            <option value="automatic" {{ request('transmission') == 'automatic' ? 'selected' : '' }}>Automatique</option>
                            <option value="manual" {{ request('transmission') == 'manual' ? 'selected' : '' }}>Manuelle</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-1">Carburant</label>
                        <select name="fuel" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">Tous</option>
                            <option value="diesel" {{ request('fuel') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="essence" {{ request('fuel') == 'essence' ? 'selected' : '' }}>Essence</option>
                            <option value="hybrid" {{ request('fuel') == 'hybrid' ? 'selected' : '' }}>Hybride</option>
                            <option value="electric" {{ request('fuel') == 'electric' ? 'selected' : '' }}>Électrique</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">Prix min</label>
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="DA" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 block mb-1">Prix max</label>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="DA" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition">
                        Rechercher
                    </button>

                    @if(request()->hasAny(['brand', 'category', 'transmission', 'fuel', 'min_price', 'max_price', 'wilaya', 'pickup_date', 'return_date']))
                        <a href="{{ route('vehicles.index') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700">
                            Réinitialiser les filtres
                        </a>
                    @endif
                </form>
            </aside>

            <!-- Vehicle Grid -->
            <div class="flex-1">
                <!-- Sort -->
                <div class="flex items-center justify-end mb-6">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Trier par :</span>
                        <a href="{{ route('vehicles.index', array_merge(request()->query(), ['sort' => 'recent'])) }}" class="text-sm px-3 py-1 rounded-lg {{ request('sort', 'recent') === 'recent' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">Récents</a>
                        <a href="{{ route('vehicles.index', array_merge(request()->query(), ['sort' => 'price_asc'])) }}" class="text-sm px-3 py-1 rounded-lg {{ request('sort') === 'price_asc' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">Prix &uarr;</a>
                        <a href="{{ route('vehicles.index', array_merge(request()->query(), ['sort' => 'price_desc'])) }}" class="text-sm px-3 py-1 rounded-lg {{ request('sort') === 'price_desc' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">Prix &darr;</a>
                    </div>
                </div>

                @if($vehicles->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($vehicles as $vehicle)
                            @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $vehicles->links() }}
                    </div>
                @else
                    <div class="text-center py-20">
                        <svg class="w-16 h-16 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Aucun véhicule trouvé</h3>
                        <p class="mt-2 text-gray-500">Essayez de modifier vos filtres.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/map-vehicles.js') }}"></script>

@endsection

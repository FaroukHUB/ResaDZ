@extends('front.layouts.app')

@section('title', 'ResaDZ - Location de voitures en Algérie')
@section('meta_description', 'Marketplace de location de voitures en Algérie. Comparez et réservez auprès de loueurs vérifiés partout en Algérie.')

@section('content')

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight">
                    Louez votre voiture
                    <span class="block text-amber-500">partout en Algérie</span>
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-gray-300 max-w-2xl mx-auto">
                    Comparez les offres de loueurs vérifiés et réservez en quelques clics. Le meilleur de la location auto en DZ.
                </p>

                <!-- Stats -->
                <div class="mt-10 flex justify-center gap-12">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-amber-500">{{ $totalVehicles }}+</div>
                        <div class="text-sm text-gray-400 mt-1">Véhicules</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-amber-500">{{ $totalLoueurs }}+</div>
                        <div class="text-sm text-gray-400 mt-1">Loueurs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-amber-500">48</div>
                        <div class="text-sm text-gray-400 mt-1">Wilayas</div>
                    </div>
                </div>

                <!-- Search Bar -->
                <form action="{{ route('vehicles.index') }}" method="GET" class="mt-10 max-w-3xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-xl p-3 flex flex-col sm:flex-row gap-3">
                        <select name="brand" class="flex-1 px-4 py-3 rounded-xl bg-gray-50 border-0 text-gray-700 focus:ring-2 focus:ring-amber-500">
                            <option value="">Toutes les marques</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        <select name="category" class="flex-1 px-4 py-3 rounded-xl bg-gray-50 border-0 text-gray-700 focus:ring-2 focus:ring-amber-500">
                            <option value="">Toutes catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-8 py-3 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition shadow-lg shadow-amber-600/30">
                            Rechercher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="how-it-works" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-gray-900">Comment ça marche</h2>
                <p class="mt-3 text-gray-500">En 3 étapes simples</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-8">
                    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">1. Recherchez</h3>
                    <p class="text-gray-500">Parcourez les véhicules disponibles et comparez les offres des loueurs.</p>
                </div>
                <div class="text-center p-8">
                    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">2. Réservez</h3>
                    <p class="text-gray-500">Choisissez vos dates et envoyez votre demande de réservation.</p>
                </div>
                <div class="text-center p-8">
                    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">3. Roulez</h3>
                    <p class="text-gray-500">Récupérez le véhicule et profitez de votre trajet en toute sérénité.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Vehicles -->
    @if($featuredVehicles->count() > 0)
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Véhicules disponibles</h2>
                    <p class="mt-2 text-gray-500">Les meilleures offres du moment</p>
                </div>
                <a href="{{ route('vehicles.index') }}" class="text-amber-600 font-semibold hover:text-amber-700 transition">
                    Voir tout &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredVehicles as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Loueurs Section -->
    @if($loueurs->count() > 0)
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-900">Nos loueurs partenaires</h2>
                <p class="mt-2 text-gray-500">Des professionnels vérifiés à votre service</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($loueurs as $loueur)
                    <a href="{{ route('loueur.show', $loueur->slug) }}" class="group bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-lg hover:border-amber-200 transition-all">
                        <div class="flex items-center gap-4">
                            @if($loueur->logo)
                                <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-14 h-14 rounded-xl object-cover">
                            @else
                                <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">{{ strtoupper(substr($loueur->company_name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-bold text-gray-900 group-hover:text-amber-600 transition">{{ $loueur->company_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $loueur->city ?? $loueur->wilaya ?? 'Algérie' }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-sm text-gray-500">{{ $loueur->vehicles_count }} véhicule{{ $loueur->vehicles_count > 1 ? 's' : '' }}</span>
                            @if($loueur->is_verified)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-full">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Vérifié
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA -->
    <section class="py-20 bg-gradient-to-br from-amber-600 to-orange-600">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white">Vous êtes loueur de voitures ?</h2>
            <p class="mt-4 text-amber-100 text-lg">Rejoignez ResaDZ et développez votre activité en ligne. Gérez vos véhicules, réservations et finances depuis un seul tableau de bord.</p>
            <a href="/loueur" class="mt-8 inline-flex items-center px-8 py-4 bg-white text-amber-700 font-bold rounded-xl hover:bg-gray-50 transition shadow-xl">
                Devenir partenaire
            </a>
        </div>
    </section>

@endsection

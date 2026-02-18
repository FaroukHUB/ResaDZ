@extends('front.layouts.app')

@section('title', $loueur->company_name . ' - Location de voitures | ResaDZ')
@section('meta_description', $loueur->meta_description ?? $loueur->company_name . ' - Location de voitures à ' . ($loueur->city ?? 'en Algérie') . '. Réservez en ligne sur ResaDZ.')

@section('meta_extra')
@php
$schemaData = [
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => $loueur->company_name,
    'description' => $loueur->description ?? 'Location de voitures',
    'address' => [
        '@type' => 'PostalAddress',
        'addressLocality' => $loueur->city ?? '',
        'addressRegion' => $loueur->wilaya ?? '',
        'addressCountry' => 'DZ',
    ],
    'url' => url()->current(),
];
if ($loueur->phone) {
    $schemaData['telephone'] = $loueur->phone;
}
@endphp
<script type="application/ld+json">{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
@endsection

@section('content')

    <!-- Hero -->
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col sm:flex-row items-start gap-6">
                @if($loueur->logo)
                    <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-20 h-20 rounded-2xl object-cover shadow-sm border border-gray-200">
                @else
                    <div class="w-20 h-20 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-sm">
                        <span class="text-white font-bold text-3xl">{{ strtoupper(substr($loueur->company_name, 0, 1)) }}</span>
                    </div>
                @endif

                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $loueur->company_name }}</h1>
                        @if($loueur->is_verified)
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-green-700 bg-green-50 px-3 py-1 rounded-full">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Vérifié
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-500 mt-1">{{ $loueur->city ?? '' }} {{ $loueur->wilaya ? '- ' . $loueur->wilaya : '' }}</p>
                    @if($loueur->description)
                        <p class="text-gray-600 mt-3 max-w-2xl">{{ $loueur->description }}</p>
                    @endif

                    <!-- Contact -->
                    <div class="flex flex-wrap gap-3 mt-4">
                        @if($loueur->phone)
                            <a href="tel:{{ $loueur->phone }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white font-semibold rounded-lg hover:bg-amber-700 transition text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $loueur->phone }}
                            </a>
                        @endif
                        @if($loueur->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $loueur->whatsapp) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition text-sm">
                                WhatsApp
                            </a>
                        @endif
                        @if($loueur->facebook)
                            <a href="{{ $loueur->facebook }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-sm">
                                Facebook
                            </a>
                        @endif
                        @if($loueur->instagram)
                            <a href="https://instagram.com/{{ ltrim($loueur->instagram, '@') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-pink-600 text-white font-semibold rounded-lg hover:bg-pink-700 transition text-sm">
                                Instagram
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vehicles -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Véhicules disponibles
            <span class="text-gray-400 font-normal text-lg">({{ $vehicles->total() }})</span>
        </h2>

        @if($vehicles->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($vehicles as $vehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>

            <div class="mt-10">
                {{ $vehicles->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-500">Aucun véhicule disponible pour le moment.</p>
            </div>
        @endif
    </section>

@endsection

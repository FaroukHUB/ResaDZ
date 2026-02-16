@extends('front.layouts.app')

@section('title', $vehicle->full_name . ' - Location ResaDZ')
@section('meta_description', 'Louez ' . $vehicle->full_name . ' à partir de ' . number_format($vehicle->price_per_day, 0, ',', ' ') . ' DA/jour chez ' . ($vehicle->loueur->company_name ?? 'ResaDZ'))
@section('og_title', $vehicle->full_name . ' - ' . number_format($vehicle->price_per_day, 0, ',', ' ') . ' DA/jour')
@section('og_image', $vehicle->image ? asset('storage/' . $vehicle->image) : '')

@section('meta_extra')
<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Car",
    "name": "{{ $vehicle->full_name }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ $vehicle->brand->name ?? '' }}"
    },
    "fuelType": "{{ $vehicle->fuel_type }}",
    "numberOfDoors": {{ $vehicle->doors ?? 5 }},
    "vehicleTransmission": "{{ $vehicle->transmission }}",
    "seatingCapacity": {{ $vehicle->seats ?? 5 }},
    "offers": {
        "@type": "Offer",
        "price": "{{ $vehicle->price_per_day }}",
        "priceCurrency": "DZD",
        "availability": "https://schema.org/InStock"
    }
}
</script>
@endsection

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-amber-600">Accueil</a>
            <span>/</span>
            <a href="{{ route('vehicles.index') }}" class="hover:text-amber-600">Véhicules</a>
            <span>/</span>
            <span class="text-gray-900">{{ $vehicle->full_name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left: Images + Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Image -->
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-200">
                    <div class="aspect-[16/10] bg-gray-100">
                        @if($vehicle->image)
                            <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->full_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H21M3.375 14.25h4.875c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Gallery -->
                @if($vehicle->gallery && count($vehicle->gallery) > 0)
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($vehicle->gallery as $photo)
                            <div class="aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                                <img src="{{ asset('storage/' . $photo) }}" alt="{{ $vehicle->full_name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300 cursor-pointer">
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Specs -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Caractéristiques</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Transmission</div>
                            <div class="font-bold text-gray-900 mt-1">{{ $vehicle->transmission === 'automatic' ? 'Automatique' : 'Manuelle' }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Carburant</div>
                            <div class="font-bold text-gray-900 mt-1">{{ ucfirst($vehicle->fuel_type) }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Places</div>
                            <div class="font-bold text-gray-900 mt-1">{{ $vehicle->seats }}</div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Portes</div>
                            <div class="font-bold text-gray-900 mt-1">{{ $vehicle->doors ?? 5 }}</div>
                        </div>
                        @if($vehicle->year)
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Année</div>
                            <div class="font-bold text-gray-900 mt-1">{{ $vehicle->year }}</div>
                        </div>
                        @endif
                        @if($vehicle->color)
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-sm text-gray-500">Couleur</div>
                            <div class="font-bold text-gray-900 mt-1">{{ $vehicle->color }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Loueur Info -->
                @if($vehicle->loueur)
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Loueur</h2>
                    <a href="{{ route('loueur.show', $vehicle->loueur->slug) }}" class="flex items-center gap-4 group">
                        @if($vehicle->loueur->logo)
                            <img src="{{ asset('storage/' . $vehicle->loueur->logo) }}" alt="{{ $vehicle->loueur->company_name }}" class="w-14 h-14 rounded-xl object-cover">
                        @else
                            <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                                <span class="text-white font-bold text-lg">{{ strtoupper(substr($vehicle->loueur->company_name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-bold text-gray-900 group-hover:text-amber-600 transition">{{ $vehicle->loueur->company_name }}</h3>
                            <p class="text-sm text-gray-500">{{ $vehicle->loueur->city ?? '' }} {{ $vehicle->loueur->wilaya ? '- ' . $vehicle->loueur->wilaya : '' }}</p>
                            @if($vehicle->loueur->is_verified)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 mt-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Loueur vérifié
                                </span>
                            @endif
                        </div>
                    </a>
                </div>
                @endif
            </div>

            <!-- Right: Booking Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 sticky top-24 space-y-5">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $vehicle->full_name }}</h1>
                        @if($vehicle->brand)
                            <p class="text-gray-500">{{ $vehicle->brand->name }} {{ $vehicle->category ? '- ' . $vehicle->category->name : '' }}</p>
                        @endif
                    </div>

                    <!-- Price -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-5">
                        <div class="text-sm text-amber-700 font-medium">Prix par jour</div>
                        <div class="mt-1">
                            <span class="text-4xl font-black text-amber-600">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }}</span>
                            <span class="text-lg text-amber-700 ml-1">DA</span>
                        </div>
                        @if($vehicle->price_per_day_eur)
                            <div class="text-sm text-amber-600 mt-1">~ {{ number_format($vehicle->price_per_day_eur, 0) }} EUR</div>
                        @endif
                    </div>

                    <!-- Contact Loueur -->
                    @if($vehicle->loueur)
                        <div class="space-y-3">
                            @if($vehicle->loueur->phone)
                                <a href="tel:{{ $vehicle->loueur->phone }}" class="flex items-center justify-center gap-2 w-full py-3 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition shadow-lg shadow-amber-600/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    Appeler
                                </a>
                            @endif
                            @if($vehicle->loueur->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $vehicle->loueur->whatsapp) }}?text={{ urlencode('Bonjour, je suis intéressé par ' . $vehicle->full_name . ' sur ResaDZ.') }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                    WhatsApp
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Payment Methods -->
                    @if($vehicle->loueur && $vehicle->loueur->payment_methods)
                        <div>
                            <div class="text-sm font-medium text-gray-700 mb-2">Paiements acceptés</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($vehicle->loueur->payment_methods as $method)
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">
                                        {{ match($method) {
                                            'cash' => 'Espèces',
                                            'cib' => 'CIB',
                                            'dahabia' => 'Dahabia',
                                            'baridimob' => 'BaridiMob',
                                            'paypal' => 'PayPal',
                                            'bank_transfer' => 'Virement',
                                            'wise' => 'Wise',
                                            default => $method
                                        } }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Vehicles -->
        @if($relatedVehicles->count() > 0)
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Véhicules similaires</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedVehicles as $relVehicle)
                    @include('front.components.vehicle-card', ['vehicle' => $relVehicle])
                @endforeach
            </div>
        </div>
        @endif
    </div>

@endsection

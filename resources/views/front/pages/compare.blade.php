@extends('front.layouts.app')

@section('title', 'Comparer les véhicules - ResaDZ')

@section('content')

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Comparer les véhicules</h1>
            <p class="text-gray-500 mt-1">{{ $vehicles->count() }} véhicules sélectionnés</p>
        </div>

        @if($vehicles->count() >= 2)
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                {{-- Photos --}}
                <tr>
                    <td class="p-3 bg-gray-50 font-semibold text-sm text-gray-500 w-40"></td>
                    @foreach($vehicles as $vehicle)
                        <td class="p-3 text-center">
                            <a href="{{ route('vehicles.show', $vehicle->slug) }}">
                                @if($vehicle->image)
                                    <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->full_name }}" class="w-full h-40 object-cover rounded-xl">
                                @else
                                    <div class="w-full h-40 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6"/></svg>
                                    </div>
                                @endif
                            </a>
                        </td>
                    @endforeach
                </tr>

                {{-- Name --}}
                <tr class="border-t border-gray-100">
                    <td class="p-3 bg-gray-50 font-semibold text-sm text-gray-500">Véhicule</td>
                    @foreach($vehicles as $vehicle)
                        <td class="p-3 text-center">
                            <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="font-bold text-gray-900 hover:text-red-600">
                                {{ $vehicle->full_name }}
                            </a>
                        </td>
                    @endforeach
                </tr>

                {{-- Loueur --}}
                <tr class="border-t border-gray-100">
                    <td class="p-3 bg-gray-50 font-semibold text-sm text-gray-500">Loueur</td>
                    @foreach($vehicles as $vehicle)
                        <td class="p-3 text-center text-sm text-gray-600">
                            @if($vehicle->loueur)
                                <a href="{{ route('loueur.show', $vehicle->loueur->slug) }}" class="hover:text-red-600">{{ $vehicle->loueur->company_name }}</a>
                                @if($vehicle->loueur->rating > 0)
                                    <span class="text-yellow-500 ml-1">{{ number_format($vehicle->loueur->rating, 1) }}/5</span>
                                @endif
                            @endif
                        </td>
                    @endforeach
                </tr>

                {{-- Price --}}
                <tr class="border-t border-gray-100 bg-red-50">
                    <td class="p-3 font-semibold text-sm text-gray-700">Prix / jour</td>
                    @foreach($vehicles as $vehicle)
                        <td class="p-3 text-center">
                            <span class="text-xl font-black text-gray-900">{{ number_format($vehicle->price_per_day, 0, ',', ' ') }} DA</span>
                            @if($vehicle->price_per_day_eur)
                                <span class="block text-sm text-gray-500">{{ number_format($vehicle->price_per_day_eur, 0) }} &euro;</span>
                            @endif
                        </td>
                    @endforeach
                </tr>

                @php
                    $specs = [
                        ['label' => 'Année', 'field' => 'year'],
                        ['label' => 'Catégorie', 'field' => 'category', 'relation' => true],
                        ['label' => 'Boîte', 'field' => 'transmission', 'format' => fn($v) => $v === 'automatic' ? 'Automatique' : 'Manuelle'],
                        ['label' => 'Carburant', 'field' => 'fuel_type', 'format' => fn($v) => ucfirst($v)],
                        ['label' => 'Places', 'field' => 'seats', 'suffix' => ' places'],
                        ['label' => 'Portes', 'field' => 'doors'],
                        ['label' => 'Km/jour', 'field' => 'mileage_limit_per_day', 'format' => fn($v) => $v ? $v . ' km' : 'Illimité'],
                        ['label' => 'Caution', 'field' => 'deposit_amount', 'format' => fn($v) => $v ? number_format($v, 0, ',', ' ') . ' DA' : '-'],
                    ];
                @endphp

                @foreach($specs as $spec)
                    <tr class="border-t border-gray-100">
                        <td class="p-3 bg-gray-50 font-semibold text-sm text-gray-500">{{ $spec['label'] }}</td>
                        @foreach($vehicles as $vehicle)
                            <td class="p-3 text-center text-sm text-gray-700">
                                @if(isset($spec['relation']) && $spec['relation'])
                                    {{ $vehicle->{$spec['field']}->name ?? '-' }}
                                @elseif(isset($spec['format']))
                                    {{ ($spec['format'])($vehicle->{$spec['field']}) }}
                                @else
                                    {{ $vehicle->{$spec['field']} ?? '-' }}{{ isset($spec['suffix']) && $vehicle->{$spec['field']} ? $spec['suffix'] : '' }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach

                {{-- Badges --}}
                <tr class="border-t border-gray-100">
                    <td class="p-3 bg-gray-50 font-semibold text-sm text-gray-500">Services</td>
                    @foreach($vehicles as $vehicle)
                        <td class="p-3 text-center">
                            @forelse($vehicle->getBadges() as $badge)
                                <span class="inline-flex items-center gap-1 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full mb-1">
                                    {{ $badge['text'] }}
                                </span>
                            @empty
                                <span class="text-gray-400">-</span>
                            @endforelse
                        </td>
                    @endforeach
                </tr>

                {{-- Actions --}}
                <tr class="border-t border-gray-200">
                    <td class="p-3 bg-gray-50"></td>
                    @foreach($vehicles as $vehicle)
                        <td class="p-4 text-center">
                            <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition w-full">
                                Réserver
                            </a>
                        </td>
                    @endforeach
                </tr>
            </table>
        </div>
        @else
            <p class="text-gray-500">Sélectionnez au moins 2 véhicules pour comparer.</p>
        @endif
    </section>

@endsection

@extends('front.layouts.app')

@section('title', 'Transferts & Taxi - ResaDZ')
@section('meta_description', 'Service de transfert et taxi en Algérie. Aéroport, gare, hôtel... Réservez votre chauffeur privé avec ResaDZ.')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-neutral-900 to-neutral-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-black text-white mb-2">Service de transfert</h1>
            <p class="text-white/60">Trouvez un chauffeur pour tous vos déplacements en Algérie</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- Left: Search Filters --}}
            <div class="lg:col-span-1">
                <form action="{{ route('transfers.search') }}" method="GET" class="bg-white rounded-2xl border border-gray-200 p-6 sticky top-24 space-y-4">
                    <h3 class="font-bold text-gray-900 text-lg mb-4">Rechercher</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lieu de départ</label>
                        <input type="text" name="departure" value="{{ $departure }}" placeholder="Ex: Aéroport Alger"
                               class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
                        <input type="text" name="destination" value="{{ $destination }}" placeholder="Ex: Oran centre"
                               class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="date" value="{{ $date }}" min="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Heure</label>
                            <select name="time" class="w-full px-3 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                @for($h = 0; $h <= 23; $h++)
                                    <option value="{{ sprintf('%02d:00', $h) }}" {{ $time == sprintf('%02d:00', $h) ? 'selected' : '' }}>{{ sprintf('%02d:00', $h) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Passagers</label>
                        <select name="passengers" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ $passengers == $i ? 'selected' : '' }}>{{ $i }} {{ $i > 1 ? 'passagers' : 'passager' }}</option>
                            @endfor
                        </select>
                    </div>

                    <button type="submit" class="w-full py-3 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition">
                        Rechercher
                    </button>
                </form>
            </div>

            {{-- Right: Results --}}
            <div class="lg:col-span-3 space-y-6">

                @if(count($results) === 0)
                    <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Aucun transfert disponible</h3>
                        <p class="text-gray-500">Essayez de modifier vos critères de recherche ou contactez-nous pour un transfert personnalisé.</p>
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ count($results) }} prestataire(s) disponible(s)</p>

                    @foreach($results as $result)
                        @php $loueur = $result['loueur']; @endphp
                        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                            {{-- Loueur Header --}}
                            <div class="p-6 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        @if($loueur->logo)
                                            <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-14 h-14 rounded-xl object-cover">
                                        @else
                                            <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center">
                                                <span class="text-amber-600 font-black text-lg">{{ substr($loueur->company_name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-bold text-gray-900 text-lg">{{ $loueur->company_name }}</h3>
                                            <div class="flex items-center gap-3 text-sm text-gray-500">
                                                @if($result['rating'] > 0)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        {{ number_format($result['rating'], 1) }}
                                                    </span>
                                                @endif
                                                <span>{{ $loueur->city ?? $loueur->wilaya }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($result['is_24h'])
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">24h/24</span>
                                        @endif
                                        @if($result['luggage_included'])
                                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Bagages inclus</span>
                                        @endif
                                    </div>
                                </div>

                                @if(!empty($result['description']))
                                    <p class="text-sm text-gray-500 mt-3">{{ Str::limit($result['description'], 150) }}</p>
                                @endif
                            </div>

                            {{-- Routes --}}
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach($result['routes'] as $route)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-amber-50 transition group">
                                            <div class="flex items-center gap-4">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                                                    <div class="w-px h-6 bg-gray-300"></div>
                                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $route['from'] ?? '-' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $route['to'] ?? '-' }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <div class="text-right">
                                                    @if(isset($route['on_request']) && $route['on_request'])
                                                        <div class="text-amber-600 font-bold">Sur devis</div>
                                                    @elseif(($route['price'] ?? 0) > 0)
                                                        <div class="text-lg font-black text-amber-600">{{ number_format($route['price'], 0, ',', ' ') }} DA</div>
                                                    @endif
                                                    <div class="text-xs text-gray-400">
                                                        {{ ucfirst($route['vehicle_type'] ?? 'berline') }}
                                                        · {{ $route['max_passengers'] ?? 4 }} places
                                                    </div>
                                                </div>
                                                <button type="button"
                                                        onclick="openBookingModal({{ $loueur->id }}, '{{ addslashes($route['from'] ?? '') }}', '{{ addslashes($route['to'] ?? '') }}', {{ $route['price'] ?? 0 }}, '{{ $route['vehicle_type'] ?? 'berline' }}')"
                                                        class="px-5 py-2.5 bg-amber-600 text-white font-semibold rounded-xl hover:bg-amber-700 transition text-sm">
                                                    Réserver
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Booking Modal --}}
<div id="bookingModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Réserver un transfert</h3>
            <button onclick="closeBookingModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Trajet recap --}}
        <div class="flex items-center gap-3 p-4 bg-amber-50 rounded-xl mb-6">
            <div class="flex flex-col items-center">
                <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                <div class="w-px h-4 bg-amber-300"></div>
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            </div>
            <div>
                <div id="modalFrom" class="text-sm font-medium text-gray-900"></div>
                <div id="modalTo" class="text-sm text-gray-500"></div>
            </div>
            <div class="ml-auto">
                <div id="modalPrice" class="text-lg font-black text-amber-600"></div>
            </div>
        </div>

        <form action="{{ route('transfers.book') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="loueur_id" id="modalLoueurId">
            <input type="hidden" name="departure" id="modalDeparture">
            <input type="hidden" name="destination" id="modalDestination">
            <input type="hidden" name="price" id="modalPriceInput">
            <input type="hidden" name="vehicle_type" id="modalVehicleType">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="transfer_date" required min="{{ date('Y-m-d') }}" value="{{ $date }}"
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Heure *</label>
                    <select name="transfer_time" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                        @for($h = 0; $h <= 23; $h++)
                            <option value="{{ sprintf('%02d:00', $h) }}" {{ ($time ?: '10:00') == sprintf('%02d:00', $h) ? 'selected' : '' }}>{{ sprintf('%02d:00', $h) }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Passagers *</label>
                    <select name="passengers" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ $passengers == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bagages *</label>
                    <select name="luggage_count" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                        @for($i = 0; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i > 1 ? 'bagages' : 'bagage' }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet *</label>
                <input type="text" name="client_name" required value="{{ auth()->user()->name ?? '' }}"
                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                    <input type="tel" name="client_phone" required
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                           placeholder="0X XX XX XX XX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="client_email" required value="{{ auth()->user()->email ?? '' }}"
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Remarque (optionnel)</label>
                <textarea name="client_notes" rows="2"
                          class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                          placeholder="Ex: J'ai 2 grosses valises, numéro de vol..."></textarea>
            </div>

            <button type="submit" class="w-full py-4 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition shadow-lg shadow-amber-600/20">
                Confirmer la réservation
            </button>

            <p class="text-xs text-gray-400 text-center">Service gratuit - Le chauffeur vous contactera pour confirmer</p>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openBookingModal(loueurId, from, to, price, vehicleType) {
        document.getElementById('modalLoueurId').value = loueurId;
        document.getElementById('modalDeparture').value = from;
        document.getElementById('modalDestination').value = to;
        document.getElementById('modalPriceInput').value = price;
        document.getElementById('modalVehicleType').value = vehicleType;

        document.getElementById('modalFrom').textContent = from;
        document.getElementById('modalTo').textContent = to;
        document.getElementById('modalPrice').textContent = price > 0
            ? new Intl.NumberFormat('fr-DZ').format(price) + ' DA'
            : 'Sur devis';

        document.getElementById('bookingModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeBookingModal() {
        document.getElementById('bookingModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Fermer en cliquant en dehors
    document.getElementById('bookingModal').addEventListener('click', function(e) {
        if (e.target === this) closeBookingModal();
    });
</script>
@endsection

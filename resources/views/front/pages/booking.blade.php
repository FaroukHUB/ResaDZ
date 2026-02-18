@extends('front.layouts.app')

@section('title', 'Réserver ' . $vehicle->full_name . ' - ResaDZ')

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-amber-600">Accueil</a>
            <span>/</span>
            <a href="{{ route('vehicles.show', $vehicle->slug) }}" class="hover:text-amber-600">{{ $vehicle->full_name }}</a>
            <span>/</span>
            <span class="text-gray-900">Réserver</span>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900 mb-8">Réserver {{ $vehicle->full_name }}</h1>

        <form action="{{ route('booking.store') }}" method="POST" id="bookingForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left: Form -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Dates & Heures -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Dates et heures de location</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de début *</label>
                                <input type="date" name="start_date" id="start_date" required
                                       min="{{ date('Y-m-d') }}"
                                       value="{{ old('start_date') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                @error('start_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Heure de prise en charge *</label>
                                <select name="pickup_time" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Choisir une heure</option>
                                    @for($h = 7; $h <= 22; $h++)
                                        <option value="{{ sprintf('%02d:00', $h) }}" {{ old('pickup_time') == sprintf('%02d:00', $h) ? 'selected' : '' }}>{{ sprintf('%02d:00', $h) }}</option>
                                        @if($h < 22)
                                            <option value="{{ sprintf('%02d:30', $h) }}" {{ old('pickup_time') == sprintf('%02d:30', $h) ? 'selected' : '' }}>{{ sprintf('%02d:30', $h) }}</option>
                                        @endif
                                    @endfor
                                </select>
                                @error('pickup_time') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin *</label>
                                <input type="date" name="end_date" id="end_date" required
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       value="{{ old('end_date') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                @error('end_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Heure de retour *</label>
                                <select name="return_time" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Choisir une heure</option>
                                    @for($h = 7; $h <= 22; $h++)
                                        <option value="{{ sprintf('%02d:00', $h) }}" {{ old('return_time') == sprintf('%02d:00', $h) ? 'selected' : '' }}>{{ sprintf('%02d:00', $h) }}</option>
                                        @if($h < 22)
                                            <option value="{{ sprintf('%02d:30', $h) }}" {{ old('return_time') == sprintf('%02d:30', $h) ? 'selected' : '' }}>{{ sprintf('%02d:30', $h) }}</option>
                                        @endif
                                    @endfor
                                </select>
                                @error('return_time') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        @if($vehicle->min_rental_days > 1)
                            <p class="text-sm text-amber-600 mt-2">Durée minimum : {{ $vehicle->min_rental_days }} jours</p>
                        @endif
                        @if($vehicle->max_rental_days)
                            <p class="text-sm text-amber-600 mt-1">Durée maximum : {{ $vehicle->max_rental_days }} jours</p>
                        @endif
                    </div>

                    <!-- Client Info -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Vos informations</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet *</label>
                                <input type="text" name="client_name" required
                                       value="{{ old('client_name', auth()->user()->name ?? '') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="Votre nom et prénom">
                                @error('client_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                                <input type="tel" name="client_phone" required
                                       value="{{ old('client_phone') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="0X XX XX XX XX">
                                @error('client_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email (optionnel)</label>
                                <input type="email" name="client_email"
                                       value="{{ old('client_email', auth()->user()->email ?? '') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="votre@email.com">
                            </div>
                        </div>
                    </div>

                    <!-- Lieu de prise en charge & retour -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Lieu de prise en charge & retour</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if($deliveryZones->count() > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Zone de récupération</label>
                                <select name="pickup_zone_id" id="pickup_zone_id"
                                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Sur place (gratuit)</option>
                                    @foreach($deliveryZones->where('delivery_available', true) as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }} {{ $zone->delivery_fee > 0 ? '(+' . $zone->getFormattedDeliveryFee() . ')' : '(Gratuit)' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Zone de retour</label>
                                <select name="return_zone_id" id="return_zone_id"
                                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Sur place (gratuit)</option>
                                    @foreach($deliveryZones->where('return_available', true) as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }} {{ $zone->return_fee > 0 ? '(+' . number_format($zone->return_fee, 0, ',', ' ') . ' ' . ($zone->currency === 'EUR' ? '€' : 'DA') . ')' : '(Gratuit)' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse de récupération *</label>
                                <input type="text" name="pickup_address" required
                                       value="{{ old('pickup_address') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="Ex: Aéroport Houari Boumediene, Alger centre...">
                                @error('pickup_address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse de retour *</label>
                                <input type="text" name="return_address" required
                                       value="{{ old('return_address') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="Ex: Même adresse, autre adresse...">
                                @error('return_address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    @if(count($availableOptions) > 0)
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Options supplémentaires</h2>
                        <div class="space-y-3">
                            @foreach($availableOptions as $option)
                                <label class="flex items-center justify-between p-4 rounded-xl bg-gray-50 hover:bg-amber-50 cursor-pointer transition border border-transparent hover:border-amber-200">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="options[]" value="{{ $option['name'] }}" class="w-5 h-5 text-amber-600 rounded focus:ring-amber-500 option-checkbox">
                                        <span class="font-medium text-gray-900">{{ $option['name'] }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-amber-600">
                                        +{{ number_format($option['price'] ?? 0, 0, ',', ' ') }}
                                        {{ ($option['currency'] ?? 'DZD') === 'EUR' ? '€' : 'DA' }}
                                        /{{ ($option['per'] ?? 'day') === 'day' ? 'jour' : 'location' }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Documents -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Documents</h2>
                        <p class="text-sm text-gray-500 mb-4">Pour accélérer le traitement de votre réservation, envoyez vos documents maintenant.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pièce d'identité (CNI)</label>
                                <input type="file" name="client_id_document" accept="image/*,.pdf"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                                @error('client_id_document') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Permis de conduire (recto)</label>
                                <input type="file" name="client_license_front" accept="image/*,.pdf"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                                @error('client_license_front') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Permis de conduire (verso)</label>
                                <input type="file" name="client_license_back" accept="image/*,.pdf"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                                @error('client_license_back') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Remarques</h2>
                        <textarea name="internal_notes" rows="3"
                                  class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                                  placeholder="Ex: Je souhaite récupérer le véhicule à 9h..."></textarea>
                    </div>
                </div>

                <!-- Right: Price Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 sticky top-24 space-y-4">
                        <!-- Vehicle recap -->
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                            @if($vehicle->image)
                                <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->full_name }}" class="w-16 h-12 rounded-lg object-cover">
                            @endif
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">{{ $vehicle->full_name }}</h3>
                                <p class="text-xs text-gray-500">{{ $vehicle->loueur->company_name ?? '' }}</p>
                            </div>
                        </div>

                        <!-- Price breakdown -->
                        <div id="priceBreakdown" class="space-y-2 text-sm">
                            <p class="text-gray-500 text-center py-4">Sélectionnez vos dates pour voir le prix</p>
                        </div>

                        <!-- Timer info -->
                        @if($timerHours)
                        <div class="bg-amber-50 rounded-xl p-3 text-sm">
                            <p class="text-amber-800 font-medium">Délai de confirmation</p>
                            <p class="text-amber-600 text-xs mt-1">Vous avez {{ $timerHours }}h pour confirmer votre réservation après validation.</p>
                        </div>
                        @endif

                        <!-- Submit -->
                        <button type="submit" id="submitBtn" disabled
                                class="w-full py-4 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition shadow-lg shadow-amber-600/20 disabled:opacity-50 disabled:cursor-not-allowed">
                            Réserver maintenant
                        </button>

                        <p class="text-xs text-gray-400 text-center">Aucun paiement en ligne requis. Le loueur vous contactera pour confirmer.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
<script>
    const vehicleId = {{ $vehicle->id }};
    const calcUrl = '{{ route("booking.calculate") }}';
    const csrfToken = '{{ csrf_token() }}';

    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const pickupZone = document.getElementById('pickup_zone_id');
    const returnZone = document.getElementById('return_zone_id');
    const optionBoxes = document.querySelectorAll('.option-checkbox');
    const priceBreakdown = document.getElementById('priceBreakdown');
    const submitBtn = document.getElementById('submitBtn');

    function recalculate() {
        if (!startDate.value || !endDate.value) return;

        const selectedOptions = [];
        optionBoxes.forEach(cb => { if (cb.checked) selectedOptions.push(cb.value); });

        fetch(calcUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                vehicle_id: vehicleId,
                start_date: startDate.value,
                end_date: endDate.value,
                pickup_zone_id: pickupZone ? pickupZone.value : null,
                return_zone_id: returnZone ? returnZone.value : null,
                options: selectedOptions,
                currency: 'DZD',
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.total !== undefined) {
                let html = '';
                html += `<div class="flex justify-between"><span class="text-gray-600">${data.total_days} jour(s) x ${fmt(data.daily_rate)} ${data.currency_symbol}</span><span class="font-medium">${fmt(data.base_price)} ${data.currency_symbol}</span></div>`;

                if (data.duration_discount > 0) {
                    html += `<div class="flex justify-between text-green-600"><span>Remise durée (-${data.duration_discount_percent}%)</span><span>-${fmt(data.duration_discount)} ${data.currency_symbol}</span></div>`;
                }
                if (data.season_surcharge > 0) {
                    html += `<div class="flex justify-between text-orange-600"><span>${data.season_name || 'Haute saison'}</span><span>+${fmt(data.season_surcharge)} ${data.currency_symbol}</span></div>`;
                }
                if (data.delivery_fee > 0) {
                    html += `<div class="flex justify-between"><span class="text-gray-600">Livraison</span><span>+${fmt(data.delivery_fee)} ${data.currency_symbol}</span></div>`;
                }
                if (data.return_fee > 0) {
                    html += `<div class="flex justify-between"><span class="text-gray-600">Retour</span><span>+${fmt(data.return_fee)} ${data.currency_symbol}</span></div>`;
                }
                if (data.options_total > 0) {
                    html += `<div class="flex justify-between"><span class="text-gray-600">Options</span><span>+${fmt(data.options_total)} ${data.currency_symbol}</span></div>`;
                }

                html += `<div class="border-t border-gray-200 pt-3 mt-3 flex justify-between text-lg"><span class="font-bold text-gray-900">Total</span><span class="font-black text-amber-600">${data.formatted_total}</span></div>`;

                if (data.advance_amount > 0) {
                    html += `<div class="flex justify-between text-sm"><span class="text-gray-500">Acompte (${data.advance_percentage}%)</span><span class="font-semibold">${data.formatted_advance}</span></div>`;
                }
                if (data.deposit_amount > 0) {
                    html += `<div class="flex justify-between text-sm"><span class="text-gray-500">Caution</span><span class="font-semibold">${data.formatted_deposit}</span></div>`;
                }

                priceBreakdown.innerHTML = html;
                submitBtn.disabled = false;
            }
        })
        .catch(() => {});
    }

    function fmt(n) {
        return new Intl.NumberFormat('fr-DZ', { maximumFractionDigits: 0 }).format(n);
    }

    [startDate, endDate].forEach(el => el.addEventListener('change', recalculate));
    if (pickupZone) pickupZone.addEventListener('change', recalculate);
    if (returnZone) returnZone.addEventListener('change', recalculate);
    optionBoxes.forEach(cb => cb.addEventListener('change', recalculate));
</script>
@endsection

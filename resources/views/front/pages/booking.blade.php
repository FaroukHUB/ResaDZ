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

        <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
            @csrf
            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left: Form -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Dates & Heure de prise en charge -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Quand souhaitez-vous louer ?</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de prise en charge *</label>
                                <input type="date" name="start_date" id="start_date" required
                                       min="{{ date('Y-m-d') }}"
                                       value="{{ old('start_date') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                @error('start_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Heure de prise en charge *</label>
                                <select name="pickup_time" id="pickup_time" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Choisir</option>
                                    @for($h = $operatingHoursStart; $h <= $operatingHoursEnd; $h++)
                                        <option value="{{ sprintf('%02d:00', $h) }}" {{ old('pickup_time') == sprintf('%02d:00', $h) ? 'selected' : '' }}>{{ sprintf('%02d:00', $h) }}</option>
                                    @endfor
                                </select>
                                @error('pickup_time') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de retour *</label>
                                <input type="date" name="end_date" id="end_date" required
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       value="{{ old('end_date') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                @error('end_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Heure de retour calculée -->
                        <div id="returnTimeInfo" class="hidden mt-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                            <p class="text-blue-800 text-sm">
                                <span class="font-semibold">Retour prévu :</span>
                                Le véhicule devra être restitué le <span id="returnDateDisplay" class="font-bold"></span> avant <span id="returnTimeDisplay" class="font-bold"></span>
                            </p>
                        </div>

                        @if($vehicle->min_rental_days > 1)
                            <p class="text-sm text-amber-600 mt-3">Durée minimum : {{ $vehicle->min_rental_days }} jours</p>
                        @endif
                    </div>

                    <!-- Client Info -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Vos coordonnées</h2>
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" name="client_email" required
                                       value="{{ old('client_email', auth()->user()->email ?? '') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="votre@email.com">
                                <p class="text-xs text-gray-500 mt-1">Vous recevrez la confirmation par email</p>
                                @error('client_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Lieu de prise en charge & retour -->
                    @if($deliveryZones->where('delivery_available', true)->count() > 0)
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Lieu de prise en charge</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Zone *</label>
                                <select name="pickup_zone_id" id="pickup_zone_id" required
                                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                    @if($deliveryZones->where('delivery_available', true)->count() > 1)
                                        <option value="">Sélectionnez une zone</option>
                                    @endif
                                    @foreach($deliveryZones->where('delivery_available', true) as $zone)
                                        <option value="{{ $zone->id }}" {{ $deliveryZones->where('delivery_available', true)->count() === 1 ? 'selected' : '' }}>
                                            {{ $zone->name }} {{ $zone->delivery_fee > 0 ? '(+' . $zone->getFormattedDeliveryFee() . ')' : '(Gratuit)' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pickup_zone_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Toggle retour différent (seulement si plusieurs zones de retour) --}}
                            @if($deliveryZones->where('return_available', true)->count() > 1)
                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" id="different_return" class="w-5 h-5 text-amber-600 rounded focus:ring-amber-500">
                                <span class="text-sm text-gray-700">Je souhaite rendre le véhicule à un endroit différent</span>
                            </label>

                            {{-- Zone de retour (masqué par défaut) --}}
                            <div id="return_fields" class="hidden pt-4 border-t border-gray-200">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Zone de retour *</label>
                                <select name="return_zone_id" id="return_zone_id"
                                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Sélectionnez une zone</option>
                                    @foreach($deliveryZones->where('return_available', true) as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }} {{ $zone->return_fee > 0 ? '(+' . number_format($zone->return_fee, 0, ',', ' ') . ' DA)' : '(Gratuit)' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            {{-- Champ caché pour retour identique --}}
                            <input type="hidden" name="same_return_location" id="same_return_location" value="1">
                        </div>
                    </div>
                    @endif

                    <!-- Options -->
                    @if(count($rentalOptions) > 0 || $fuelReturnFee > 0 || $washReturnFee > 0)
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Options</h2>
                        @error('options') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror
                        <div class="space-y-3" id="optionsContainer">
                            @foreach($rentalOptions as $option)
                                @php
                                    $isFree = ($option['is_free'] ?? false) || (($option['price'] ?? 0) == 0);
                                    $optionPrice = (float) ($option['price'] ?? 0);
                                    $optionName = $option['name'] ?? '';
                                @endphp
                                <label class="option-label flex items-center justify-between p-4 rounded-xl {{ $isFree ? 'bg-green-50 hover:bg-green-100 border-green-200' : 'bg-gray-50 hover:bg-amber-50 border-transparent hover:border-amber-200' }} cursor-pointer transition border"
                                       data-option-name="{{ $optionName }}">
                                    <div class="flex items-center gap-4">
                                        <input type="checkbox" name="options[]" value="{{ $optionName }}"
                                               data-price="{{ $optionPrice }}"
                                               data-per="{{ $option['per'] ?? 'day' }}"
                                               data-free="{{ $isFree ? '1' : '0' }}"
                                               data-option-name="{{ $optionName }}"
                                               class="w-5 h-5 text-amber-600 rounded focus:ring-amber-500 option-checkbox rental-option">
                                        @if(!empty($option['image']))
                                            <img src="{{ asset('storage/' . $option['image']) }}" alt="{{ $optionName }}" class="w-12 h-12 rounded-lg object-cover">
                                        @endif
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-gray-900 option-name">{{ $optionName }}</span>
                                                @if($isFree)
                                                    <span class="px-2 py-0.5 bg-green-500 text-white text-xs font-bold rounded-full">OFFERT</span>
                                                @endif
                                                <span class="unavailable-badge hidden px-2 py-0.5 bg-red-100 text-red-600 text-xs font-medium rounded-full">Indisponible</span>
                                            </div>
                                            @if(!empty($option['description']))
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $option['description'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if(!$isFree && $optionPrice > 0)
                                        <span class="text-sm font-semibold text-amber-600 whitespace-nowrap">
                                            +{{ number_format($optionPrice, 0, ',', ' ') }} DA
                                            /{{ ($option['per'] ?? 'day') === 'day' ? 'jour' : 'loc.' }}
                                        </span>
                                    @endif
                                </label>
                            @endforeach

                            @if($fuelReturnFee > 0)
                                <label class="flex items-center justify-between p-4 rounded-xl bg-gray-50 hover:bg-amber-50 cursor-pointer transition border border-transparent hover:border-amber-200">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="options[]" value="Retour sans plein"
                                               data-price="{{ $fuelReturnFee }}" data-per="booking" data-free="0"
                                               class="w-5 h-5 text-amber-600 rounded focus:ring-amber-500 option-checkbox">
                                        <div>
                                            <span class="font-medium text-gray-900">Retour sans plein</span>
                                            <p class="text-xs text-gray-500">Je ne souhaite pas faire le plein au retour</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold text-amber-600">+{{ number_format($fuelReturnFee, 0, ',', ' ') }} DA</span>
                                </label>
                            @endif

                            @if($washReturnFee > 0)
                                <label class="flex items-center justify-between p-4 rounded-xl bg-gray-50 hover:bg-amber-50 cursor-pointer transition border border-transparent hover:border-amber-200">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="options[]" value="Retour sans lavage"
                                               data-price="{{ $washReturnFee }}" data-per="booking" data-free="0"
                                               class="w-5 h-5 text-amber-600 rounded focus:ring-amber-500 option-checkbox">
                                        <div>
                                            <span class="font-medium text-gray-900">Retour sans lavage</span>
                                            <p class="text-xs text-gray-500">Je ne souhaite pas laver le véhicule au retour</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold text-amber-600">+{{ number_format($washReturnFee, 0, ',', ' ') }} DA</span>
                                </label>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Une remarque ?</h2>
                        <textarea name="internal_notes" rows="3"
                                  class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                                  placeholder="Ex: Je voyage avec un bébé, auriez-vous un siège auto ?..."></textarea>
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

                        <!-- Submit -->
                        <button type="submit" id="submitBtn" disabled
                                class="w-full py-4 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition shadow-lg shadow-amber-600/20 disabled:opacity-50 disabled:cursor-not-allowed">
                            Envoyer ma demande
                        </button>

                        <p class="text-xs text-gray-400 text-center">Le loueur examinera votre demande et vous contactera rapidement.</p>
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
    const checkOptionsUrl = '{{ route("booking.check-options") }}';
    const csrfToken = '{{ csrf_token() }}';
    const returnMarginHours = {{ $returnMarginHours ?? 2 }};

    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const pickupTime = document.getElementById('pickup_time');
    const pickupZone = document.getElementById('pickup_zone_id');
    const returnZone = document.getElementById('return_zone_id');
    const differentReturn = document.getElementById('different_return');
    const returnFields = document.getElementById('return_fields');
    const sameReturnLocation = document.getElementById('same_return_location');
    const optionBoxes = document.querySelectorAll('.option-checkbox');
    const rentalOptions = document.querySelectorAll('.rental-option');
    const priceBreakdown = document.getElementById('priceBreakdown');
    const submitBtn = document.getElementById('submitBtn');
    const returnTimeInfo = document.getElementById('returnTimeInfo');
    const returnDateDisplay = document.getElementById('returnDateDisplay');
    const returnTimeDisplay = document.getElementById('returnTimeDisplay');

    // Toggle retour différent
    if (differentReturn) {
        differentReturn.addEventListener('change', function() {
            if (this.checked) {
                if (returnFields) returnFields.classList.remove('hidden');
                if (sameReturnLocation) sameReturnLocation.value = '0';
            } else {
                if (returnFields) returnFields.classList.add('hidden');
                if (sameReturnLocation) sameReturnLocation.value = '1';
                if (returnZone) returnZone.value = '';
            }
            recalculate();
        });
    }

    function updateReturnTime() {
        if (!endDate.value || !pickupTime.value) {
            returnTimeInfo.classList.add('hidden');
            return;
        }

        // Calculer l'heure de retour = heure de prise en charge + marge
        const [hours, minutes] = pickupTime.value.split(':').map(Number);
        let returnHour = hours + returnMarginHours;
        if (returnHour > 22) returnHour = 22;

        // Formater la date
        const endDateObj = new Date(endDate.value);
        const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        const formattedDate = endDateObj.toLocaleDateString('fr-FR', options);

        returnDateDisplay.textContent = formattedDate;
        returnTimeDisplay.textContent = String(returnHour).padStart(2, '0') + ':00';
        returnTimeInfo.classList.remove('hidden');
    }

    // Vérifier la disponibilité des options
    function checkOptionAvailability() {
        if (!startDate.value || !endDate.value) return;

        fetch(checkOptionsUrl, {
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
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.options) {
                // Créer un map des options disponibles
                const availabilityMap = {};
                data.options.forEach(opt => {
                    availabilityMap[opt.name] = opt.is_available;
                });

                // Mettre à jour l'UI
                rentalOptions.forEach(checkbox => {
                    const optionName = checkbox.dataset.optionName;
                    const label = checkbox.closest('.option-label');
                    const badge = label ? label.querySelector('.unavailable-badge') : null;

                    if (availabilityMap[optionName] === false) {
                        // Option indisponible
                        checkbox.disabled = true;
                        checkbox.checked = false;
                        if (label) {
                            label.classList.add('opacity-50', 'cursor-not-allowed');
                            label.classList.remove('cursor-pointer', 'hover:bg-amber-50', 'hover:bg-green-100');
                        }
                        if (badge) badge.classList.remove('hidden');
                    } else {
                        // Option disponible
                        checkbox.disabled = false;
                        if (label) {
                            label.classList.remove('opacity-50', 'cursor-not-allowed');
                            label.classList.add('cursor-pointer');
                            if (checkbox.dataset.free === '1') {
                                label.classList.add('hover:bg-green-100');
                            } else {
                                label.classList.add('hover:bg-amber-50');
                            }
                        }
                        if (badge) badge.classList.add('hidden');
                    }
                });
            }
        })
        .catch(() => {});
    }

    function recalculate() {
        if (!startDate.value || !endDate.value) return;

        updateReturnTime();

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
                // Prix de base (sans frais de service)
                const basePriceWithoutFees = data.base_price - (data.client_service_fee_total || 0);
                html += `<div class="flex justify-between"><span class="text-gray-600">${data.total_days} jour(s) x ${fmt(data.loueur_daily_rate)} DA</span><span class="font-medium">${fmt(basePriceWithoutFees)} DA</span></div>`;

                // Frais de service ResaDZ
                if (data.client_service_fee_total > 0) {
                    html += `<div class="flex justify-between text-gray-600"><span>Frais de service <span class="text-xs text-gray-400">(${data.total_days}j x ${fmt(data.client_service_fee_per_day)} DA)</span></span><span class="font-medium">${fmt(data.client_service_fee_total)} DA</span></div>`;
                }

                if (data.duration_discount > 0) {
                    html += `<div class="flex justify-between text-green-600"><span>Remise durée</span><span>-${fmt(data.duration_discount)} DA</span></div>`;
                }
                if (data.season_surcharge > 0) {
                    html += `<div class="flex justify-between text-orange-600"><span>${data.season_name || 'Haute saison'}</span><span>+${fmt(data.season_surcharge)} DA</span></div>`;
                }
                if (data.delivery_fee > 0) {
                    html += `<div class="flex justify-between"><span class="text-gray-600">Livraison</span><span>+${fmt(data.delivery_fee)} DA</span></div>`;
                }
                if (data.return_fee > 0) {
                    html += `<div class="flex justify-between"><span class="text-gray-600">Retour</span><span>+${fmt(data.return_fee)} DA</span></div>`;
                }
                if (data.options_total > 0) {
                    html += `<div class="flex justify-between"><span class="text-gray-600">Options</span><span>+${fmt(data.options_total)} DA</span></div>`;
                }

                html += `<div class="border-t border-gray-200 pt-3 mt-3 flex justify-between text-lg"><span class="font-bold text-gray-900">Total</span><span class="font-black text-amber-600">${data.formatted_total}</span></div>`;

                // Info frais de service
                if (data.client_service_fee_total > 0) {
                    html += `<div class="text-xs text-gray-400 mt-2 p-2 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-500">Pourquoi des frais de service ?</span><br>
                        Ces frais couvrent : vérification des loueurs, support client 7j/7, paiement sécurisé et protection de vos données.
                    </div>`;
                }

                if (data.deposit_amount > 0) {
                    html += `<div class="flex justify-between text-sm mt-2"><span class="text-gray-500">Caution (remboursable)</span><span class="font-semibold">${data.formatted_deposit}</span></div>`;
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

    // Recalculer quand les champs changent
    [startDate, endDate, pickupTime].forEach(el => el.addEventListener('change', recalculate));
    if (pickupZone) pickupZone.addEventListener('change', recalculate);
    if (returnZone) returnZone.addEventListener('change', recalculate);
    optionBoxes.forEach(cb => cb.addEventListener('change', recalculate));

    // Vérifier disponibilité des options quand les dates changent
    [startDate, endDate].forEach(el => el.addEventListener('change', checkOptionAvailability));
</script>
@endsection

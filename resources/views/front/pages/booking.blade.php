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

        {{-- Affichage des erreurs globales --}}
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-red-800">Erreur lors de la réservation</p>
                        <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
            @csrf
            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left: Form -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Dates & Heure de prise en charge -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Quand souhaitez-vous louer ?</h2>
                        {{-- Calendrier de sélection type Airbnb : pilote les champs cachés start_date / end_date --}}
                        <input type="hidden" name="start_date" id="start_date" value="{{ old('start_date', request('start')) }}">
                        <input type="hidden" name="end_date" id="end_date" value="{{ old('end_date', request('end')) }}">
                        <div id="bookingRangeCalendar" class="mb-2"></div>
                        @error('start_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        @error('end_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
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
                                <input type="text" name="client_name" required
                                       value="{{ old('client_name', auth()->user()->name ?? '') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="Nom complet *">
                                @error('client_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <div class="flex gap-2">
                                    <select name="client_phone_indicatif"
                                            class="px-2 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500 text-sm"
                                            style="max-width: 130px;">
                                        <option value="+213" {{ old('client_phone_indicatif', '+213') === '+213' ? 'selected' : '' }}>🇩🇿 +213</option>
                                        <option value="+33" {{ old('client_phone_indicatif') === '+33' ? 'selected' : '' }}>🇫🇷 +33</option>
                                        <option value="+32" {{ old('client_phone_indicatif') === '+32' ? 'selected' : '' }}>🇧🇪 +32</option>
                                        <option value="+41" {{ old('client_phone_indicatif') === '+41' ? 'selected' : '' }}>🇨🇭 +41</option>
                                        <option value="+34" {{ old('client_phone_indicatif') === '+34' ? 'selected' : '' }}>🇪🇸 +34</option>
                                        <option value="+39" {{ old('client_phone_indicatif') === '+39' ? 'selected' : '' }}>🇮🇹 +39</option>
                                        <option value="+44" {{ old('client_phone_indicatif') === '+44' ? 'selected' : '' }}>🇬🇧 +44</option>
                                        <option value="+49" {{ old('client_phone_indicatif') === '+49' ? 'selected' : '' }}>🇩🇪 +49</option>
                                        <option value="+1" {{ old('client_phone_indicatif') === '+1' ? 'selected' : '' }}>🇨🇦/🇺🇸 +1</option>
                                        <option value="+90" {{ old('client_phone_indicatif') === '+90' ? 'selected' : '' }}>🇹🇷 +90</option>
                                        <option value="+971" {{ old('client_phone_indicatif') === '+971' ? 'selected' : '' }}>🇦🇪 +971</option>
                                        <option value="+966" {{ old('client_phone_indicatif') === '+966' ? 'selected' : '' }}>🇸🇦 +966</option>
                                        <option value="+974" {{ old('client_phone_indicatif') === '+974' ? 'selected' : '' }}>🇶🇦 +974</option>
                                        <option value="+216" {{ old('client_phone_indicatif') === '+216' ? 'selected' : '' }}>🇹🇳 +216</option>
                                        <option value="+212" {{ old('client_phone_indicatif') === '+212' ? 'selected' : '' }}>🇲🇦 +212</option>
                                    </select>
                                    <input type="tel" name="client_phone" required
                                           value="{{ old('client_phone') }}"
                                           class="flex-1 px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                                           placeholder="Téléphone / WhatsApp *">
                                </div>
                                <p class="text-gray-400 text-xs mt-1">Choisissez l'indicatif de votre numéro WhatsApp (ex : +33 si numéro français)</p>
                                @error('client_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <input type="email" name="client_email" required
                                       value="{{ old('client_email', auth()->user()->email ?? '') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="Email *">
                                <p class="text-xs text-gray-500 mt-1">Vous recevrez la confirmation par email</p>
                                @error('client_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Lieu de prise en charge & retour -->
                    @php $customLocationEnabled = $vehicle->loueur ? (bool) $vehicle->loueur->getSetting('custom_location_enabled', false) : false; @endphp
                    @if($deliveryZones->where('delivery_available', true)->count() > 0 || $customLocationEnabled)
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Lieu de prise en charge</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Zone *</label>
                                <select name="pickup_zone_id" id="pickup_zone_id" required
                                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                    @if($deliveryZones->where('delivery_available', true)->count() > 1 || $customLocationEnabled)
                                        <option value="">Sélectionnez une zone</option>
                                    @endif
                                    @foreach($deliveryZones->where('delivery_available', true) as $zone)
                                        <option value="{{ $zone->id }}" {{ $deliveryZones->where('delivery_available', true)->count() === 1 && !$customLocationEnabled ? 'selected' : '' }}>
                                            {{ $zone->name }} {{ $zone->delivery_fee > 0 ? '(+' . $zone->getFormattedDeliveryFee() . ')' : '(Gratuit)' }}
                                        </option>
                                    @endforeach
                                    @if($customLocationEnabled)
                                        <option value="custom">📍 Autre lieu — À confirmer par le loueur</option>
                                    @endif
                                </select>
                                @error('pickup_zone_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Champ lieu personnalisé prise en charge --}}
                            <div id="custom_pickup_fields" class="hidden space-y-3">
                                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                                    <p class="text-sm text-amber-800 font-medium">📍 Indiquez l'adresse exacte souhaitée. Le loueur confirmera la disponibilité et le prix.</p>
                                </div>
                                <input type="text" name="custom_pickup_location" id="custom_pickup_location"
                                       placeholder="Ex: Hôtel Sheraton, Oran centre, Devant la gare..."
                                       class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                            </div>

                            {{-- Message livraison aéroport offerte (dynamique via JS) --}}
                            <div id="airportDeliveryHint" class="hidden"></div>

                            {{-- Toggle retour différent --}}
                            @if($deliveryZones->where('return_available', true)->count() > 1 || $customLocationEnabled)
                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                                <input type="checkbox" id="different_return" class="w-5 h-5 text-amber-600 rounded focus:ring-amber-500">
                                <span class="text-sm text-gray-700">Je souhaite rendre le véhicule à un endroit différent</span>
                            </label>

                            {{-- Zone de retour (masqué par défaut) --}}
                            <div id="return_fields" class="hidden pt-4 border-t border-gray-200 space-y-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Zone de retour *</label>
                                <select name="return_zone_id" id="return_zone_id"
                                        class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Sélectionnez une zone</option>
                                    @foreach($deliveryZones->where('return_available', true) as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }} {{ $zone->return_fee > 0 ? '(+' . number_format($zone->return_fee, 0, ',', ' ') . ' DA)' : '(Gratuit)' }}</option>
                                    @endforeach
                                    @if($customLocationEnabled)
                                        <option value="custom">📍 Autre lieu — À confirmer par le loueur</option>
                                    @endif
                                </select>

                                {{-- Champ lieu personnalisé retour --}}
                                <div id="custom_return_fields" class="hidden">
                                    <input type="text" name="custom_return_location" id="custom_return_location"
                                           placeholder="Ex: Aéroport Houari Boumediene, Terminal 1..."
                                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-amber-500 focus:border-amber-500">
                                </div>
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
                                  placeholder="Informations complémentaires pour le loueur (optionnel)"></textarea>
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

                        <!-- Conditions du loueur -->
                        @if(!empty($conditionsPdf) || count($rentalConditions ?? []) > 0)
                        <div class="border-t border-gray-200 pt-4 mt-2">
                            <p class="font-semibold text-gray-900 text-sm mb-3">Conditions du loueur</p>
                            @if(!empty($conditionsPdf))
                                <a href="{{ asset('storage/' . $conditionsPdf) }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition mb-3">
                                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Conditions générales (PDF)</p>
                                        <p class="text-xs text-gray-500">Télécharger le document</p>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            @endif
                            @foreach($rentalConditions ?? [] as $condition)
                                <div class="flex gap-2 mb-2 last:mb-0">
                                    <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-800">{{ $condition['title'] === 'Autre' ? ($condition['custom_title'] ?? 'Condition') : $condition['title'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $condition['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Info paiement en ligne -->
                        @if(!empty($advancePaymentMethods) || ($vehicle->loueur && $vehicle->loueur->stripe_onboarding_complete))
                        <div class="border-t border-gray-200 pt-4 mt-2">
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <div>
                                        <p class="font-semibold text-blue-900 text-sm">Paiement sécurisé disponible</p>
                                        <p class="text-xs text-blue-700 mt-1">
                                            Après validation de votre demande, vous pourrez régler l'acompte ou la totalité en ligne par <strong>carte bancaire</strong> (Visa, Mastercard). Vous pouvez aussi payer en espèces à la remise du véhicule.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

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

@if(\App\Models\Setting::get('facebook_pixel_id'))
<script>
fbq('track','InitiateCheckout',{content_name:'{{ addslashes($vehicle->full_name) }}',value:{{ $vehicle->price_per_day }},currency:'DZD'});
</script>
@endif

@endsection

@section('scripts')
<script src="/js/resadz-range-calendar.js"></script>
<script>
    const vehicleId = {{ $vehicle->id }};
    const calcUrl = '{{ route("booking.calculate") }}';
    const checkOptionsUrl = '{{ route("booking.check-options") }}';
    const csrfToken = '{{ csrf_token() }}';
    const returnMarginHours = {{ $returnMarginHours ?? 2 }};

    // Types des zones de livraison (pour détecter les zones aéroport)
    const zoneTypes = {!! json_encode($deliveryZones->pluck('type', 'id')->toArray()) !!};
    const freeAirportDeliveryDays = {{ $vehicle->loueur ? (int) $vehicle->loueur->getSetting('free_airport_delivery_days', 0) : 0 }};
    const airportDeliveryHint = document.getElementById('airportDeliveryHint');

    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    // === Calendrier Airbnb : mêmes données de dispo que le calendrier loueur ===
    (function initRangeCalendar() {
        const container = document.getElementById('bookingRangeCalendar');
        if (!container || !window.ResadzRangeCalendar) return;

        const cal = new ResadzRangeCalendar(container, {
            minDays: {{ max(1, (int) ($vehicle->min_rental_days ?? 1)) }},
            start: startDate.value || null,
            end: endDate.value || null,
            onChange: function (s, e) {
                startDate.value = s;
                endDate.value = e;
                startDate.dispatchEvent(new Event('change'));
                endDate.dispatchEvent(new Event('change'));
            }
        });

        fetch('/api/vehicles/{{ $vehicle->slug }}/unavailable-dates')
            .then(r => r.json())
            .then(data => { if (data && data.dates) cal.setUnavailable(data.dates); })
            .catch(() => {});
    })();
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
                const symbol = data.currency === 'EUR' ? '€' : 'DA';

                // Prix de base (nouveau modèle 2026 - pas de frais de service client)
                let priceLabel = `${data.total_days} jour(s) x ${fmt(data.loueur_daily_rate)} ${symbol}`;
                if (data.degressive_applied && data.degressive_from_days) {
                    priceLabel = `<span class="text-green-600 font-medium">Prix réduit ${data.degressive_from_days}j+</span> · ${data.total_days} jour(s) x ${fmt(data.loueur_daily_rate)} ${symbol}`;
                }
                html += `<div class="flex justify-between"><span class="text-gray-600">${priceLabel}</span><span class="font-medium">${fmt(data.base_price)} ${symbol}</span></div>`;

                if (data.duration_discount > 0) {
                    html += `<div class="flex justify-between text-green-600"><span>Remise durée</span><span>-${fmt(data.duration_discount)} ${symbol}</span></div>`;
                }
                if (data.season_surcharge > 0) {
                    if (data.seasonal_details && data.seasonal_details.length > 0) {
                        data.seasonal_details.forEach(sd => {
                            html += `<div class="flex justify-between text-orange-600"><span>🌞 ${sd.name} <span class="text-xs text-orange-400">(${sd.days}j × +${fmt(sd.supplement_per_day)} ${symbol})</span></span><span>+${fmt(sd.total)} ${symbol}</span></div>`;
                            html += `<div class="text-xs text-orange-400 text-right -mt-1">du ${sd.start} au ${sd.end}</div>`;
                        });
                    } else {
                        html += `<div class="flex justify-between text-orange-600"><span>${data.season_name || 'Haute saison'}</span><span>+${fmt(data.season_surcharge)} ${symbol}</span></div>`;
                    }
                }
                // Affichage livraison/retour (custom ou zone)
                const isCustomPickup = pickupZone && pickupZone.value === 'custom';
                const isCustomReturn = returnZone && returnZone.value === 'custom';

                if (isCustomPickup) {
                    html += `<div class="flex justify-between text-amber-600"><span>📍 Livraison lieu personnalisé</span><span class="font-medium text-xs">À confirmer</span></div>`;
                } else if (data.free_airport_delivery) {
                    html += `<div class="flex justify-between text-green-600"><span>✈️ Livraison aéroport</span><span class="font-semibold">Offerte ✓</span></div>`;
                } else if (data.delivery_fee > 0) {
                    html += `<div class="flex justify-between"><span class="text-gray-600">Livraison</span><span>+${fmt(data.delivery_fee)} ${symbol}</span></div>`;
                }
                if (isCustomReturn) {
                    html += `<div class="flex justify-between text-amber-600"><span>📍 Retour lieu personnalisé</span><span class="font-medium text-xs">À confirmer</span></div>`;
                } else if (!data.free_airport_delivery && data.return_fee > 0) {
                    html += `<div class="flex justify-between"><span class="text-gray-600">Retour</span><span>+${fmt(data.return_fee)} ${symbol}</span></div>`;
                }

                // Afficher chaque option par son nom
                if (data.options_detail && data.options_detail.length > 0) {
                    data.options_detail.forEach(opt => {
                        html += `<div class="flex justify-between"><span class="text-gray-600">${opt.name}</span><span>+${fmt(opt.total)} ${symbol}</span></div>`;
                    });
                }

                html += `<div class="border-t border-gray-200 pt-3 mt-3 flex justify-between text-lg"><span class="font-bold text-gray-900">Total</span><span class="font-black text-amber-600">${data.formatted_total}</span></div>`;
                if (data.currency !== 'EUR' && data.formatted_total_eur) {
                    html += `<div class="flex justify-between text-sm text-gray-500"><span></span><span>ou ${data.formatted_total_eur}</span></div>`;
                }


                // Caution - afficher les deux montants si disponibles
                if (data.deposit_amount_da > 0 || data.deposit_amount_eur > 0) {
                    let depositHtml = `<div class="flex justify-between text-sm mt-2"><span class="text-gray-500">Caution (remboursable)</span><span class="font-semibold">`;
                    if (data.formatted_deposit_da) {
                        depositHtml += data.formatted_deposit_da;
                    }
                    if (data.formatted_deposit_da && data.formatted_deposit_eur) {
                        depositHtml += ` <span class="text-gray-400">ou</span> `;
                    }
                    if (data.formatted_deposit_eur) {
                        depositHtml += data.formatted_deposit_eur;
                    }
                    depositHtml += `</span></div>`;
                    html += depositHtml;
                }

                if (data.advance_amount > 0) {
                    let advanceEurHtml = '';
                    if (data.currency !== 'EUR' && data.formatted_advance_eur) {
                        advanceEurHtml = `<span class="text-blue-500 text-sm ml-1">(ou ${data.formatted_advance_eur})</span>`;
                    }
                    html += `<div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-blue-800 font-semibold text-sm">Acompte à verser</span>
                                <p class="text-blue-600 text-xs mt-0.5">${data.advance_percentage}% du total à payer à la réservation</p>
                            </div>
                            <span class="font-bold text-blue-700 text-lg">${data.formatted_advance}${advanceEurHtml}</span>
                        </div>
                    </div>`;
                }

                priceBreakdown.innerHTML = html;

                // Hint livraison aéroport offerte
                if (airportDeliveryHint && freeAirportDeliveryDays > 0) {
                    const selectedZoneId = pickupZone ? pickupZone.value : null;
                    const isAirportZone = selectedZoneId && zoneTypes[selectedZoneId] === 'airport';

                    if (isAirportZone && data.free_airport_delivery) {
                        airportDeliveryHint.innerHTML = '<div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium">✅ Livraison aéroport offerte pour cette durée !</div>';
                        airportDeliveryHint.classList.remove('hidden');
                    } else if (isAirportZone && !data.free_airport_delivery && data.total_days < freeAirportDeliveryDays) {
                        const joursManquants = freeAirportDeliveryDays - data.total_days;
                        airportDeliveryHint.innerHTML = `<div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">💡 Réservez ${freeAirportDeliveryDays} jours ou plus pour bénéficier de la livraison aéroport gratuite <span class="font-medium">(encore ${joursManquants} jour${joursManquants > 1 ? 's' : ''})</span></div>`;
                        airportDeliveryHint.classList.remove('hidden');
                    } else {
                        airportDeliveryHint.classList.add('hidden');
                    }
                } else if (airportDeliveryHint) {
                    airportDeliveryHint.classList.add('hidden');
                }

                // Afficher/masquer la section choix méthode de paiement acompte
                const advSection = document.getElementById('advancePaymentSection');
                const advAmountDisplay = document.getElementById('advanceAmountDisplay');
                const advAmountEurDisplay = document.getElementById('advanceAmountEurDisplay');
                if (advSection) {
                    if (data.advance_amount > 0) {
                        advSection.classList.remove('hidden');
                        if (advAmountDisplay) advAmountDisplay.textContent = data.formatted_advance;
                        if (advAmountEurDisplay && data.currency !== 'EUR' && data.formatted_advance_eur) {
                            advAmountEurDisplay.textContent = ` (ou ${data.formatted_advance_eur})`;
                        } else if (advAmountEurDisplay) {
                            advAmountEurDisplay.textContent = '';
                        }
                    } else {
                        advSection.classList.add('hidden');
                    }
                }

                submitBtn.disabled = false;
            }
        })
        .catch(() => {});
    }

    function fmt(n) {
        return new Intl.NumberFormat('fr-DZ', { maximumFractionDigits: 0 }).format(n);
    }

    // Gestion des lieux personnalisés
    const customPickupFields = document.getElementById('custom_pickup_fields');
    const customReturnFields = document.getElementById('custom_return_fields');

    if (pickupZone) {
        pickupZone.addEventListener('change', function() {
            if (customPickupFields) {
                customPickupFields.classList.toggle('hidden', this.value !== 'custom');
            }
            // Si custom, on ne requiert plus le zone_id réel
            if (this.value === 'custom') {
                this.removeAttribute('required');
            } else {
                this.setAttribute('required', 'required');
            }
            recalculate();
        });
    }
    if (returnZone) {
        returnZone.addEventListener('change', function() {
            if (customReturnFields) {
                customReturnFields.classList.toggle('hidden', this.value !== 'custom');
            }
            recalculate();
        });
    }

    // Recalculer quand les champs changent
    [startDate, endDate, pickupTime].forEach(el => el.addEventListener('change', recalculate));
    optionBoxes.forEach(cb => cb.addEventListener('change', recalculate));

    // Vérifier disponibilité des options quand les dates changent
    [startDate, endDate].forEach(el => el.addEventListener('change', checkOptionAvailability));

    // --- Vérification des dates bloquées ---
    let unavailableDates = [];
    const availabilityWarning = document.createElement('div');
    availabilityWarning.id = 'availabilityWarning';
    availabilityWarning.className = 'hidden mt-3 p-4 bg-red-50 border border-red-200 rounded-xl';
    availabilityWarning.innerHTML = '<div class="flex items-center gap-2"><svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg><p class="text-sm text-red-700 font-medium">Ce véhicule n\'est pas disponible pour les dates sélectionnées. Certaines dates sont bloquées ou déjà réservées.</p></div>';
    startDate.closest('.bg-white')?.querySelector('.grid')?.after(availabilityWarning);

    // Charger les dates indisponibles au chargement de la page
    fetch('/api/vehicles/{{ $vehicle->slug }}/unavailable-dates')
        .then(r => r.json())
        .then(data => {
            if (data.success) unavailableDates = data.dates;
        })
        .catch(() => {});

    function hasUnavailableDateInRange(start, end) {
        if (!start || !end || unavailableDates.length === 0) return false;
        return unavailableDates.some(d => d >= start && d <= end);
    }

    function checkDateAvailability() {
        const s = startDate.value;
        const e = endDate.value;
        if (!s || !e) {
            availabilityWarning.classList.add('hidden');
            submitBtn.disabled = false;
            return;
        }
        if (unavailableDates.includes(s) || unavailableDates.includes(e) || hasUnavailableDateInRange(s, e)) {
            availabilityWarning.classList.remove('hidden');
            submitBtn.disabled = true;
        } else {
            availabilityWarning.classList.add('hidden');
        }
    }

    [startDate, endDate].forEach(el => el.addEventListener('change', checkDateAvailability));

    // Gestion du choix de méthode de paiement d'acompte
    const advanceMethodRadios = document.querySelectorAll('.advance-method-radio');
    const advanceMethodLabels = document.querySelectorAll('.advance-method-label');
    const advanceMethodInput = document.getElementById('advance_payment_method');
    const advanceTimerInfo = document.getElementById('advanceTimerInfo');
    const timerDisplay = document.getElementById('timerDisplay');

    advanceMethodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Mettre à jour le hidden input
            if (advanceMethodInput) advanceMethodInput.value = this.value;

            // Highlight la méthode sélectionnée
            advanceMethodLabels.forEach(label => {
                label.classList.remove('border-amber-500', 'bg-amber-50');
                label.classList.add('border-transparent');
            });
            const parentLabel = this.closest('.advance-method-label');
            if (parentLabel) {
                parentLabel.classList.remove('border-transparent');
                parentLabel.classList.add('border-amber-500', 'bg-amber-50');
            }

            // Afficher le délai correspondant
            const timer = parentLabel ? parentLabel.dataset.timer : null;
            if (timer && advanceTimerInfo && timerDisplay) {
                timerDisplay.textContent = timer;
                advanceTimerInfo.classList.remove('hidden');
            }
        });
    });
</script>
@endsection

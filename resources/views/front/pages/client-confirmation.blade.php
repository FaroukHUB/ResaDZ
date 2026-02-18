@extends('front.layouts.app')

@section('title', 'Ma réservation ' . $booking->reference . ' - ResaDZ')

@section('content')

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="text-center mb-8">
            @if($booking->isConfirmedByLoueur())
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Réservation confirmée</h1>
                <p class="text-gray-500 mt-2">Votre réservation a été acceptée par le loueur.</p>
            @else
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Réservation en attente</h1>
                <p class="text-gray-500 mt-2">Le loueur examine votre demande.</p>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left: Booking details -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Reference & Status -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div>
                            <span class="text-sm text-gray-500">Référence</span>
                            <p class="text-2xl font-bold text-gray-900">{{ $booking->reference }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-700',
                                'confirmed' => 'bg-green-100 text-green-700',
                                'active' => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-gray-100 text-gray-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                            ];
                            $statusLabels = [
                                'pending' => 'En attente',
                                'confirmed' => 'Confirmée',
                                'active' => 'En cours',
                                'completed' => 'Terminée',
                                'cancelled' => 'Annulée',
                            ];
                        @endphp
                        <span class="px-4 py-2 {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-700' }} text-sm font-semibold rounded-full">
                            {{ $statusLabels[$booking->status] ?? $booking->status }}
                        </span>
                    </div>

                    <!-- Vehicle -->
                    <div class="flex items-center gap-4 mt-4">
                        @if($booking->vehicle->image)
                            <img src="{{ asset('storage/' . $booking->vehicle->image) }}" alt="{{ $booking->vehicle->full_name }}" class="w-24 h-16 rounded-xl object-cover">
                        @endif
                        <div>
                            <h3 class="font-bold text-lg text-gray-900">{{ $booking->vehicle->full_name }}</h3>
                            <p class="text-sm text-gray-500">{{ $booking->loueur->company_name ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Dates & Location -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="font-bold text-gray-900 mb-4">Dates et lieu</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center gap-2 text-green-600 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-sm font-medium">Prise en charge</span>
                            </div>
                            <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->start_date)->translatedFormat('l d F Y') }}</p>
                            <p class="text-gray-600">à {{ $booking->pickup_time }}</p>
                            <p class="text-sm text-gray-500 mt-2">{{ $booking->pickup_address }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center gap-2 text-red-600 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-sm font-medium">Retour</span>
                            </div>
                            <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->end_date)->translatedFormat('l d F Y') }}</p>
                            <p class="text-gray-600">avant {{ $booking->return_time }}</p>
                            <p class="text-sm text-gray-500 mt-2">{{ $booking->return_address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Conditions -->
                @if(count($rentalConditions) > 0)
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="font-bold text-gray-900 mb-4">Conditions de location</h2>
                    <div class="space-y-4">
                        @foreach($rentalConditions as $condition)
                            <div class="flex gap-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $condition['title'] === 'Autre' ? ($condition['custom_title'] ?? 'Condition') : $condition['title'] }}</h3>
                                    <p class="text-sm text-gray-600">{{ $condition['description'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Documents Upload -->
                @if($requireDocuments && $booking->isConfirmedByLoueur())
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="font-bold text-gray-900 mb-2">Documents requis</h2>
                    <p class="text-sm text-gray-500 mb-4">Pour finaliser votre réservation, veuillez fournir les documents suivants.</p>

                    <form action="{{ route('booking.upload-documents', $booking->confirmation_token) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- CNI -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Carte d'identité</label>
                                @if($booking->client_id_document)
                                    <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-xl">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span class="text-green-700 text-sm">Document fourni</span>
                                    </div>
                                @else
                                    <input type="file" name="client_id_document" accept="image/*,.pdf"
                                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                                @endif
                            </div>

                            <!-- Permis recto -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Permis de conduire (recto)</label>
                                @if($booking->client_license_front)
                                    <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-xl">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span class="text-green-700 text-sm">Document fourni</span>
                                    </div>
                                @else
                                    <input type="file" name="client_license_front" accept="image/*,.pdf"
                                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                                @endif
                            </div>

                            <!-- Permis verso -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Permis de conduire (verso)</label>
                                @if($booking->client_license_back)
                                    <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-xl">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span class="text-green-700 text-sm">Document fourni</span>
                                    </div>
                                @else
                                    <input type="file" name="client_license_back" accept="image/*,.pdf"
                                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                                @endif
                            </div>
                        </div>

                        @if(!$booking->client_id_document || !$booking->client_license_front || !$booking->client_license_back)
                            <button type="submit" class="w-full py-3 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition">
                                Envoyer mes documents
                            </button>
                        @endif
                    </form>
                </div>
                @endif

            </div>

            <!-- Right: Price Summary & Actions -->
            <div class="space-y-6">

                <!-- Price Summary -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 sticky top-24">
                    <h2 class="font-bold text-gray-900 mb-4">Récapitulatif</h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ $booking->total_days }} jour(s)</span>
                            <span class="font-medium">{{ number_format($booking->base_price, 0, ',', ' ') }} DA</span>
                        </div>

                        @if($booking->duration_discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Remise durée</span>
                                <span>-{{ number_format($booking->duration_discount, 0, ',', ' ') }} DA</span>
                            </div>
                        @endif

                        @if($booking->season_surcharge > 0)
                            <div class="flex justify-between text-orange-600">
                                <span>Supplément saison</span>
                                <span>+{{ number_format($booking->season_surcharge, 0, ',', ' ') }} DA</span>
                            </div>
                        @endif

                        @if($booking->delivery_fee > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Livraison</span>
                                <span>+{{ number_format($booking->delivery_fee, 0, ',', ' ') }} DA</span>
                            </div>
                        @endif

                        @if($booking->return_fee > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Retour</span>
                                <span>+{{ number_format($booking->return_fee, 0, ',', ' ') }} DA</span>
                            </div>
                        @endif

                        @if($booking->options_total > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Options</span>
                                <span>+{{ number_format($booking->options_total, 0, ',', ' ') }} DA</span>
                            </div>
                        @endif

                        @if($booking->extra_fees > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Frais supplémentaires</span>
                                <span>+{{ number_format($booking->extra_fees, 0, ',', ' ') }} DA</span>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-900">Total</span>
                            <span class="text-2xl font-black text-amber-600">{{ $booking->getFormattedTotal() }}</span>
                        </div>
                    </div>

                    @if($booking->deposit_amount > 0)
                        <div class="bg-amber-50 rounded-xl p-4 mt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-amber-800 text-sm font-medium">Caution (remboursable)</span>
                                <span class="font-bold text-amber-700">{{ number_format($booking->deposit_amount, 0, ',', ' ') }} {{ $booking->deposit_currency === 'EUR' ? '€' : 'DA' }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Acompte -->
                    @if($depositRequired && $booking->advance_amount > 0 && $booking->isConfirmedByLoueur())
                        <div class="bg-blue-50 rounded-xl p-4 mt-4">
                            <p class="text-blue-800 font-medium mb-2">Acompte</p>
                            <p class="text-2xl font-bold text-blue-700">{{ number_format($booking->advance_amount, 0, ',', ' ') }} DA</p>
                            <p class="text-blue-600 text-xs mt-1">Pour garantir votre réservation</p>

                            @if(in_array('paypal', $depositPaymentMethods) && $booking->loueur?->paypal_email)
                                <a href="https://www.paypal.me/{{ $booking->loueur->paypal_email }}/{{ $booking->advance_amount }}" target="_blank"
                                   class="mt-3 w-full flex items-center justify-center gap-2 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.471z"/></svg>
                                    Payer par PayPal
                                </a>
                            @endif

                            @if(in_array('cash', $depositPaymentMethods))
                                <p class="text-blue-600 text-sm mt-3 text-center">ou payez en espèces lors de la prise en charge</p>
                            @endif
                        </div>
                    @endif

                    <!-- Download Contract -->
                    @if($booking->isConfirmedByLoueur())
                        <div class="mt-4 space-y-2">
                            <a href="{{ route('contract.download-by-token', $booking->confirmation_token) }}" target="_blank"
                               class="w-full flex items-center justify-center gap-2 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Télécharger le contrat
                            </a>
                        </div>
                    @endif

                    <!-- Contact Loueur -->
                    @if($booking->loueur)
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <p class="text-gray-500 text-sm mb-3 text-center">Besoin d'aide ?</p>
                            <div class="flex flex-col gap-2">
                                @if($booking->loueur->phone)
                                    <a href="tel:{{ $booking->loueur->phone }}" class="flex items-center justify-center gap-2 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        {{ $booking->loueur->phone }}
                                    </a>
                                @endif
                                @if($booking->loueur->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->loueur->whatsapp) }}?text={{ urlencode('Bonjour, j\'ai une question concernant ma réservation ' . $booking->reference) }}" target="_blank"
                                       class="flex items-center justify-center gap-2 py-2.5 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition text-sm">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        WhatsApp
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('home') }}" class="text-amber-600 font-semibold hover:text-amber-700">&larr; Retour à l'accueil</a>
        </div>
    </div>

@endsection

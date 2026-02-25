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

                        @if($booking->client_service_fee > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Frais de service</span>
                                <span>+{{ number_format($booking->client_service_fee, 0, ',', ' ') }} DA</span>
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

                    <!-- Messages -->
                    @if(in_array($booking->status, ['confirmed', 'active', 'completed']))
                        <div class="mt-4">
                            <a href="{{ route('client.conversation', $booking->confirmation_token) }}"
                               class="w-full flex items-center justify-center gap-2 py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                Contacter le loueur
                            </a>
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

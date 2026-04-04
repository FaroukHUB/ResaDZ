@extends('front.layouts.app')

@section('title', 'Réservation confirmée ' . $booking->reference . ' - ResaDZ')

@section('content')

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        <!-- Success Icon -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Réservation envoyée !</h1>
            <p class="text-gray-500 mt-2">Votre demande a bien été transmise au loueur.</p>
        </div>

        <!-- Booking Details -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-5">

            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <span class="text-sm text-gray-500">Référence</span>
                    <p class="text-xl font-bold text-gray-900">{{ $booking->reference }}</p>
                </div>
                <span class="px-3 py-1 bg-amber-100 text-amber-700 text-sm font-semibold rounded-full">En attente</span>
            </div>

            <!-- Vehicle -->
            <div class="flex items-center gap-4">
                @if($booking->vehicle->image)
                    <img src="{{ asset('storage/' . $booking->vehicle->image) }}" alt="{{ $booking->vehicle->full_name }}" class="w-20 h-14 rounded-lg object-cover">
                @endif
                <div>
                    <h3 class="font-bold text-gray-900">{{ $booking->vehicle->full_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $booking->vehicle->loueur->company_name ?? '' }}</p>
                </div>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-2 gap-4 bg-gray-50 rounded-xl p-4">
                <div>
                    <span class="text-xs text-gray-500">Début</span>
                    <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Fin</span>
                    <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Price -->
            <div class="bg-amber-50 rounded-xl p-4">
                <div class="flex justify-between items-center">
                    <span class="text-amber-800 font-medium">Total ({{ $booking->total_days }} jours)</span>
                    <div class="text-right">
                        <span class="text-2xl font-black text-amber-600">{{ $booking->getFormattedTotal() }}</span>
                        @if($booking->total_price_eur > 0 && $booking->currency !== 'EUR')
                            <span class="block text-sm text-amber-500">ou {{ $booking->getFormattedTotalEur() }}</span>
                        @endif
                    </div>
                </div>
                @if($booking->advance_amount > 0)
                    <div class="flex justify-between items-center mt-2 text-sm">
                        <span class="text-amber-700">Acompte à verser</span>
                        <span class="font-semibold text-amber-700">
                            {{ number_format($booking->advance_amount, 0, ',', ' ') }} {{ $booking->currency === 'EUR' ? '€' : 'DA' }}
                            @if($booking->advance_amount_eur > 0 && $booking->currency !== 'EUR')
                                <span class="text-amber-500">(ou {{ $booking->getFormattedAdvanceEur() }})</span>
                            @endif
                        </span>
                    </div>
                @endif
                @if($booking->deposit_amount > 0 || $booking->deposit_amount_eur > 0)
                    <div class="flex justify-between items-center mt-1 text-sm">
                        <span class="text-amber-700">Caution</span>
                        <span class="font-semibold text-amber-700">
                            @if($booking->deposit_amount > 0)
                                {{ number_format($booking->deposit_amount, 0, ',', ' ') }} DA
                            @endif
                            @if($booking->deposit_amount > 0 && $booking->deposit_amount_eur > 0)
                                <span class="text-amber-500">ou</span>
                            @endif
                            @if($booking->deposit_amount_eur > 0)
                                {{ $booking->getFormattedDepositEur() }}
                            @endif
                        </span>
                    </div>
                @endif
            </div>

            <!-- Timer & Payment Method -->
            @if($booking->advance_expires_at && $booking->advance_amount > 0)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                    <div class="text-center">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-blue-800 font-bold">Délai de paiement de l'acompte</p>
                        </div>
                        <div id="countdown" class="text-3xl font-black text-blue-700 my-3" data-expires="{{ $booking->advance_expires_at }}"></div>
                        <p class="text-blue-600 text-sm">
                            Vous avez jusqu'au <strong>{{ \Carbon\Carbon::parse($booking->advance_expires_at)->format('d/m/Y à H:i') }}</strong> pour régler l'acompte.
                        </p>
                        @if($booking->advance_payment_method)
                            @php
                                $methodLabels = [
                                    'cash' => 'Espèces (sur place)',
                                    'cib' => 'CIB (carte bancaire)',
                                    'dahabia' => 'Dahabia',
                                    'baridimob' => 'BaridiMob',
                                    'paypal' => 'PayPal',
                                    'bank_transfer' => 'Virement bancaire',
                                ];
                            @endphp
                            <p class="text-blue-700 text-sm mt-2 font-medium">
                                Mode de paiement choisi : {{ $methodLabels[$booking->advance_payment_method] ?? $booking->advance_payment_method }}
                            </p>
                        @endif
                        <p class="text-blue-500 text-xs mt-3">
                            Passé ce délai, la réservation sera automatiquement annulée et le véhicule remis en disponibilité.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Info -->
            <div class="text-center pt-4 border-t border-gray-100">
                <p class="text-gray-500 text-sm">Le loueur va vous contacter pour confirmer la réservation.</p>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('home') }}" class="text-amber-600 font-semibold hover:text-amber-700">&larr; Retour à l'accueil</a>
        </div>
    </div>

@if(\App\Models\Setting::get('facebook_pixel_id'))
<script>
fbq('track','Purchase',{content_name:'{{ addslashes($booking->vehicle->full_name ?? "") }}',value:{{ $booking->total_price ?? 0 }},currency:'{{ $booking->currency ?? "DZD" }}'});
</script>
@endif

@endsection

@section('scripts')
@if($booking->advance_expires_at && $booking->advance_amount > 0)
<script>
    const countdownEl = document.getElementById('countdown');
    const expires = new Date(countdownEl.dataset.expires).getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const diff = expires - now;

        if (diff <= 0) {
            countdownEl.textContent = 'Délai expiré';
            countdownEl.classList.remove('text-blue-700');
            countdownEl.classList.add('text-red-600');
            return;
        }

        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        countdownEl.textContent = `${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m ${String(seconds).padStart(2, '0')}s`;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
</script>
@endif
@endsection

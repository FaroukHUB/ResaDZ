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

            {{-- Online Payment Section --}}
            @php
                $loueur = $booking->vehicle?->loueur;
                $canPayOnline = $loueur && $loueur->stripe_account_id && $loueur->stripe_onboarding_complete;
                $advancePaid = $booking->advance_status === 'paid';
                $fullPaid = $booking->payment_status === 'paid';
                $hasAdvance = $booking->advance_amount > 0;
                $advanceEur = $booking->advance_amount_eur ?: round($booking->advance_amount * 0.0037, 2);
                $totalEur = $booking->total_price_eur ?: round($booking->total_price * 0.0037, 2);
            @endphp

            @if($canPayOnline && !$fullPaid)
                <div style="background: linear-gradient(135deg, #f0f4ff, #e8ecff); border: 2px solid #635BFF; border-radius: 16px; padding: 24px;">
                    <div class="flex items-center gap-3 mb-4">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="#635BFF"><path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/></svg>
                        <div>
                            <h3 style="font-weight: 700; color: #1e293b; font-size: 18px; margin: 0;">Payer en ligne</h3>
                            <p style="color: #6b7280; font-size: 13px; margin: 2px 0 0 0;">Paiement sécurisé par carte bancaire ou PayPal</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        {{-- Pay advance --}}
                        @if($hasAdvance && !$advancePaid)
                            <form action="{{ route('stripe.payment.checkout', $booking->reference) }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_type" value="advance">
                                <button type="submit" style="width: 100%; padding: 14px 24px; background: white; border: 2px solid #635BFF; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: between; gap: 12px; transition: all 0.2s;"
                                        onmouseover="this.style.background='#f8f7ff'" onmouseout="this.style.background='white'">
                                    <div style="text-align: left; flex: 1;">
                                        <p style="font-weight: 700; color: #1e293b; margin: 0; font-size: 15px;">Payer l'acompte</p>
                                        <p style="color: #6b7280; font-size: 13px; margin: 2px 0 0 0;">Aucune commission — 100% va au loueur</p>
                                    </div>
                                    <div style="text-align: right;">
                                        <p style="font-weight: 800; color: #635BFF; font-size: 20px; margin: 0;">{{ number_format($advanceEur, 2) }} €</p>
                                    </div>
                                </button>
                            </form>
                        @elseif($advancePaid && $hasAdvance)
                            <div style="padding: 14px 24px; background: #f0fdf4; border: 2px solid #22c55e; border-radius: 12px; display: flex; align-items: center; gap: 12px;">
                                <svg width="24" height="24" fill="none" stroke="#22c55e" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <p style="font-weight: 600; color: #166534; margin: 0;">Acompte payé ({{ number_format($advanceEur, 2) }} €)</p>
                            </div>
                        @endif

                        {{-- Pay full amount --}}
                        @if(!$fullPaid)
                            <form action="{{ route('stripe.payment.checkout', $booking->reference) }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_type" value="full">
                                <button type="submit" style="width: 100%; padding: 14px 24px; background: linear-gradient(135deg, #635BFF, #7B73FF); border: none; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: between; gap: 12px; transition: all 0.2s; box-shadow: 0 4px 14px rgba(99,91,255,0.3);"
                                        onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'">
                                    <div style="text-align: left; flex: 1;">
                                        <p style="font-weight: 700; color: white; margin: 0; font-size: 15px;">Payer la totalité</p>
                                        <p style="color: rgba(255,255,255,0.75); font-size: 13px; margin: 2px 0 0 0;">CB (Visa, Mastercard) ou PayPal</p>
                                    </div>
                                    <div style="text-align: right;">
                                        <p style="font-weight: 800; color: white; font-size: 20px; margin: 0;">{{ number_format($totalEur, 2) }} €</p>
                                    </div>
                                </button>
                            </form>
                        @endif
                    </div>

                    <div style="margin-top: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <svg width="14" height="14" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span style="font-size: 12px; color: #9ca3af;">Paiement sécurisé par Stripe — vos données bancaires ne transitent jamais par ResaDZ</span>
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

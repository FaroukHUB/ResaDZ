<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement PayPal - ResaDZ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @if($paypalMode === 'live')
        <script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency=USD"></script>
    @else
        <script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency=USD"></script>
    @endif
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-amber-500 to-yellow-400 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Boost Véhicule</h1>
            <p class="text-gray-600 mt-1">Paiement sécurisé via PayPal</p>
        </div>

        {{-- Order Summary --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <h2 class="font-semibold text-gray-900 mb-3">Récapitulatif</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Véhicule</span>
                    <span class="font-medium text-gray-900">{{ $boost->vehicle->brand->name }} {{ $boost->vehicle->model }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pack</span>
                    <span class="font-medium text-gray-900">{{ $package->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Durée</span>
                    <span class="font-medium text-gray-900">{{ $package->duration_days }} jours</span>
                </div>
                <hr class="my-2">
                <div class="flex justify-between text-base">
                    <span class="font-semibold text-gray-900">Total</span>
                    <div class="text-right">
                        <span class="font-bold text-amber-600">{{ number_format($amountDZD, 0, ',', ' ') }} DA</span>
                        <span class="block text-xs text-gray-500">(~{{ $amountUSD }} USD)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- PayPal Button Container --}}
        <div id="paypal-button-container" class="mb-4"></div>

        {{-- Loading State --}}
        <div id="loading" class="hidden text-center py-4">
            <svg class="animate-spin h-8 w-8 mx-auto text-amber-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-600 mt-2">Traitement du paiement...</p>
        </div>

        {{-- Cancel Link --}}
        <div class="text-center mt-4">
            <a href="{{ route('boost.paypal.cancel') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                Annuler et revenir
            </a>
        </div>

        {{-- Security Notice --}}
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-center gap-2 text-xs text-gray-500">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                <span>Paiement 100% sécurisé</span>
            </div>
        </div>
    </div>

    <script>
        paypal.Buttons({
            style: {
                layout: 'vertical',
                color: 'gold',
                shape: 'rect',
                label: 'paypal'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        description: 'Boost {{ $package->name }} - {{ $boost->vehicle->brand->name }} {{ $boost->vehicle->model }}',
                        amount: {
                            value: '{{ $amountUSD }}'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                document.getElementById('paypal-button-container').classList.add('hidden');
                document.getElementById('loading').classList.remove('hidden');

                return actions.order.capture().then(function(details) {
                    // Redirect to success page with order ID
                    window.location.href = '{{ route("boost.paypal.success") }}?paypal_order_id=' + data.orderID;
                });
            },
            onCancel: function(data) {
                window.location.href = '{{ route("boost.paypal.cancel") }}';
            },
            onError: function(err) {
                console.error('PayPal Error:', err);
                alert('Une erreur est survenue. Veuillez réessayer.');
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>

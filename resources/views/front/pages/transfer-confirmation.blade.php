@extends('front.layouts.app')

@section('title', 'Transfert confirmé - ResaDZ')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-lg w-full">
        <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
            {{-- Success Icon --}}
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-2xl font-black text-gray-900 mb-2">Demande envoyée !</h1>
            <p class="text-gray-500 mb-6">Votre demande de transfert a été transmise au prestataire.</p>

            {{-- Reference --}}
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <p class="text-sm text-gray-500">Référence</p>
                <p class="text-xl font-bold text-gray-900">{{ $transfer->reference }}</p>
            </div>

            {{-- Trajet --}}
            <div class="bg-amber-50 rounded-xl p-4 mb-6 text-left">
                <div class="flex items-center gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                        <div class="w-px h-4 bg-amber-300"></div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ $transfer->departure }}</div>
                        <div class="text-sm text-gray-500">{{ $transfer->destination }}</div>
                    </div>
                    @if($transfer->price > 0)
                        <div class="text-lg font-black text-amber-600">{{ number_format($transfer->price, 0, ',', ' ') }} DA</div>
                    @endif
                </div>
            </div>

            {{-- Details --}}
            <div class="text-left space-y-2 text-sm mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-500">Date</span>
                    <span class="font-medium text-gray-900">{{ $transfer->transfer_date->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Heure</span>
                    <span class="font-medium text-gray-900">{{ $transfer->transfer_time }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Passagers</span>
                    <span class="font-medium text-gray-900">{{ $transfer->passengers }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Bagages</span>
                    <span class="font-medium text-gray-900">{{ $transfer->luggage_count }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Prestataire</span>
                    <span class="font-medium text-gray-900">{{ $transfer->loueur->company_name }}</span>
                </div>
            </div>

            <div class="bg-blue-50 rounded-xl p-4 mb-6">
                <p class="text-sm text-blue-700">
                    <span class="font-semibold">Prochaine étape :</span>
                    Le chauffeur vous contactera par téléphone ou email pour confirmer votre transfert.
                </p>
            </div>

            <a href="{{ route('home') }}" class="inline-block px-8 py-3 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition">
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection

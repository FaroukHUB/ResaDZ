@extends('front.layouts.app')

@section('title', 'Mon espace - ResaDZ')

@section('content')

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mon espace</h1>
                <p class="text-gray-500 mt-1">Bienvenue, {{ $currentBooking->client_name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('client.support', $token) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium rounded-xl hover:bg-blue-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Support
                </a>
                <a href="{{ route('booking.client-confirmation', $token) }}" class="inline-flex items-center gap-2 text-sm text-primary-600 hover:text-primary-800 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Retour à ma réservation
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- Bookings List -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Mes réservations</h2>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($bookings as $booking)
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
                        $canMessage = in_array($booking->status, ['confirmed', 'active', 'completed']);
                    @endphp

                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Vehicle image -->
                            <div class="flex-shrink-0">
                                @if($booking->vehicle && $booking->vehicle->image)
                                    <img src="{{ asset('storage/' . $booking->vehicle->image) }}" alt="{{ $booking->vehicle->full_name ?? '' }}" class="w-28 h-20 rounded-xl object-cover">
                                @else
                                    <div class="w-28 h-20 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0H6.375c-.621 0-1.125-.504-1.125-1.125V11.25"/></svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $booking->vehicle->full_name ?? 'Véhicule' }}</h3>
                                        <p class="text-sm text-gray-500">{{ $booking->loueur->company_name ?? '' }}</p>
                                    </div>
                                    <span class="flex-shrink-0 px-3 py-1 {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-700' }} text-xs font-semibold rounded-full">
                                        {{ $statusLabels[$booking->status] ?? $booking->status }}
                                    </span>
                                </div>

                                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $booking->start_date->format('d/m/Y') }} - {{ $booking->end_date->format('d/m/Y') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        {{ $booking->reference }}
                                    </span>
                                    <span class="font-semibold text-gray-900">
                                        {{ number_format($booking->total_price, 0, ',', ' ') }} DA
                                    </span>
                                </div>

                                <!-- Actions -->
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a href="{{ route('booking.client-confirmation', $booking->confirmation_token) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Voir détails
                                    </a>
                                    @if($canMessage)
                                        <a href="{{ route('client.conversation', $booking->confirmation_token) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            Messages
                                            @php
                                                $unreadCount = $booking->conversation?->client_unread_count ?? 0;
                                            @endphp
                                            @if($unreadCount > 0)
                                                <span class="bg-white text-primary-600 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $unreadCount }}</span>
                                            @endif
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 text-gray-400 text-sm rounded-lg cursor-not-allowed" title="Messagerie disponible après confirmation">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            Messages
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-gray-500">Aucune réservation trouvée.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

@endsection

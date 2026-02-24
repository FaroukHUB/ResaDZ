@extends('front.layouts.app')

@section('title', 'Support - ResaDZ')

@section('content')

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('client.dashboard', $token) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Support</h1>
                    <p class="text-gray-500 mt-1">Contactez l'équipe ResaDZ</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- New Ticket Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="font-bold text-gray-900 mb-4">Nouvelle demande</h2>

                    <form action="{{ route('client.support.create', $token) }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                                <select name="category" required class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200 text-sm">
                                    @foreach($categories as $value => $label)
                                        <option value="{{ $value }}" {{ old('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Concernant une réservation ?</label>
                                <select name="booking_id" class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200 text-sm">
                                    <option value="">-- Non --</option>
                                    @php
                                        $myBookings = \App\Models\Booking::where('client_email', $booking->client_email)->orderByDesc('created_at')->limit(10)->get();
                                    @endphp
                                    @foreach($myBookings as $b)
                                        <option value="{{ $b->id }}" {{ old('booking_id') == $b->id ? 'selected' : '' }}>
                                            {{ $b->reference }} - {{ $b->vehicle->full_name ?? 'Véhicule' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sujet</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                                   class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200 text-sm"
                                   placeholder="Décrivez brièvement votre demande...">
                            @error('subject')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea name="message" required rows="5" maxlength="5000"
                                      class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200 text-sm"
                                      placeholder="Expliquez votre demande en détail...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition">
                            Envoyer ma demande
                        </button>
                    </form>
                </div>
            </div>

            <!-- Conversations List -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Mes demandes</h3>
                    </div>

                    <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                        @forelse($conversations as $conv)
                            <a href="{{ route('client.support.show', [$token, $conv->id]) }}"
                               class="block px-4 py-3 hover:bg-gray-50 transition {{ $conv->client_unread ? 'bg-primary-50' : '' }}">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate {{ $conv->client_unread ? 'font-bold' : '' }}">
                                            {{ $conv->subject }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $conv->category_label }}</p>
                                        @if($conv->latestMessage->first())
                                            <p class="text-xs text-gray-400 truncate mt-1">{{ Str::limit($conv->latestMessage->first()->content, 50) }}</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-{{ $conv->status_color }}-100 text-{{ $conv->status_color }}-700">
                                            {{ $conv->status_label }}
                                        </span>
                                        @if($conv->client_unread)
                                            <span class="w-2 h-2 bg-primary-600 rounded-full"></span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                <p class="text-sm text-gray-500">Aucune demande</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="mt-4 bg-blue-50 rounded-2xl p-4">
                    <p class="text-sm font-medium text-blue-900 mb-2">Besoin d'aide urgente ?</p>
                    <p class="text-xs text-blue-700">
                        Notre équipe répond généralement sous 24h. Pour les urgences liées à une location en cours, contactez directement le loueur.
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection

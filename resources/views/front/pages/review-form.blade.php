@extends('front.layouts.app')

@section('title', 'Laisser un avis - ResaDZ')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Laissez votre avis</h1>
        <p class="mt-2 text-gray-500">Votre expérience aide les futurs clients à choisir</p>
    </div>

    {{-- Booking summary --}}
    <div class="bg-gray-50 rounded-xl p-5 mb-8 flex items-center gap-4">
        @if($booking->vehicle && $booking->vehicle->image)
            <img src="{{ asset('storage/' . $booking->vehicle->image) }}" alt="" class="w-20 h-14 object-cover rounded-lg">
        @endif
        <div>
            <p class="font-semibold text-gray-900">
                @if($booking->vehicle && $booking->vehicle->brand){{ $booking->vehicle->brand->name }}@endif
                {{ $booking->vehicle->model ?? '' }}
            </p>
            <p class="text-sm text-gray-500">
                {{ $booking->loueur->company_name ?? '' }} &middot;
                {{ $booking->start_date->format('d/m/Y') }} → {{ $booking->end_date->format('d/m/Y') }}
            </p>
        </div>
    </div>

    {{-- Review form --}}
    <form action="{{ route('review.store', $booking->confirmation_token) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Overall rating --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Note globale <span class="text-red-500">*</span></label>
            <div class="flex items-center gap-2" x-data="{ rating: 0, hover: 0 }">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button"
                            @click="rating = {{ $i }}"
                            @mouseenter="hover = {{ $i }}"
                            @mouseleave="hover = 0"
                            class="transition-transform hover:scale-110">
                        <svg class="w-10 h-10 transition-colors"
                             :class="(hover >= {{ $i }} || rating >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-300'"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endfor
                <input type="hidden" name="rating_overall" x-model="rating">
                <span class="ml-3 text-sm text-gray-500" x-show="rating > 0" x-text="['', 'Mauvais', 'Moyen', 'Bien', 'Très bien', 'Excellent'][rating]"></span>
            </div>
            @error('rating_overall')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Detail ratings --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <p class="text-sm font-semibold text-gray-700 mb-4">Notes détaillées <span class="text-gray-400 font-normal">(optionnel)</span></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach([
                    'rating_vehicle' => 'État du véhicule',
                    'rating_communication' => 'Communication',
                    'rating_punctuality' => 'Ponctualité',
                    'rating_cleanliness' => 'Propreté',
                ] as $field => $label)
                    <div x-data="{ rating: 0 }">
                        <label class="block text-xs text-gray-500 mb-1">{{ $label }}</label>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}">
                                    <svg class="w-6 h-6 transition-colors"
                                         :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-200'"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="{{ $field }}" x-model="rating">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Comment --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Commentaire <span class="text-gray-400 font-normal">(optionnel)</span></label>
            <textarea name="comment" rows="4" maxlength="1000"
                      class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                      placeholder="Décrivez votre expérience avec ce loueur...">{{ old('comment') }}</textarea>
            @error('comment')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="w-full py-4 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg">
            Publier mon avis
        </button>

        <p class="text-xs text-gray-400 text-center">Votre avis sera publié avec votre prénom. Il aide la communauté ResaDZ.</p>
    </form>
</div>
@endsection

@section('head')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection

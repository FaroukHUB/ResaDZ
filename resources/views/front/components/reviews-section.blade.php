@php
    $reviews = $loueur->reviews()
        ->where('type', 'client_to_loueur')
        ->where('is_public', true)
        ->where('is_approved', true)
        ->with('reviewer')
        ->latest()
        ->limit(10)
        ->get();
@endphp

@if($reviews->count() > 0 || $loueur->total_reviews > 0)
<section class="mt-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Avis clients</h2>
        @if($loueur->rating > 0)
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($loueur->rating) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-lg font-bold text-gray-900">{{ number_format($loueur->rating, 1) }}</span>
                <span class="text-sm text-gray-500">({{ $loueur->total_reviews }} avis)</span>
            </div>
        @endif
    </div>

    <div class="space-y-4">
        @foreach($reviews as $review)
            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-900 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">
                                {{ $review->reviewer ? strtoupper(substr($review->reviewer->name, 0, 1)) : '?' }}
                            </span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">
                                {{ $review->reviewer ? $review->reviewer->name : 'Client vérifié' }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $review->rating_overall ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                </div>

                @if($review->comment)
                    <p class="mt-3 text-gray-600 text-sm leading-relaxed">{{ $review->comment }}</p>
                @endif

                {{-- Detail ratings --}}
                @if($review->rating_vehicle || $review->rating_communication || $review->rating_punctuality || $review->rating_cleanliness)
                    <div class="mt-3 flex flex-wrap gap-3">
                        @foreach([
                            'rating_vehicle' => 'Véhicule',
                            'rating_communication' => 'Communication',
                            'rating_punctuality' => 'Ponctualité',
                            'rating_cleanliness' => 'Propreté',
                        ] as $field => $label)
                            @if($review->$field)
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded-full">
                                    {{ $label }}
                                    <span class="font-semibold text-gray-700">{{ $review->$field }}/5</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Loueur response --}}
                @if($review->response)
                    <div class="mt-4 bg-gray-50 rounded-lg p-4 border-l-3 border-red-500">
                        <p class="text-xs font-semibold text-gray-500 mb-1">Réponse du loueur</p>
                        <p class="text-sm text-gray-600">{{ $review->response }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</section>
@endif

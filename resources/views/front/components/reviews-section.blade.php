@php
    $reviews = $loueur->reviews()
        ->where('type', 'client_to_loueur')
        ->where('is_public', true)
        ->where('is_approved', true)
        ->with('reviewer')
        ->latest()
        ->limit(10)
        ->get();

    // Calculer les moyennes par catégorie
    $allReviews = $loueur->reviews()
        ->where('type', 'client_to_loueur')
        ->where('is_public', true)
        ->where('is_approved', true)
        ->get();

    $avgVehicle = $allReviews->avg('rating_vehicle');
    $avgCommunication = $allReviews->avg('rating_communication');
    $avgPunctuality = $allReviews->avg('rating_punctuality');

    // Distribution des notes (1-5 étoiles)
    $ratingDistribution = [];
    for ($i = 5; $i >= 1; $i--) {
        $count = $allReviews->where('rating_overall', $i)->count();
        $ratingDistribution[$i] = [
            'count' => $count,
            'percentage' => $loueur->total_reviews > 0 ? ($count / $loueur->total_reviews) * 100 : 0,
        ];
    }
@endphp

@if($reviews->count() > 0 || $loueur->total_reviews > 0)
<section class="mt-12" id="avis">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Avis clients</h2>

    {{-- Rating Summary Card --}}
    @if($loueur->total_reviews > 0)
        <div class="bg-white border border-gray-200 rounded-2xl p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Left: Overall Rating --}}
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <div class="text-5xl font-bold text-gray-900">{{ number_format($loueur->rating, 1) }}</div>
                        <div class="flex items-center justify-center gap-0.5 mt-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= round($loueur->rating) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ $loueur->total_reviews }} avis</p>
                    </div>

                    {{-- Rating Distribution Bars --}}
                    <div class="flex-1 space-y-2">
                        @foreach($ratingDistribution as $stars => $data)
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 w-3">{{ $stars }}</span>
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-400 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400 w-6 text-right">{{ $data['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right: Category Ratings --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-700">Notes par catégorie</h3>
                    @if($avgVehicle)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">État du véhicule</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500 rounded-full" style="width: {{ ($avgVehicle / 5) * 100 }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 w-8">{{ number_format($avgVehicle, 1) }}</span>
                            </div>
                        </div>
                    @endif
                    @if($avgCommunication)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Communication</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ ($avgCommunication / 5) * 100 }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 w-8">{{ number_format($avgCommunication, 1) }}</span>
                            </div>
                        </div>
                    @endif
                    @if($avgPunctuality)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Ponctualité</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-purple-500 rounded-full" style="width: {{ ($avgPunctuality / 5) * 100 }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 w-8">{{ number_format($avgPunctuality, 1) }}</span>
                            </div>
                        </div>
                    @endif
                    @if(!$avgVehicle && !$avgCommunication && !$avgPunctuality)
                        <p class="text-sm text-gray-400">Pas assez de données détaillées</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Reviews List --}}
    @if($reviews->count() > 0)
        <div class="space-y-4">
            @foreach($reviews as $review)
                <div class="bg-white border border-gray-100 rounded-xl p-5 hover:border-gray-200 transition">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-gray-800 to-gray-900 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">
                                    {{ $review->reviewer ? strtoupper(substr($review->reviewer->name, 0, 1)) : '?' }}
                                </span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-gray-900 text-sm">
                                        {{ $review->reviewer ? $review->reviewer->name : 'Client vérifié' }}
                                    </p>
                                    <span class="inline-flex items-center gap-1 text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Vérifié
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400">{{ $review->created_at->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating_overall ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>

                    @if($review->comment)
                        <p class="mt-3 text-gray-600 text-sm leading-relaxed">{{ $review->comment }}</p>
                    @endif

                    {{-- Detail ratings --}}
                    @if($review->rating_vehicle || $review->rating_communication || $review->rating_punctuality)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($review->rating_vehicle)
                                <span class="inline-flex items-center gap-1 text-xs text-gray-600 bg-gray-50 px-2.5 py-1 rounded-lg">
                                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Véhicule {{ $review->rating_vehicle }}/5
                                </span>
                            @endif
                            @if($review->rating_communication)
                                <span class="inline-flex items-center gap-1 text-xs text-gray-600 bg-gray-50 px-2.5 py-1 rounded-lg">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    Communication {{ $review->rating_communication }}/5
                                </span>
                            @endif
                            @if($review->rating_punctuality)
                                <span class="inline-flex items-center gap-1 text-xs text-gray-600 bg-gray-50 px-2.5 py-1 rounded-lg">
                                    <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Ponctualité {{ $review->rating_punctuality }}/5
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Loueur response --}}
                    @if($review->response)
                        <div class="mt-4 bg-gray-50 rounded-xl p-4 border-l-4 border-red-500">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                <span class="text-xs font-semibold text-gray-700">Réponse de {{ $loueur->company_name }}</span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $review->response }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-gray-50 rounded-2xl">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <p class="text-gray-500">Pas encore d'avis pour ce loueur</p>
            <p class="text-sm text-gray-400 mt-1">Soyez le premier à laisser un avis après votre location !</p>
        </div>
    @endif
</section>
@endif

<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="welcome-banner" style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                            <x-heroicon-o-star class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">Avis clients</h2>
                            <p class="text-white/80 text-sm">Gerez votre reputation et repondez aux clients</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Guide Section --}}
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-light-bulb class="w-5 h-5 text-white" />
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-amber-900 dark:text-amber-100">Pourquoi repondre aux avis ?</p>
                    <ul class="text-sm text-amber-700 dark:text-amber-300 mt-2 space-y-1">
                        <li>• <strong>Avis positifs</strong> : Remerciez vos clients pour les fideliser et encourager de nouveaux avis</li>
                        <li>• <strong>Avis negatifs</strong> : Montrez votre professionnalisme en proposant une solution ou une explication</li>
                        <li>• <strong>Visibilite</strong> : Les agences qui repondent aux avis sont mieux classees sur ResaDZ</li>
                        <li>• <strong>Confiance</strong> : 90% des clients lisent les reponses avant de reserver</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Average Rating --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Note moyenne</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['average'] }}<span class="text-lg text-gray-400">/5</span></p>
                    </div>
                </div>
            </div>

            {{-- Total Reviews --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total avis</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Without Response --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sans réponse</p>
                        <p class="text-3xl font-bold {{ $stats['without_response'] > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ $stats['without_response'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Distribution --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Distribution</p>
                <div class="space-y-1.5">
                    @foreach($stats['distribution'] as $rating => $data)
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 w-4">{{ $rating }}</span>
                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-yellow-400 rounded-full transition-all" style="width: {{ $data['percentage'] }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 w-8 text-right">{{ $data['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Reviews List --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tous les avis</h2>
            </div>

            @if($reviews->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">Aucun avis pour le moment</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Les avis de vos clients apparaîtront ici</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($reviews as $review)
                        <div class="p-6">
                            {{-- Header --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-gray-700 to-gray-900 rounded-full flex items-center justify-center">
                                        <span class="text-white font-semibold">
                                            {{ $review->reviewer ? strtoupper(substr($review->reviewer->name, 0, 1)) : '?' }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ $review->reviewer?->name ?? 'Client' }}
                                        </p>
                                        <div class="flex items-center gap-2 text-sm text-gray-500">
                                            <span>{{ $review->created_at->format('d/m/Y') }}</span>
                                            @if($review->booking && $review->booking->vehicle)
                                                <span>•</span>
                                                <span>{{ $review->booking->vehicle->full_name ?? '' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $review->rating_overall ? 'text-yellow-400' : 'text-gray-200 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>

                            {{-- Comment --}}
                            @if($review->comment)
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">{{ $review->comment }}</p>
                            @endif

                            {{-- Detail ratings --}}
                            @if($review->rating_vehicle || $review->rating_communication || $review->rating_punctuality || $review->rating_cleanliness)
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach([
                                        'rating_vehicle' => 'Véhicule',
                                        'rating_communication' => 'Communication',
                                        'rating_punctuality' => 'Ponctualité',
                                        'rating_cleanliness' => 'Propreté',
                                    ] as $field => $label)
                                        @if($review->$field)
                                            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-full">
                                                {{ $label }}
                                                <span class="font-semibold text-gray-900 dark:text-white">{{ $review->$field }}/5</span>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            {{-- Existing Response --}}
                            @if($review->response)
                                <div class="bg-primary-50 dark:bg-primary-900/20 rounded-xl p-4 border-l-4 border-primary-500">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                        <span class="text-sm font-semibold text-primary-700 dark:text-primary-400">Votre réponse</span>
                                        <span class="text-xs text-gray-400">{{ $review->responded_at?->format('d/m/Y') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $review->response }}</p>
                                </div>
                            @elseif($respondingToId === $review->id)
                                {{-- Response Form --}}
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Votre réponse
                                    </label>
                                    <textarea
                                        wire:model="responseText"
                                        rows="3"
                                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                        placeholder="Remerciez le client ou apportez des précisions..."
                                    ></textarea>
                                    <div class="flex items-center justify-end gap-2 mt-3">
                                        <button
                                            type="button"
                                            wire:click="cancelResponse"
                                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                                        >
                                            Annuler
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="submitResponse"
                                            style="background: linear-gradient(135deg, #10b981 0%, #06b6d4 50%, #3b82f6 100%);"
                                            class="px-5 py-2 text-sm font-semibold text-white rounded-xl hover:opacity-90 transition-opacity"
                                        >
                                            Publier la réponse
                                        </button>
                                    </div>
                                </div>
                            @else
                                {{-- Response Button --}}
                                <button
                                    type="button"
                                    wire:click="startResponding({{ $review->id }})"
                                    class="mt-2 inline-flex items-center gap-2 text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    Répondre à cet avis
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>

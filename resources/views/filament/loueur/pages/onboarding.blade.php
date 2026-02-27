<x-filament-panels::page>
    <div class="max-w-5xl mx-auto">
        {{-- Header with progress --}}
        <div class="mb-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bienvenue sur ResaDZ !</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Configurez votre espace en quelques minutes pour commencer à recevoir des réservations.</p>
            </div>

            {{-- Progress bar --}}
            <div class="relative">
                <div class="flex justify-between mb-2">
                    @foreach($this->getStepInfo() as $step => $info)
                        <div class="flex flex-col items-center flex-1 {{ $step < $totalSteps ? 'relative' : '' }}">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300
                                {{ $step < $currentStep ? 'bg-green-500 text-white' : '' }}
                                {{ $step === $currentStep ? 'bg-primary-600 text-white ring-4 ring-primary-100 dark:ring-primary-900' : '' }}
                                {{ $step > $currentStep ? 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' : '' }}">
                                @if($step < $currentStep)
                                    <x-heroicon-s-check class="w-5 h-5" />
                                @else
                                    {{ $step }}
                                @endif
                            </div>
                            <span class="mt-2 text-xs font-medium text-center hidden sm:block
                                {{ $step === $currentStep ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $info['title'] }}
                            </span>
                            @if($step < $totalSteps)
                                <div class="hidden sm:block absolute top-5 left-1/2 w-full h-0.5 {{ $step < $currentStep ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }}" style="transform: translateX(50%);"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Current step info --}}
        @php
            $stepInfo = $this->getStepInfo()[$currentStep] ?? [];
        @endphp
        <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/40 rounded-lg flex items-center justify-center flex-shrink-0">
                    @if($stepInfo['icon'] ?? false)
                        <x-dynamic-component :component="$stepInfo['icon']" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    @endif
                </div>
                <div>
                    <h2 class="font-semibold text-primary-900 dark:text-primary-100">
                        Étape {{ $currentStep }}/{{ $totalSteps }} : {{ $stepInfo['title'] ?? '' }}
                    </h2>
                    <p class="text-sm text-primary-700 dark:text-primary-300 mt-0.5">
                        {{ $stepInfo['description'] ?? '' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Assistant tips per step --}}
        @php
            $tips = [
                1 => [
                    'title' => 'Conseil',
                    'text' => 'Un profil complet inspire confiance. Les clients préfèrent les agences avec une description claire et des coordonnées vérifiables.',
                ],
                2 => [
                    'title' => 'Important',
                    'text' => 'Sans zone de livraison, vos clients ne pourront pas réserver. Créez au moins une zone avec le lieu principal de prise en charge.',
                ],
                3 => [
                    'title' => 'Astuce',
                    'text' => 'Un acompte de 20-30% est recommandé pour sécuriser les réservations tout en restant attractif. Proposez plusieurs méthodes de paiement.',
                ],
                4 => [
                    'title' => 'Bon à savoir',
                    'text' => 'Les options populaires comme le siège bébé ou le GPS peuvent être offertes gratuitement pour vous démarquer de la concurrence.',
                ],
                5 => [
                    'title' => 'Conseil juridique',
                    'text' => 'Des conditions claires protègent à la fois vous et vos clients. Précisez au minimum l\'âge requis et les documents demandés.',
                ],
                6 => [
                    'title' => 'Marketing',
                    'text' => 'Les badges attirent l\'attention sur vos véhicules. N\'activez que ceux qui correspondent vraiment à vos services.',
                ],
                7 => [
                    'title' => 'Dernière étape !',
                    'text' => 'Activez au moins un canal de notification pour ne jamais manquer une réservation. Les notifications push sont les plus rapides.',
                ],
            ];
            $tip = $tips[$currentStep] ?? null;
        @endphp

        @if($tip)
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-amber-900 dark:text-amber-100 text-sm">{{ $tip['title'] }}</p>
                        <p class="text-sm text-amber-700 dark:text-amber-300 mt-0.5">{{ $tip['text'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit.prevent="{{ $currentStep === $totalSteps ? 'completeOnboarding' : 'nextStep' }}">
                {{ $this->form }}

                {{-- Navigation buttons --}}
                <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        @if($currentStep > 1)
                            <x-filament::button
                                type="button"
                                color="gray"
                                wire:click="previousStep"
                            >
                                <x-heroicon-s-arrow-left class="w-4 h-4 mr-1" />
                                Précédent
                            </x-filament::button>
                        @else
                            <button
                                type="button"
                                wire:click="skipOnboarding"
                                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline"
                            >
                                Configurer plus tard
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $currentStep }}/{{ $totalSteps }}
                        </span>

                        @if($currentStep === $totalSteps)
                            <x-filament::button type="submit" color="success">
                                <x-heroicon-s-check class="w-4 h-4 mr-1" />
                                Terminer la configuration
                            </x-filament::button>
                        @else
                            <x-filament::button type="submit">
                                Suivant
                                <x-heroicon-s-arrow-right class="w-4 h-4 ml-1" />
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Skip link at bottom --}}
        @if($currentStep > 1)
            <div class="text-center mt-4">
                <button
                    type="button"
                    wire:click="skipOnboarding"
                    class="text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 underline"
                >
                    Terminer plus tard et accéder au tableau de bord
                </button>
            </div>
        @endif
    </div>
</x-filament-panels::page>

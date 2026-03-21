<x-filament-panels::page>
    <div class="max-w-5xl mx-auto">
        {{-- Header with progress --}}
        <div class="mb-8">
            {{-- Welcome banner --}}
            <div class="welcome-banner mb-8">
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Bienvenue sur ResaDZ !</h1>
                        <p class="text-white/80 mt-1">Configurez votre espace en quelques minutes pour commencer a recevoir des reservations.</p>
                    </div>
                    <div class="hidden md:flex items-center gap-2 bg-white/20 backdrop-blur rounded-xl px-4 py-2">
                        <x-heroicon-o-sparkles class="w-5 h-5" />
                        <span class="font-semibold">{{ round(($currentStep / $totalSteps) * 100) }}% complete</span>
                    </div>
                </div>
            </div>

            {{-- Progress bar visual --}}
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    @foreach($this->getStepInfo() as $step => $info)
                        <div class="flex flex-col items-center flex-1 {{ $step < $totalSteps ? 'relative' : '' }}">
                            {{-- Step circle --}}
                            <div class="step-indicator transition-all duration-500
                                {{ $step < $currentStep ? 'step-indicator-completed' : '' }}
                                {{ $step === $currentStep ? 'step-indicator-active scale-110' : '' }}
                                {{ $step > $currentStep ? 'step-indicator-pending' : '' }}">
                                @if($step < $currentStep)
                                    <x-heroicon-s-check class="w-5 h-5" />
                                @else
                                    {{ $step }}
                                @endif
                            </div>
                            {{-- Step label --}}
                            <span class="mt-3 text-xs font-medium text-center hidden sm:block max-w-[80px]
                                {{ $step === $currentStep ? 'text-primary-600 dark:text-primary-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $info['title'] }}
                            </span>
                            {{-- Connector line --}}
                            @if($step < $totalSteps)
                                <div class="hidden sm:block absolute top-5 left-1/2 w-full h-1 rounded-full transition-all duration-500
                                    {{ $step < $currentStep ? 'bg-gradient-to-r from-green-500 to-emerald-400' : 'bg-gray-200 dark:bg-gray-700' }}"
                                    style="transform: translateX(50%);"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Progress percentage bar --}}
                <div class="progress-modern mt-4">
                    <div class="progress-modern-bar" style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
                </div>
            </div>
        </div>

        {{-- Current step info card --}}
        @php
            $stepInfo = $this->getStepInfo()[$currentStep] ?? [];
        @endphp
        <div class="stat-card border-l-4 border-primary-500 bg-gradient-to-r from-primary-50 to-white dark:from-primary-900/20 dark:to-gray-800 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                    @if($stepInfo['icon'] ?? false)
                        <x-dynamic-component :component="$stepInfo['icon']" class="w-7 h-7 text-white" />
                    @else
                        <span class="text-xl font-bold text-white">{{ $currentStep }}</span>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="badge-modern badge-info">Etape {{ $currentStep }}/{{ $totalSteps }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ $stepInfo['title'] ?? '' }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
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
                    'text' => 'Un profil complet inspire confiance. Les clients preferent les agences avec une description claire et des coordonnees verifiables.',
                    'icon' => 'heroicon-o-light-bulb',
                    'color' => 'amber',
                ],
                2 => [
                    'title' => 'Important',
                    'text' => 'Sans zone de livraison, vos clients ne pourront pas reserver. Creez au moins une zone avec le lieu principal de prise en charge.',
                    'icon' => 'heroicon-o-exclamation-triangle',
                    'color' => 'red',
                ],
                3 => [
                    'title' => 'Astuce',
                    'text' => 'Un acompte de 20-30% est recommande pour securiser les reservations tout en restant attractif. Proposez plusieurs methodes de paiement.',
                    'icon' => 'heroicon-o-currency-dollar',
                    'color' => 'green',
                ],
                4 => [
                    'title' => 'Bon a savoir',
                    'text' => 'Les options populaires comme le siege bebe ou le GPS peuvent etre offertes gratuitement pour vous demarquer de la concurrence.',
                    'icon' => 'heroicon-o-gift',
                    'color' => 'purple',
                ],
                5 => [
                    'title' => 'Conseil juridique',
                    'text' => 'Des conditions claires protegent a la fois vous et vos clients. Precisez au minimum l\'age requis et les documents demandes.',
                    'icon' => 'heroicon-o-scale',
                    'color' => 'blue',
                ],
                6 => [
                    'title' => 'Marketing',
                    'text' => 'Les badges attirent l\'attention sur vos vehicules. N\'activez que ceux qui correspondent vraiment a vos services.',
                    'icon' => 'heroicon-o-sparkles',
                    'color' => 'pink',
                ],
                7 => [
                    'title' => 'Derniere etape !',
                    'text' => 'Activez au moins un canal de notification pour ne jamais manquer une reservation. Les notifications push sont les plus rapides.',
                    'icon' => 'heroicon-o-bell',
                    'color' => 'indigo',
                ],
            ];
            $tip = $tips[$currentStep] ?? null;
            $tipColors = [
                'amber' => 'from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-amber-300 dark:border-amber-700',
                'red' => 'from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border-red-300 dark:border-red-700',
                'green' => 'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-green-300 dark:border-green-700',
                'purple' => 'from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 border-purple-300 dark:border-purple-700',
                'blue' => 'from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-blue-300 dark:border-blue-700',
                'pink' => 'from-pink-50 to-rose-50 dark:from-pink-900/20 dark:to-rose-900/20 border-pink-300 dark:border-pink-700',
                'indigo' => 'from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/20 border-indigo-300 dark:border-indigo-700',
            ];
            $iconColors = [
                'amber' => 'from-amber-500 to-orange-500',
                'red' => 'from-red-500 to-rose-500',
                'green' => 'from-green-500 to-emerald-500',
                'purple' => 'from-purple-500 to-violet-500',
                'blue' => 'from-blue-500 to-indigo-500',
                'pink' => 'from-pink-500 to-rose-500',
                'indigo' => 'from-indigo-500 to-violet-500',
            ];
        @endphp

        @if($tip)
            <div class="stat-card border bg-gradient-to-r {{ $tipColors[$tip['color']] ?? $tipColors['amber'] }} mb-6 animate-slide-up">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br {{ $iconColors[$tip['color']] ?? $iconColors['amber'] }} rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                        <x-dynamic-component :component="$tip['icon']" class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $tip['title'] }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $tip['text'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <div class="card-modern">
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
                                class="group"
                            >
                                <x-heroicon-s-arrow-left class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform" />
                                Precedent
                            </x-filament::button>
                        @else
                            <button
                                type="button"
                                wire:click="skipOnboarding"
                                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline decoration-dashed hover:decoration-solid transition-all"
                            >
                                Configurer plus tard
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex items-center gap-1">
                            @for($i = 1; $i <= $totalSteps; $i++)
                                <div class="w-2 h-2 rounded-full transition-all duration-300
                                    {{ $i < $currentStep ? 'bg-green-500' : '' }}
                                    {{ $i === $currentStep ? 'bg-primary-500 w-4' : '' }}
                                    {{ $i > $currentStep ? 'bg-gray-300 dark:bg-gray-600' : '' }}"></div>
                            @endfor
                        </div>

                        @if($currentStep === $totalSteps)
                            <x-filament::button type="submit" color="success" class="btn-gradient-success group">
                                <x-heroicon-s-check class="w-4 h-4 mr-1 group-hover:scale-110 transition-transform" />
                                Terminer la configuration
                            </x-filament::button>
                        @else
                            <x-filament::button type="submit" class="btn-gradient-primary group">
                                Suivant
                                <x-heroicon-s-arrow-right class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Skip link at bottom --}}
        @if($currentStep > 1)
            <div class="text-center mt-6">
                <button
                    type="button"
                    wire:click="skipOnboarding"
                    class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                >
                    <x-heroicon-o-forward class="w-4 h-4" />
                    Terminer plus tard et acceder au tableau de bord
                </button>
            </div>
        @endif
    </div>
</x-filament-panels::page>

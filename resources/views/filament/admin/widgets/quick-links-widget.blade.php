<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        @foreach($this->getCategories() as $category)
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Category Header --}}
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 bg-{{ $category['color'] }}-50 dark:bg-{{ $category['color'] }}-950/30">
                    <h3 class="text-sm font-semibold text-{{ $category['color'] }}-700 dark:text-{{ $category['color'] }}-400">
                        {{ $category['title'] }}
                    </h3>
                </div>

                {{-- Links --}}
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($category['links'] as $link)
                        <a href="{{ $link['url'] }}"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                            {{-- Icon --}}
                            <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-{{ $category['color'] }}-100 dark:bg-{{ $category['color'] }}-900/50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                @svg($link['icon'], 'w-5 h-5 text-' . $category['color'] . '-600 dark:text-' . $category['color'] . '-400')
                            </div>

                            {{-- Text --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $link['label'] }}
                                    </span>
                                    @if(!empty($link['badge']))
                                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold rounded-full bg-{{ $link['badgeColor'] ?? 'gray' }}-100 text-{{ $link['badgeColor'] ?? 'gray' }}-700 dark:bg-{{ $link['badgeColor'] ?? 'gray' }}-900/50 dark:text-{{ $link['badgeColor'] ?? 'gray' }}-400">
                                            {{ $link['badge'] }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $link['description'] }}
                                </p>
                            </div>

                            {{-- Arrow --}}
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-{{ $category['color'] }}-500 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>

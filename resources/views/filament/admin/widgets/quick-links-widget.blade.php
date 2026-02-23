<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        {{-- Gestion --}}
        <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Gestion</h3>
            </div>
            <div class="space-y-1">
                <a href="/admin/loueurs" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                        @svg('heroicon-o-building-storefront', 'w-4 h-4 text-amber-600 dark:text-amber-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Loueurs</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \App\Models\Loueur::count() }} loueurs</p>
                    </div>
                </a>
                <a href="/admin/bookings" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                        @svg('heroicon-o-calendar-days', 'w-4 h-4 text-amber-600 dark:text-amber-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Réservations</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \App\Models\Booking::count() }} réservations</p>
                    </div>
                </a>
                <a href="/admin/revenues" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                        @svg('heroicon-o-currency-dollar', 'w-4 h-4 text-amber-600 dark:text-amber-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Revenus</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Suivi financier</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Catalogue --}}
        <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Catalogue</h3>
            </div>
            <div class="space-y-1">
                <a href="/admin/vehicles" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        @svg('heroicon-o-truck', 'w-4 h-4 text-blue-600 dark:text-blue-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Véhicules</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \App\Models\Vehicle::count() }} véhicules</p>
                    </div>
                </a>
                <a href="/admin/brands" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        @svg('heroicon-o-bookmark', 'w-4 h-4 text-blue-600 dark:text-blue-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Marques</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \App\Models\Brand::count() }} marques</p>
                    </div>
                </a>
                <a href="/admin/categories" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        @svg('heroicon-o-tag', 'w-4 h-4 text-blue-600 dark:text-blue-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Catégories</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \App\Models\Category::count() }} catégories</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Contenu --}}
        <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Contenu</h3>
            </div>
            <div class="space-y-1">
                <a href="/admin/blog-posts" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        @svg('heroicon-o-newspaper', 'w-4 h-4 text-emerald-600 dark:text-emerald-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Actualités</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \App\Models\BlogPost::count() }} articles</p>
                    </div>
                </a>
                <a href="/admin/hero-slides" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        @svg('heroicon-o-photo', 'w-4 h-4 text-emerald-600 dark:text-emerald-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Slides Hero</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Carrousel accueil</p>
                    </div>
                </a>
                <a href="/admin/popups" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        @svg('heroicon-o-window', 'w-4 h-4 text-emerald-600 dark:text-emerald-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Popups</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \App\Models\Popup::count() }} popups</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Analytics --}}
        <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Analytics</h3>
            </div>
            <div class="space-y-1">
                <a href="/admin/statistics" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center">
                        @svg('heroicon-o-chart-bar', 'w-4 h-4 text-purple-600 dark:text-purple-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Statistiques</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Analyses détaillées</p>
                    </div>
                </a>
                <a href="/admin/leads" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center">
                        @svg('heroicon-o-user-plus', 'w-4 h-4 text-purple-600 dark:text-purple-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Leads</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \App\Models\Lead::count() }} contacts</p>
                    </div>
                </a>
                <a href="/admin/newsletter-subscribers" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center">
                        @svg('heroicon-o-envelope', 'w-4 h-4 text-purple-600 dark:text-purple-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Newsletter</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \App\Models\NewsletterSubscriber::count() }} abonnés</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Configuration --}}
        <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Configuration</h3>
            </div>
            <div class="space-y-1">
                <a href="/admin/platform-settings" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-500/20 flex items-center justify-center">
                        @svg('heroicon-o-cog-8-tooth', 'w-4 h-4 text-gray-600 dark:text-gray-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Paramètres</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Configuration générale</p>
                    </div>
                </a>
                <a href="/admin/lead-capture-settings" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-500/20 flex items-center justify-center">
                        @svg('heroicon-o-cursor-arrow-ripple', 'w-4 h-4 text-gray-600 dark:text-gray-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Lead Capture</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Popups & formulaires</p>
                    </div>
                </a>
                <a href="/admin/boost-packages" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-500/20 flex items-center justify-center">
                        @svg('heroicon-o-rocket-launch', 'w-4 h-4 text-gray-600 dark:text-gray-400')
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Packs Boost</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ \App\Models\BoostPackage::count() }} packs</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>

<a href="{{ route('loueur.show', $loueur->slug) }}" class="group block bg-white border {{ $featured ?? false ? 'border-amber-200 ring-2 ring-amber-100' : 'border-gray-200' }} rounded-2xl p-6 hover:shadow-xl hover:border-green-200 transition-all hover:-translate-y-1 h-full relative">
    @if($featured ?? false)
        <div class="absolute -top-2 -right-2 w-8 h-8 bg-amber-400 rounded-full flex items-center justify-center shadow-lg">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
        </div>
    @endif

    <div class="flex items-center gap-4">
        @if($loueur->logo)
            <img src="{{ asset('storage/' . $loueur->logo) }}" alt="{{ $loueur->company_name }}" class="w-14 h-14 rounded-xl object-cover">
        @else
            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex flex-col items-center justify-center shadow-sm">
                <span class="text-white font-black text-[10px] leading-none">PARTENAIRE</span>
                <span class="text-white font-black text-xs leading-tight">ResaDZ</span>
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-gray-900 group-hover:text-green-600 transition truncate">{{ $loueur->company_name }}</h3>
            <p class="text-sm text-gray-500">{{ $loueur->city ?? $loueur->wilaya ?? 'Algerie' }}</p>
            @if($loueur->total_reviews > 0)
                <div class="flex items-center gap-1.5 mt-1">
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-3.5 h-3.5 {{ $i <= round($loueur->rating) ? 'text-amber-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ number_format($loueur->rating, 1) }}</span>
                    <span class="text-xs text-gray-400">({{ $loueur->total_reviews }})</span>
                </div>
            @else
                <p class="text-xs text-gray-400 mt-1">Nouveau partenaire</p>
            @endif
        </div>
    </div>
    <div class="mt-4 flex items-center justify-between">
        <span class="text-sm text-gray-500">{{ $loueur->vehicles_count }} vehicule{{ $loueur->vehicles_count > 1 ? 's' : '' }}</span>
        <div class="flex items-center gap-2">
            @if($loueur->rating >= 4.5 && $loueur->total_reviews >= 10)
                <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full border border-amber-200">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Top
                </span>
            @endif
            @if($loueur->is_verified)
                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full border border-green-200">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Verifie
                </span>
            @endif
        </div>
    </div>
</a>

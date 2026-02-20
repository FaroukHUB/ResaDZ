@php
    use App\Models\Popup;

    $pageType = $pageType ?? null;
    $isMobile = request()->header('User-Agent') && preg_match('/Mobile|Android|iPhone/i', request()->header('User-Agent'));

    $popup = Popup::active()
        ->ordered()
        ->when($isMobile, fn($q) => $q->where('show_on_mobile', true))
        ->when(!$isMobile, fn($q) => $q->where('show_on_desktop', true))
        ->first();

    // Check if popup should show on this page
    if ($popup && !$popup->shouldShowOnPage($pageType)) {
        $popup = null;
    }
@endphp

@if($popup)
<div
    x-data="popupManager({{ $popup->id }}, '{{ $popup->frequency }}', {{ $popup->delay_seconds * 1000 }})"
    x-show="isVisible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak
    class="fixed inset-0 z-[100] flex {{ $popup->position_classes }}"
    @keydown.escape.window="closePopup()"
>
    {{-- Overlay --}}
    @if($popup->show_overlay)
    <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="{{ $popup->closable ? 'closePopup()' : '' }}"
    ></div>
    @endif

    {{-- Popup Content --}}
    <div
        class="relative {{ $popup->size_classes }} w-full mx-4 rounded-2xl shadow-2xl overflow-hidden"
        style="background-color: {{ $popup->background_color ?? '#ffffff' }};"
        x-transition:enter="transition ease-out duration-300 delay-100"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        @click.stop
    >
        {{-- Close Button --}}
        @if($popup->closable)
        <button
            @click="closePopup()"
            class="absolute top-3 right-3 z-10 p-2 rounded-full bg-black/10 hover:bg-black/20 transition"
            style="color: {{ $popup->text_color ?? '#1f2937' }};"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        @endif

        {{-- Image --}}
        @if($popup->image)
        <div class="aspect-video w-full overflow-hidden">
            <img
                src="{{ asset('storage/' . $popup->image) }}"
                alt="{{ $popup->title ?? $popup->name }}"
                class="w-full h-full object-cover"
            >
        </div>
        @endif

        {{-- Content --}}
        <div class="p-6" style="color: {{ $popup->text_color ?? '#1f2937' }};">
            @if($popup->title)
            <h3 class="text-xl font-bold mb-3">{{ $popup->title }}</h3>
            @endif

            @if($popup->content)
            <p class="text-sm opacity-80 leading-relaxed mb-4">{{ $popup->content }}</p>
            @endif

            {{-- Button --}}
            @if($popup->button_text && $popup->button_url)
            <a
                href="{{ $popup->button_url }}"
                @click="trackClick()"
                class="inline-flex items-center justify-center px-6 py-3 rounded-lg font-semibold text-white transition hover:opacity-90"
                style="background-color: {{ $popup->button_color ?? '#dc2626' }};"
            >
                {{ $popup->button_text }}
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endif
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
function popupManager(popupId, frequency, delay) {
    return {
        isVisible: false,
        popupId: popupId,
        frequency: frequency,

        init() {
            // Check if should show based on frequency
            if (!this.shouldShowBasedOnFrequency()) {
                return;
            }

            // Show after delay
            setTimeout(() => {
                this.showPopup();
            }, delay);
        },

        shouldShowBasedOnFrequency() {
            const storageKey = `popup_${this.popupId}`;
            const stored = localStorage.getItem(storageKey);

            if (!stored) return true;

            const data = JSON.parse(stored);
            const now = Date.now();

            switch (this.frequency) {
                case 'always':
                    return true;
                case 'once_per_session':
                    return !sessionStorage.getItem(storageKey);
                case 'once_per_day':
                    // Check if 24 hours have passed
                    return (now - data.timestamp) > (24 * 60 * 60 * 1000);
                case 'once_ever':
                    return false;
                default:
                    return true;
            }
        },

        markAsShown() {
            const storageKey = `popup_${this.popupId}`;
            const data = { timestamp: Date.now(), shown: true };

            localStorage.setItem(storageKey, JSON.stringify(data));
            sessionStorage.setItem(storageKey, 'true');
        },

        showPopup() {
            this.isVisible = true;
            this.markAsShown();
            this.trackView();
        },

        closePopup() {
            this.isVisible = false;
        },

        trackView() {
            fetch(`/api/popup/${this.popupId}/view`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            }).catch(() => {});
        },

        trackClick() {
            fetch(`/api/popup/${this.popupId}/click`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            }).catch(() => {});
        }
    };
}
</script>
@endif

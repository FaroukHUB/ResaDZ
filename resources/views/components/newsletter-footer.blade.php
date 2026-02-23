<div x-data="newsletterFooter()">
    <form @submit.prevent="submit()">
        <div class="flex gap-2">
            <input
                type="email"
                x-model="email"
                placeholder="Votre email"
                class="flex-1 px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 text-sm transition"
                required
            >
            <button
                type="submit"
                :disabled="loading"
                class="px-5 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-500 transition disabled:opacity-50 text-sm whitespace-nowrap"
            >
                <span x-show="!loading" class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </span>
                <span x-show="loading">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </button>
        </div>
        <p x-show="success" x-transition class="text-green-400 text-xs mt-3 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            Inscription réussie !
        </p>
        <p x-show="error" x-transition class="text-red-400 text-xs mt-3" x-text="error"></p>
    </form>
</div>

<script>
function newsletterFooter() {
    return {
        email: '',
        loading: false,
        success: false,
        error: '',

        async submit() {
            this.loading = true;
            this.error = '';
            this.success = false;

            try {
                const response = await fetch('/api/marketing/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({
                        email: this.email,
                        source: 'footer'
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.success = true;
                    this.email = '';
                } else {
                    this.error = data.message || 'Une erreur est survenue';
                }
            } catch (e) {
                this.error = 'Une erreur est survenue';
            }

            this.loading = false;
        }
    };
}
</script>

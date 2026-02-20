<div x-data="newsletterFooter()" class="bg-gray-800 rounded-xl p-6">
    <h4 class="text-white font-semibold mb-2">Newsletter</h4>
    <p class="text-gray-400 text-sm mb-4">Recevez nos meilleures offres</p>

    <form @submit.prevent="submit()">
        <div class="flex gap-2">
            <input
                type="email"
                x-model="email"
                placeholder="Votre email"
                class="flex-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm"
                required
            >
            <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition disabled:opacity-50 text-sm"
            >
                <span x-show="!loading">OK</span>
                <span x-show="loading">...</span>
            </button>
        </div>
        <p x-show="success" x-transition class="text-green-400 text-xs mt-2">✓ Inscription réussie !</p>
        <p x-show="error" x-transition class="text-red-400 text-xs mt-2" x-text="error"></p>
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

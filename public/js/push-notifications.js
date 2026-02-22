// Push Notifications Client - ResaDZ

class PushNotificationManager {
    constructor() {
        this.swRegistration = null;
        this.publicKey = null;
    }

    async init() {
        if (!('serviceWorker' in navigator)) {
            console.log('[Push] Service workers not supported');
            return false;
        }

        if (!('PushManager' in window)) {
            console.log('[Push] Push notifications not supported');
            return false;
        }

        try {
            // Register service worker
            this.swRegistration = await navigator.serviceWorker.register('/sw.js');
            console.log('[Push] Service Worker registered');

            // Get VAPID public key
            const response = await fetch('/api/push/public-key');
            const data = await response.json();
            this.publicKey = data.publicKey;

            return true;
        } catch (error) {
            console.error('[Push] Init error:', error);
            return false;
        }
    }

    async isSubscribed() {
        if (!this.swRegistration) return false;

        const subscription = await this.swRegistration.pushManager.getSubscription();
        return subscription !== null;
    }

    async subscribe() {
        if (!this.swRegistration || !this.publicKey) {
            throw new Error('Push notifications not initialized');
        }

        // Request notification permission
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            throw new Error('Notification permission denied');
        }

        // Convert VAPID key to Uint8Array
        const applicationServerKey = this.urlBase64ToUint8Array(this.publicKey);

        // Subscribe to push
        const subscription = await this.swRegistration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: applicationServerKey
        });

        // Send subscription to server
        const response = await fetch('/api/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            credentials: 'include',
            body: JSON.stringify(subscription.toJSON())
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || 'Failed to save subscription');
        }

        console.log('[Push] Subscribed successfully');
        return true;
    }

    async unsubscribe() {
        if (!this.swRegistration) return;

        const subscription = await this.swRegistration.pushManager.getSubscription();
        if (!subscription) return;

        // Unsubscribe from push
        await subscription.unsubscribe();

        // Notify server
        await fetch('/api/push/unsubscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            credentials: 'include',
            body: JSON.stringify({ endpoint: subscription.endpoint })
        });

        console.log('[Push] Unsubscribed successfully');
    }

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
}

// Global instance
window.pushManager = new PushNotificationManager();

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    const btn = document.getElementById('push-notification-toggle');
    if (!btn) return;

    const initialized = await window.pushManager.init();
    if (!initialized) {
        btn.disabled = true;
        btn.textContent = 'Non supporté';
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        return;
    }

    const updateButton = async () => {
        const subscribed = await window.pushManager.isSubscribed();
        if (subscribed) {
            btn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>Notifications activées';
            btn.classList.remove('bg-amber-500', 'hover:bg-amber-600');
            btn.classList.add('bg-green-600', 'hover:bg-green-700');
            btn.dataset.subscribed = 'true';
        } else {
            btn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>Activer les notifications';
            btn.classList.remove('bg-green-600', 'hover:bg-green-700');
            btn.classList.add('bg-amber-500', 'hover:bg-amber-600');
            btn.dataset.subscribed = 'false';
        }
    };

    await updateButton();

    btn.addEventListener('click', async () => {
        btn.disabled = true;
        try {
            if (btn.dataset.subscribed === 'true') {
                await window.pushManager.unsubscribe();
            } else {
                await window.pushManager.subscribe();
            }
            await updateButton();
        } catch (error) {
            console.error('[Push] Error:', error);
            alert(error.message || 'Une erreur est survenue');
        }
        btn.disabled = false;
    });
});

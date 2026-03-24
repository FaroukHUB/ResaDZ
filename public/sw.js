// Service Worker for ResaDZ PWA — Cache offline + Push Notifications

const CACHE_NAME = 'resadz-v1';
const STATIC_ASSETS = [
    '/assets/favicon.png',
    '/assets/favicon-96x96.png',
    '/manifest.json',
];

// ─── INSTALL: Pre-cache static assets ───
self.addEventListener('install', (event) => {
    console.log('[SW] Installing — caching static assets');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// ─── ACTIVATE: Clean old caches ───
self.addEventListener('activate', (event) => {
    console.log('[SW] Activated');
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        ).then(() => clients.claim())
    );
});

// ─── FETCH: Network-first with cache fallback ───
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Skip non-GET, API calls, Livewire, and cross-origin
    if (
        request.method !== 'GET' ||
        request.url.includes('/api/') ||
        request.url.includes('/livewire/') ||
        request.url.includes('chrome-extension') ||
        !request.url.startsWith(self.location.origin)
    ) {
        return;
    }

    event.respondWith(
        fetch(request)
            .then((response) => {
                // Cache successful responses for static assets
                if (response.ok && (
                    request.url.includes('/assets/') ||
                    request.url.includes('/build/') ||
                    request.url.includes('/css/') ||
                    request.url.includes('/js/')
                )) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => caches.match(request))
    );
});

// ─── PUSH: Receive and display push notifications ───
self.addEventListener('push', (event) => {
    console.log('[SW] Push notification received');

    let data = {
        title: 'Nouvelle notification',
        body: 'Vous avez une nouvelle notification',
        icon: '/assets/favicon.png',
        badge: '/assets/favicon-96x96.png',
        tag: 'notification',
        requireInteraction: true,
        data: {}
    };

    if (event.data) {
        try {
            const payload = event.data.json();
            data = { ...data, ...payload };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/assets/favicon.png',
        badge: data.badge || '/assets/favicon-96x96.png',
        tag: data.tag || 'notification',
        requireInteraction: data.requireInteraction !== false,
        vibrate: [200, 100, 200],
        data: data.data || {},
        actions: data.actions || []
    };

    // Add default actions for booking notifications
    if (data.data && data.data.type === 'booking') {
        options.actions = [
            { action: 'view', title: 'Voir la réservation' },
            { action: 'dismiss', title: 'Fermer' }
        ];
    }

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// ─── NOTIFICATION CLICK: Navigate to the right page ───
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked');

    event.notification.close();

    const data = event.notification.data || {};
    let url = data.url || '/loueur/dashboard';

    if (event.action === 'view' && data.url) {
        url = data.url;
    } else if (event.action === 'dismiss') {
        return;
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Try to focus an existing window
                for (const client of clientList) {
                    if ((client.url.includes('/loueur') || client.url.includes('/admin')) && 'focus' in client) {
                        client.navigate(url);
                        return client.focus();
                    }
                }
                // Open a new window if no existing one found
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});

self.addEventListener('notificationclose', (event) => {
    console.log('[SW] Notification closed');
});

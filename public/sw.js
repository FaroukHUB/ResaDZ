// Service Worker for Push Notifications - ResaDZ

self.addEventListener('install', (event) => {
    console.log('[SW] Service Worker installed');
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log('[SW] Service Worker activated');
    event.waitUntil(clients.claim());
});

self.addEventListener('push', (event) => {
    console.log('[SW] Push notification received');

    let data = {
        title: 'Nouvelle notification',
        body: 'Vous avez une nouvelle notification',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
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
        icon: data.icon || '/favicon.ico',
        badge: data.badge || '/favicon.ico',
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
                    if (client.url.includes('/loueur') && 'focus' in client) {
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

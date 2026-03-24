<?php

namespace App\Traits;

trait HasPwa
{
    protected function renderPwaHead(): string
    {
        return '
            <link rel="manifest" href="/manifest.json">
            <meta name="theme-color" content="#FF6B2C">
            <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="apple-mobile-web-app-status-bar-style" content="default">
            <meta name="apple-mobile-web-app-title" content="ResaDZ">
            <link rel="apple-touch-icon" href="/assets/favicon.png">
        ';
    }

    protected function renderPwaInstallCard(): string
    {
        // Only show on dashboard pages
        $path = request()->path();
        if (!str_ends_with($path, '/dashboard') && !preg_match('#^(loueur|admin|chauffeur)$#', $path)) {
            return '';
        }

        return view('filament.partials.pwa-install-card')->render();
    }

    protected function renderPwaScripts(): string
    {
        $vapidPublicKey = config('services.webpush.public_key', '');

        return <<<HTML
        <script>
        // ─── PWA: Service Worker Registration ───
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(function(reg) {
                        console.log('[PWA] Service Worker registered, scope:', reg.scope);

                        // Auto-subscribe to push if permission already granted
                        if (Notification.permission === 'granted' && '{$vapidPublicKey}') {
                            reg.pushManager.getSubscription().then(function(sub) {
                                if (!sub) {
                                    subscribeToPush(reg);
                                }
                            });
                        }
                    })
                    .catch(function(err) {
                        console.error('[PWA] SW registration failed:', err);
                    });
            });
        }

        function subscribeToPush(reg) {
            const vapidKey = '{$vapidPublicKey}';
            if (!vapidKey) return;

            const applicationServerKey = urlBase64ToUint8Array(vapidKey);
            reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: applicationServerKey
            }).then(function(subscription) {
                const key = subscription.getKey('p256dh');
                const auth = subscription.getKey('auth');

                fetch('/api/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        endpoint: subscription.endpoint,
                        p256dh_key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                        auth_token: auth ? btoa(String.fromCharCode.apply(null, new Uint8Array(auth))) : null,
                    })
                }).then(function() {
                    console.log('[PWA] Push subscription saved');
                }).catch(function(err) {
                    console.error('[PWA] Failed to save subscription:', err);
                });
            }).catch(function(err) {
                console.error('[PWA] Push subscribe failed:', err);
            });
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
        </script>
        HTML;
    }
}

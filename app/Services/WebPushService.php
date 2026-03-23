<?php

namespace App\Services;

use App\Models\Loueur;
use App\Models\PushSubscription;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('services.webpush.public_key'),
                'privateKey' => config('services.webpush.private_key'),
            ],
        ]);
    }

    /**
     * Send a push notification to a specific loueur.
     */
    public function sendToLoueur(Loueur $loueur, string $title, string $body, array $data = []): int
    {
        $subscriptions = PushSubscription::where('loueur_id', $loueur->id)->get();
        $sent = 0;

        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->p256dh_key,
                'authToken' => $sub->auth_token,
            ]);

            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'icon' => $this->getFaviconUrl(),
                'badge' => $this->getFaviconUrl(),
                'data' => $data,
                'tag' => $data['tag'] ?? 'notification',
                'requireInteraction' => true,
            ]);

            $this->webPush->queueNotification($subscription, $payload);
            $sent++;
        }

        // Send all queued notifications
        foreach ($this->webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                // Remove invalid subscriptions
                $endpoint = $report->getRequest()->getUri()->__toString();
                PushSubscription::where('endpoint', $endpoint)->delete();
                $sent--;
            }
        }

        return $sent;
    }

    /**
     * Send booking notification to loueur.
     */
    public function sendBookingNotification(Loueur $loueur, array $bookingData): int
    {
        $vehicle = $bookingData['vehicle'] ?? 'Véhicule';
        $dates = $bookingData['dates'] ?? '';
        $amount = $bookingData['amount'] ?? '';

        return $this->sendToLoueur(
            $loueur,
            'Nouvelle réservation !',
            "{$vehicle}\n{$dates}\n{$amount}",
            [
                'type' => 'booking',
                'booking_id' => $bookingData['booking_id'] ?? null,
                'url' => $bookingData['url'] ?? '/loueur/bookings',
                'tag' => 'booking-' . ($bookingData['booking_id'] ?? time()),
            ]
        );
    }

    /**
     * Get VAPID public key for client-side subscription.
     */
    public static function getPublicKey(): string
    {
        return config('services.webpush.public_key', '');
    }

    protected function getFaviconUrl(): string
    {
        $favicon = Setting::get('favicon', '');
        if ($favicon) {
            return Storage::url($favicon);
        }

        $logoLight = Setting::get('logo_light', '');
        if ($logoLight) {
            return Storage::url($logoLight);
        }

        return '/assets/favicon.png';
    }
}

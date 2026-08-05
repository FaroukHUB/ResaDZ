<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\Loueur;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CalendarController extends Controller
{
    /**
     * Generate iCal feed for a loueur's calendar.
     * Can be subscribed to in Google Calendar, Apple Calendar, Outlook, etc.
     */
    public function icalFeed(string $token)
    {
        // Decode token to get loueur ID
        $loueur = Loueur::where('id', $this->decodeToken($token))->first();

        if (!$loueur) {
            abort(404, 'Calendar not found');
        }

        $events = $this->getEvents($loueur);

        $ical = $this->generateIcal($loueur, $events);

        return response($ical, 200)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . Str::slug($loueur->company_name) . '-calendar.ics"');
    }

    /**
     * Generate a shareable calendar token for a loueur.
     */
    public static function generateToken(int $loueurId): string
    {
        // Simple token: base64(loueur_id . '-' . secret)
        $secret = config('app.key');
        $data = $loueurId . '-' . substr(md5($loueurId . $secret), 0, 16);
        return base64_encode($data);
    }

    /**
     * Decode the calendar token to get loueur ID.
     */
    private function decodeToken(string $token): ?int
    {
        try {
            $decoded = base64_decode($token);
            $parts = explode('-', $decoded);
            if (count($parts) < 2) {
                return null;
            }

            $loueurId = (int) $parts[0];
            $secret = config('app.key');
            $expectedHash = substr(md5($loueurId . $secret), 0, 16);

            if ($parts[1] === $expectedHash) {
                return $loueurId;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get all calendar events for a loueur.
     */
    private function getEvents(Loueur $loueur): array
    {
        $events = [];

        // Get bookings (past 3 months to future 12 months)
        $startDate = now()->subMonths(3);
        $endDate = now()->addMonths(12);

        $bookings = Booking::where('loueur_id', $loueur->id)
            ->where('end_date', '>=', $startDate)
            ->where('start_date', '<=', $endDate)
            ->whereIn('status', ['pending', 'confirmed', 'active', 'completed'])
            ->with('vehicle')
            ->get();

        foreach ($bookings as $booking) {
            $events[] = [
                'uid' => 'booking-' . $booking->id . '@' . parse_url(config('app.url'), PHP_URL_HOST),
                'summary' => 'Réservation: ' . $booking->client_name . ' - ' . ($booking->vehicle->full_name ?? 'Véhicule'),
                'description' => implode('\n', [
                    'Référence: ' . $booking->reference,
                    'Client: ' . $booking->client_name,
                    'Téléphone: ' . ($booking->client_phone ?? 'N/A'),
                    'Véhicule: ' . ($booking->vehicle->full_name ?? 'N/A'),
                    'Statut: ' . $this->translateStatus($booking->status),
                    'Total: ' . number_format($booking->total_price, 0, ',', ' ') . ' DA',
                ]),
                'start' => $booking->start_date,
                'end' => $booking->end_date->addDay(), // iCal end date is exclusive
                'status' => $booking->status === 'cancelled' ? 'CANCELLED' : 'CONFIRMED',
                'color' => match($booking->status) {
                    'pending' => 'yellow',
                    'confirmed' => 'blue',
                    'active' => 'green',
                    'completed' => 'gray',
                    default => 'red',
                },
            ];
        }

        // Get availability blocks
        $vehicleIds = $loueur->vehicles()->pluck('id');
        $availabilities = Availability::whereIn('vehicle_id', $vehicleIds)
            ->where('end_date', '>=', $startDate)
            ->where('start_date', '<=', $endDate)
            ->with('vehicle')
            ->get();

        foreach ($availabilities as $availability) {
            $events[] = [
                'uid' => 'block-' . $availability->id . '@' . parse_url(config('app.url'), PHP_URL_HOST),
                'summary' => $availability->type_label . ': ' . ($availability->vehicle->full_name ?? 'Véhicule'),
                'description' => implode('\n', [
                    'Type: ' . $availability->type_label,
                    'Véhicule: ' . ($availability->vehicle->full_name ?? 'N/A'),
                    'Raison: ' . ($availability->reason ?? 'Non spécifiée'),
                ]),
                'start' => $availability->start_date,
                'end' => $availability->end_date->addDay(),
                'status' => 'CONFIRMED',
                'color' => match($availability->type) {
                    'blocked' => 'red',
                    'maintenance' => 'orange',
                    default => 'gray',
                },
            ];
        }

        return $events;
    }

    /**
     * Generate iCal content.
     */
    private function generateIcal(Loueur $loueur, array $events): string
    {
        $ical = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//' . Setting::get('company_name', 'ResaDZ') . '//Calendrier Loueur//FR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:' . $this->escapeIcal($loueur->company_name . ' - ' . Setting::get('company_name', 'ResaDZ')),
            'X-WR-TIMEZONE:Africa/Algiers',
        ];

        foreach ($events as $event) {
            $ical[] = 'BEGIN:VEVENT';
            $ical[] = 'UID:' . $event['uid'];
            $ical[] = 'DTSTAMP:' . now()->format('Ymd\THis\Z');
            $ical[] = 'DTSTART;VALUE=DATE:' . $event['start']->format('Ymd');
            $ical[] = 'DTEND;VALUE=DATE:' . $event['end']->format('Ymd');
            $ical[] = 'SUMMARY:' . $this->escapeIcal($event['summary']);
            $ical[] = 'DESCRIPTION:' . $this->escapeIcal($event['description']);
            $ical[] = 'STATUS:' . $event['status'];
            $ical[] = 'END:VEVENT';
        }

        $ical[] = 'END:VCALENDAR';

        return implode("\r\n", $ical);
    }

    /**
     * Escape special characters for iCal.
     */
    private function escapeIcal(string $text): string
    {
        return str_replace(
            [',', ';', "\n", "\r"],
            ['\,', '\;', '\n', ''],
            $text
        );
    }

    /**
     * Translate booking status to French.
     */
    private function translateStatus(string $status): string
    {
        return match($status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'active' => 'En cours',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
            default => $status,
        };
    }
}

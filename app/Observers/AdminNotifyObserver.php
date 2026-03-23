<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\Loueur;
use App\Models\Review;
use App\Models\TransferBooking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Notifies admin(s) by email when key events happen:
 * - New Loueur/Chauffeur inscription  → inscription@resadz.com
 * - New Vehicle added                 → inscription@resadz.com
 * - New Booking / TransferBooking     → reservation@resadz.com
 * - New Review                        → avis@resadz.com
 */
class AdminNotifyObserver
{
    public function created($model): void
    {
        if ($model instanceof Loueur) {
            $this->notifyNewLoueur($model);
        } elseif ($model instanceof Vehicle) {
            $this->notifyNewVehicle($model);
        } elseif ($model instanceof Booking) {
            $this->notifyNewBooking($model);
        } elseif ($model instanceof TransferBooking) {
            $this->notifyNewTransferBooking($model);
        } elseif ($model instanceof Review) {
            $this->notifyNewReview($model);
        }
    }

    // ─── Inscriptions ────────────────────────────────────────────

    private function notifyNewLoueur(Loueur $loueur): void
    {
        $type = $loueur->account_type === 'taxi' ? 'chauffeur/taxi' : 'loueur';
        $subject = "Nouveau {$type} inscrit — " . ($loueur->company_name ?: 'Sans nom');

        $body = "Nouveau {$type} inscrit sur ResaDZ.\n\n" .
            "Nom : " . ($loueur->company_name ?: 'Non renseigné') . "\n" .
            "Wilaya : " . ($loueur->wilaya ?: 'Non renseignée') . "\n" .
            "Téléphone : " . ($loueur->phone ?: 'Non renseigné') . "\n" .
            "WhatsApp : " . ($loueur->whatsapp ?: 'Non renseigné') . "\n" .
            "Email : " . ($loueur->user->email ?? 'Non renseigné') . "\n" .
            "Type : " . $type . "\n\n" .
            "→ Voir dans le panel admin : " . url('/admin/loueurs/' . $loueur->id);

        $this->sendToAdmins($subject, $body, 'inscription');
    }

    private function notifyNewVehicle(Vehicle $vehicle): void
    {
        $vehicle->loadMissing(['loueur', 'brand']);

        $subject = "Nouveau véhicule ajouté — " .
            ($vehicle->brand->name ?? '') . ' ' . ($vehicle->model ?? '');

        $body = "Nouveau véhicule ajouté sur ResaDZ.\n\n" .
            "Véhicule : " . ($vehicle->brand->name ?? '') . ' ' . ($vehicle->model ?? '') . "\n" .
            "Prix/jour : " . number_format($vehicle->price_per_day ?? 0, 0, ',', ' ') . " DA\n" .
            "Loueur : " . ($vehicle->loueur->company_name ?? 'Inconnu') . "\n" .
            "Wilaya : " . ($vehicle->loueur->wilaya ?? '') . "\n\n" .
            "→ Voir dans le panel admin : " . url('/admin/vehicles/' . $vehicle->id);

        $this->sendToAdmins($subject, $body, 'admin');
    }

    // ─── Réservations ────────────────────────────────────────────

    private function notifyNewBooking(Booking $booking): void
    {
        $booking->loadMissing(['vehicle.brand', 'vehicle.loueur', 'loueur']);

        $vehicle = $booking->vehicle;
        $loueur  = $booking->loueur;

        $subject = "Nouvelle réservation #{$booking->reference} — " .
            ($vehicle->full_name ?? 'Véhicule');

        $currency = ($booking->currency === 'EUR') ? '€' : 'DA';

        $body = "Nouvelle réservation sur ResaDZ !\n\n" .
            "═══ RÉSERVATION ═══\n" .
            "Référence : {$booking->reference}\n" .
            "Statut : {$booking->status}\n\n" .
            "═══ VÉHICULE ═══\n" .
            "Véhicule : " . ($vehicle->full_name ?? 'N/A') . "\n" .
            "Prix/jour : " . number_format($vehicle->price_per_day ?? 0, 0, ',', ' ') . " DA\n\n" .
            "═══ CLIENT ═══\n" .
            "Nom : " . ($booking->client_name ?: 'Non renseigné') . "\n" .
            "Téléphone : " . ($booking->client_phone ?: 'Non renseigné') . "\n" .
            "WhatsApp : " . ($booking->client_whatsapp ?: 'Non renseigné') . "\n" .
            "Email : " . ($booking->client_email ?: 'Non renseigné') . "\n\n" .
            "═══ LOUEUR ═══\n" .
            "Agence : " . ($loueur->company_name ?? 'N/A') . "\n" .
            "Tél loueur : " . ($loueur->phone ?? 'N/A') . "\n" .
            "WhatsApp loueur : " . ($loueur->whatsapp ?? 'N/A') . "\n" .
            "Email loueur : " . ($loueur->email_contact ?? $loueur->user->email ?? 'N/A') . "\n" .
            "Wilaya : " . ($loueur->wilaya ?? 'N/A') . "\n\n" .
            "═══ DÉTAILS ═══\n" .
            "Dates : du " . ($booking->start_date?->format('d/m/Y') ?? '?') .
            " au " . ($booking->end_date?->format('d/m/Y') ?? '?') .
            " (" . ($booking->total_days ?? '?') . " jours)\n" .
            "Heure prise en charge : " . ($booking->pickup_time ?: 'Non spécifiée') . "\n" .
            "Lieu : " . ($booking->pickup_address ?: 'Non spécifié') . "\n" .
            "Total : " . number_format($booking->total_price ?? 0, 0, ',', ' ') . " {$currency}\n" .
            "Commission : " . number_format($booking->commission_amount ?? 0, 0, ',', ' ') . " {$currency} (" . ($booking->commission_rate ?? 0) . "%)\n" .
            ($booking->advance_amount > 0 ? "Acompte : " . number_format($booking->advance_amount, 0, ',', ' ') . " {$currency}\n" : '') .
            "\n→ Voir dans le panel admin : " . url('/admin/bookings/' . $booking->id) . "\n\n" .
            "💡 Pensez à relancer le loueur si pas de réponse sous 24h.";

        $this->sendToAdmins($subject, $body, 'reservation');
    }

    private function notifyNewTransferBooking(TransferBooking $transfer): void
    {
        $transfer->loadMissing('loueur');
        $loueur = $transfer->loueur;

        $subject = "Nouveau transfert réservé — " .
            ($transfer->departure ?? '?') . ' → ' . ($transfer->destination ?? '?');

        $body = "Nouveau transfert réservé sur ResaDZ !\n\n" .
            "═══ TRANSFERT ═══\n" .
            "Trajet : " . ($transfer->departure ?? '?') . " → " . ($transfer->destination ?? '?') . "\n" .
            "Date : " . ($transfer->transfer_date ?? 'Non spécifiée') . "\n" .
            "Heure : " . ($transfer->transfer_time ?? 'Non spécifiée') . "\n" .
            "Passagers : " . ($transfer->passengers ?? '?') . "\n" .
            "Prix : " . number_format($transfer->total_price ?? 0, 0, ',', ' ') . " DA\n\n" .
            "═══ CLIENT ═══\n" .
            "Nom : " . ($transfer->client_name ?: 'Non renseigné') . "\n" .
            "Téléphone : " . ($transfer->client_phone ?: 'Non renseigné') . "\n" .
            "Email : " . ($transfer->client_email ?: 'Non renseigné') . "\n\n" .
            "═══ CHAUFFEUR ═══\n" .
            "Nom : " . ($loueur->company_name ?? 'N/A') . "\n" .
            "Tél : " . ($loueur->phone ?? 'N/A') . "\n" .
            "WhatsApp : " . ($loueur->whatsapp ?? 'N/A') . "\n\n" .
            "→ Voir dans le panel admin : " . url('/admin');

        $this->sendToAdmins($subject, $body, 'reservation');
    }

    // ─── Avis ────────────────────────────────────────────────────

    private function notifyNewReview(Review $review): void
    {
        $review->loadMissing(['loueur', 'reviewer', 'booking.vehicle']);

        $subject = "Nouvel avis — " .
            ($review->rating_overall ?? '?') . "/5 pour " .
            ($review->loueur->company_name ?? 'un loueur');

        $body = "Nouvel avis posté sur ResaDZ.\n\n" .
            "═══ AVIS ═══\n" .
            "Note globale : " . ($review->rating_overall ?? '?') . "/5\n" .
            "Véhicule : " . ($review->rating_vehicle ?? '?') . "/5\n" .
            "Communication : " . ($review->rating_communication ?? '?') . "/5\n" .
            "Ponctualité : " . ($review->rating_punctuality ?? '?') . "/5\n" .
            "Propreté : " . ($review->rating_cleanliness ?? '?') . "/5\n\n" .
            "Commentaire : " . ($review->comment ?: 'Aucun commentaire') . "\n\n" .
            "═══ DÉTAILS ═══\n" .
            "Loueur : " . ($review->loueur->company_name ?? 'N/A') . "\n" .
            "Client : " . ($review->reviewer->name ?? 'Anonyme') . "\n" .
            "Véhicule : " . ($review->booking?->vehicle?->full_name ?? 'N/A') . "\n" .
            "Type : " . ($review->type === 'client_to_loueur' ? 'Client → Loueur' : 'Loueur → Client') . "\n" .
            "Approuvé : " . ($review->is_approved ? 'Oui' : 'Non') . "\n\n" .
            "→ Voir dans le panel admin : " . url('/admin/reviews/' . $review->id);

        $this->sendToAdmins($subject, $body, 'avis');
    }

    // ─── Envoi ───────────────────────────────────────────────────

    /**
     * @param string $emailType  inscription|reservation|avis|contact
     */
    private function sendToAdmins(string $subject, string $body, string $emailType = 'contact'): void
    {
        try {
            $admins = User::where('role', 'super_admin')->pluck('email')->filter();

            $from = config("resadz_emails.{$emailType}", config('resadz_emails.contact'));

            foreach ($admins as $email) {
                Mail::raw($body, function ($message) use ($email, $subject, $from) {
                    $message
                        ->from($from['address'], $from['name'])
                        ->to($email)
                        ->subject($subject);
                });
            }
        } catch (\Exception $e) {
            Log::warning('AdminNotifyObserver: failed to send email — ' . $e->getMessage());
        }
    }
}

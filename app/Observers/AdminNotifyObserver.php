<?php

namespace App\Observers;

use App\Models\Loueur;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Notifies admin(s) by email when a new Loueur, Chauffeur, or Vehicle is created.
 */
class AdminNotifyObserver
{
    public function created($model): void
    {
        if ($model instanceof Loueur) {
            $this->notifyNewLoueur($model);
        } elseif ($model instanceof Vehicle) {
            $this->notifyNewVehicle($model);
        }
    }

    private function notifyNewLoueur(Loueur $loueur): void
    {
        $type = $loueur->account_type === 'taxi' ? 'chauffeur/taxi' : 'loueur';
        $subject = "Nouveau {$type} inscrit — " . ($loueur->company_name ?: 'Sans nom');

        $body = "Nouveau {$type} inscrit sur ResaDZ.\n\n" .
            "Nom : " . ($loueur->company_name ?: 'Non renseigné') . "\n" .
            "Wilaya : " . ($loueur->wilaya ?: 'Non renseignée') . "\n" .
            "Téléphone : " . ($loueur->phone ?: 'Non renseigné') . "\n" .
            "Email : " . ($loueur->user->email ?? 'Non renseigné') . "\n" .
            "Type : " . $type . "\n\n" .
            "→ Voir dans le panel admin : " . url('/admin/loueurs/' . $loueur->id);

        $this->sendToAdmins($subject, $body);
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

        $this->sendToAdmins($subject, $body);
    }

    private function sendToAdmins(string $subject, string $body): void
    {
        try {
            $admins = User::where('role', 'super_admin')->pluck('email')->filter();

            foreach ($admins as $email) {
                Mail::raw($body, function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
            }
        } catch (\Exception $e) {
            Log::warning('AdminNotifyObserver: failed to send email — ' . $e->getMessage());
        }
    }
}

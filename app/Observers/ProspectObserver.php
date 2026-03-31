<?php

namespace App\Observers;

use App\Models\Loueur;
use App\Models\Prospect;

class ProspectObserver
{
    /**
     * Quand un loueur est créé, chercher si un prospect correspond par téléphone.
     * Si oui → marquer comme inscrit et lier le loueur.
     */
    public function created(Loueur $loueur): void
    {
        if (empty($loueur->phone)) {
            return;
        }

        // Normaliser le numéro (retirer espaces, +213, etc.)
        $phone = $this->normalizePhone($loueur->phone);

        $prospect = Prospect::where(function ($query) use ($phone, $loueur) {
            $query->where('telephone', $phone)
                ->orWhere('telephone', $loueur->phone)
                ->orWhere('telephone', '0' . substr($phone, -9))
                ->orWhere('telephone', '+213' . substr($phone, -9));
        })
        ->where('statut', '!=', 'inscrit')
        ->first();

        if ($prospect) {
            $prospect->update([
                'statut' => 'inscrit',
                'loueur_id' => $loueur->id,
                'date_dernier_contact' => now()->toDateString(),
            ]);
        }
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}

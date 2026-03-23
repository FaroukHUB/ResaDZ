<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour le nouveau modèle de commission 2026.
 *
 * Ancien modèle (supprimé):
 * - 150 DZD fixe/jour côté loueur
 * - 100 DZD fixe/jour côté locataire
 *
 * Nouveau modèle:
 * - Commission uniquement côté loueur, en % du montant HT
 * - Taux dégressif: 1-10j → 8%, +10j → 6%
 * - Locataire ne paie aucune commission
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Ajouter le palier de commission appliqué (ex: "1-10 jours", "+ de 10 jours")
            $table->string('commission_tier')->nullable()->after('commission_rate');
        });

        // Mettre à jour le default du taux de commission (8% pour les courtes durées)
        // Le champ commission_rate existant garde sa structure decimal(5,2)
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('commission_tier');
        });
    }
};

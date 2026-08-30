<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La colonne `image` a ete creee obligatoire en janvier 2024, alors que la
 * fonctionnalite de visuel studio invite explicitement le loueur a la laisser
 * vide ("Laissez vide si un visuel studio ResaDZ est disponible").
 * Resultat : enregistrer un vehicule sans photo principale echouait.
 *
 * Cette migration rend la colonne facultative. Aucune donnee n'est modifiee :
 * les photos existantes restent en place.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('vehicles', 'image')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->string('image')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Volontairement sans effet : revenir a NOT NULL echouerait sur les
        // vehicules qui n'ont pas de photo principale.
    }
};

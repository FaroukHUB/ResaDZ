<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Validation admin des nouvelles inscriptions loueur/chauffeur.
     *
     * Valeur par defaut true : tous les loueurs deja en base restent approuves
     * et visibles exactement comme avant. Seules les inscriptions creees apres
     * cette migration arrivent avec is_approved = false et attendent l'admin.
     */
    public function up(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->boolean('is_approved')->default(true)->after('is_verified');
        });
    }

    public function down(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};

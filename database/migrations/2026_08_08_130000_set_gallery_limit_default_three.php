<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ramène la limite par défaut de 5 à 3 (aucun pack payé à ce stade)
        if (Schema::hasColumn('vehicles', 'max_gallery_images')) {
            DB::table('vehicles')->where('max_gallery_images', 5)->update(['max_gallery_images' => 3]);
        }
    }

    public function down(): void
    {
        // Rien : changement de valeur par défaut uniquement
    }
};

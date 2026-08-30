<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Plage d'annees sur les visuels studio : une Logan 2005 et une Logan 2023
 * n'ont pas la meme carrosserie. Les deux bornes sont facultatives, donc les
 * visuels existants (sans plage) continuent de couvrir toutes les annees.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_templates', 'year_from')) {
                $table->unsignedSmallInteger('year_from')->nullable()->after('model_name');
            }
            if (!Schema::hasColumn('vehicle_templates', 'year_to')) {
                $table->unsignedSmallInteger('year_to')->nullable()->after('year_from');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_templates', function (Blueprint $table) {
            foreach (['year_from', 'year_to'] as $col) {
                if (Schema::hasColumn('vehicle_templates', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

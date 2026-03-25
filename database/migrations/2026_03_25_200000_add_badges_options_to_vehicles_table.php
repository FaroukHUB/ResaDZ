<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->json('vehicle_badges')->nullable()->after('gallery');
            $table->json('vehicle_options')->nullable()->after('vehicle_badges');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['vehicle_badges', 'vehicle_options']);
        });
    }
};

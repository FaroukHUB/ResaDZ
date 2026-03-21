<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_bookings', function (Blueprint $table) {
            $table->foreignId('chauffeur_vehicle_id')
                ->nullable()
                ->after('loueur_id')
                ->constrained('chauffeur_vehicles')
                ->nullOnDelete();
        });

        Schema::table('delivery_bookings', function (Blueprint $table) {
            $table->foreignId('chauffeur_vehicle_id')
                ->nullable()
                ->after('loueur_id')
                ->constrained('chauffeur_vehicles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transfer_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('chauffeur_vehicle_id');
        });

        Schema::table('delivery_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('chauffeur_vehicle_id');
        });
    }
};

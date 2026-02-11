<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Blocages de disponibilité (maintenance, indisponibilité manuelle, etc.)
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');

            $table->date('start_date');
            $table->date('end_date');

            $table->enum('type', [
                'blocked',      // Bloqué manuellement
                'maintenance',  // En maintenance
                'reserved'      // Réservé (lié à une réservation)
            ])->default('blocked');

            $table->text('reason')->nullable();         // Raison du blocage
            $table->foreignId('reservation_id')->nullable()->constrained()->onDelete('cascade');

            $table->timestamps();

            $table->index(['vehicle_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // LIV-XXXXXXXX
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();

            // Adresses
            $table->string('pickup_address');
            $table->string('pickup_city');
            $table->string('delivery_address');
            $table->string('delivery_city');

            // Colis
            $table->string('package_type')->default('colis'); // colis, document, repas
            $table->text('package_description')->nullable();
            $table->decimal('weight', 8, 2)->nullable(); // kg
            $table->decimal('price', 10, 2)->default(0);

            // Client
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email')->nullable();
            $table->text('client_notes')->nullable();

            // Destinataire
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();

            // Planning
            $table->date('pickup_date');
            $table->string('pickup_time')->nullable();

            // Statut & tracking
            $table->string('status')->default('pending');
            // pending -> confirmed -> picked_up -> in_transit -> delivered / cancelled
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();

            // Tracking
            $table->string('tracking_code')->unique()->nullable(); // code court pour le client
            $table->json('tracking_history')->nullable(); // [{status, location, note, timestamp}]

            $table->timestamps();

            $table->index(['loueur_id', 'status']);
            $table->index('tracking_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_bookings');
    }
};

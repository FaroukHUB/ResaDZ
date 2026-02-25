<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();
            $table->string('departure');
            $table->string('destination');
            $table->decimal('price', 10, 2);
            $table->string('vehicle_type')->default('berline'); // berline, suv, van, minibus
            $table->integer('max_passengers')->default(4);
            $table->boolean('is_active')->default(true);
            $table->boolean('round_trip')->default(false);
            $table->decimal('round_trip_price', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['loueur_id', 'is_active']);
            $table->index(['departure', 'destination']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_routes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add air conditioning field to vehicles
        Schema::table('vehicles', function (Blueprint $table) {
            $table->boolean('has_air_conditioning')->default(true)->after('fuel_type');
        });

        // Create vehicle offers table
        Schema::create('vehicle_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('loueur_id')->constrained()->onDelete('cascade');
            $table->string('title'); // e.g., "Offre spéciale weekend"
            $table->string('badge_text'); // e.g., "-10%", "Promo", "-20% WE"
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2); // 10 for 10%, or 500 for 500 DA
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'is_active', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_offers');

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('has_air_conditioning');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();
            $table->string('from_city');
            $table->string('to_city');
            $table->string('package_type')->default('colis'); // colis, document, repas
            $table->decimal('base_price', 10, 2);
            $table->decimal('price_per_kg', 10, 2)->nullable();
            $table->decimal('max_weight', 8, 2)->nullable(); // kg
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['loueur_id', 'is_active']);
            $table->index(['from_city', 'to_city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_rates');
    }
};

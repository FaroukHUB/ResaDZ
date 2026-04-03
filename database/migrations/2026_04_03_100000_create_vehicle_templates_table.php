<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('model_name');
            $table->string('color');
            $table->string('image_path');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['brand_id', 'model_name', 'color']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_templates');
    }
};

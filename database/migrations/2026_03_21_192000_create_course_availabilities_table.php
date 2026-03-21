<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loueur_id')->constrained()->onDelete('cascade');

            // Type de disponibilite
            $table->enum('type', ['available', 'unavailable'])->default('available');

            // Services proposes pendant ce creneau
            $table->boolean('for_transfer')->default(true);
            $table->boolean('for_delivery')->default(true);

            // Date et heure
            $table->date('date');
            $table->time('start_time')->nullable(); // null = toute la journee
            $table->time('end_time')->nullable();

            // Recurrence (optionnel)
            $table->enum('recurrence', ['none', 'daily', 'weekly', 'monthly'])->default('none');
            $table->date('recurrence_end')->nullable();
            $table->json('recurrence_days')->nullable(); // Pour weekly: [1,2,3,4,5] = lun-ven

            // Zone geographique (optionnel)
            $table->string('wilaya')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index
            $table->index(['loueur_id', 'date', 'is_active']);
            $table->index(['loueur_id', 'type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_availabilities');
    }
};

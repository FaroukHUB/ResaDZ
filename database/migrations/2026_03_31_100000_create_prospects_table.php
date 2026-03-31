<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('telephone')->unique();
            $table->string('email')->nullable();
            $table->string('wilaya')->nullable();
            $table->unsignedInteger('nb_vehicules')->nullable();
            $table->string('source')->default('autre'); // facebook, google, telegram, terrain, whatsapp, autre
            $table->string('statut')->default('non_contacte'); // non_contacte, contacte, interesse, inscrit, pas_interesse
            $table->text('notes')->nullable();
            $table->date('date_dernier_contact')->nullable();
            $table->date('relance_le')->nullable();
            $table->foreignId('loueur_id')->nullable()->constrained('loueurs')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};

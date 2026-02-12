<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();

            // Qui évalue qui
            $table->enum('type', ['client_to_loueur', 'loueur_to_client']);
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Notes (sur 5)
            $table->tinyInteger('rating_overall'); // Note globale
            $table->tinyInteger('rating_vehicle')->nullable(); // État du véhicule (client -> loueur)
            $table->tinyInteger('rating_communication')->nullable(); // Communication
            $table->tinyInteger('rating_punctuality')->nullable(); // Ponctualité
            $table->tinyInteger('rating_cleanliness')->nullable(); // Propreté (loueur -> client)
            $table->tinyInteger('rating_respect')->nullable(); // Respect (loueur -> client)

            // Commentaire
            $table->text('comment')->nullable();

            // Visibilité
            $table->boolean('is_public')->default(true);
            // Les avis loueur->client peuvent être masqués du public mais visibles des autres loueurs

            // Modération
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();

            // Réponse
            $table->text('response')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

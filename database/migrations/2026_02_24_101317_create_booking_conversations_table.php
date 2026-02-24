<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Conversations liées aux réservations (loueur <-> client)
        Schema::create('booking_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('client_email')->nullable();
            $table->timestamp('loueur_last_read_at')->nullable();
            $table->timestamp('client_last_read_at')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('loueur_unread_count')->default(0);
            $table->unsignedInteger('client_unread_count')->default(0);
            $table->timestamps();

            $table->unique('booking_id');
            $table->index(['loueur_id', 'last_message_at']);
        });

        // Messages dans les conversations de réservation
        Schema::create('booking_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_conversation_id')->constrained()->cascadeOnDelete();
            $table->enum('sender_type', ['loueur', 'client']);
            $table->foreignId('sender_id')->nullable();
            $table->text('content');
            $table->text('content_filtered')->nullable();
            $table->boolean('has_filtered_content')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['booking_conversation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_messages');
        Schema::dropIfExists('booking_conversations');
    }
};

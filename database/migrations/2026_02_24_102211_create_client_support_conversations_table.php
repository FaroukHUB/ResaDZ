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
        // Conversations support client <-> admin
        Schema::create('client_support_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('client_email');
            $table->string('client_name');
            $table->string('subject');
            $table->string('category')->default('general'); // general, booking, payment, complaint, other
            $table->enum('status', ['open', 'pending', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('client_unread')->default(false);
            $table->boolean('admin_unread')->default(true);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index('client_email');
            $table->index('last_message_at');
        });

        // Messages support client
        Schema::create('client_support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_support_conversation_id')->constrained()->cascadeOnDelete();
            $table->enum('sender_type', ['client', 'admin']);
            $table->foreignId('sender_id')->nullable();
            $table->text('content');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal_note')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['client_support_conversation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_support_messages');
        Schema::dropIfExists('client_support_conversations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->enum('status', ['open', 'closed', 'archived'])->default('open');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->string('category')->nullable(); // boost, invoice, support, general
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('loueur_unread')->default(false);
            $table->boolean('admin_unread')->default(false);
            $table->timestamps();

            $table->index(['loueur_id', 'status']);
            $table->index('last_message_at');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->enum('sender_type', ['admin', 'loueur']);
            $table->foreignId('sender_id')->nullable(); // user_id for admin, loueur_id for loueur
            $table->text('content');
            $table->json('attachments')->nullable(); // file paths
            $table->boolean('is_system_message')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};

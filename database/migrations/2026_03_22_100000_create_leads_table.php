<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('leads')) {
            return;
        }

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->string('source')->nullable(); // popup, footer, contact, etc.
            $table->string('source_page')->nullable();
            $table->json('interests')->nullable();
            $table->boolean('subscribed_newsletter')->default(false);
            $table->boolean('subscribed_whatsapp')->default(false);
            $table->boolean('subscribed_telegram')->default(false);
            $table->boolean('subscribed_sms')->default(false);
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->timestamps();

            $table->index(['email']);
            $table->index(['phone']);
            $table->index(['source']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

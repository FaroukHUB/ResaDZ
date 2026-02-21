<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('click_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 50); // phone_click, whatsapp_click, reserve_click, etc.
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('loueur_id')->nullable()->constrained()->nullOnDelete();
            $table->string('page_url', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('session_id', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('device_type', 20)->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('city')->nullable();
            $table->json('metadata')->nullable(); // Additional event data
            $table->timestamp('clicked_at');
            $table->timestamps();

            $table->index('event_type');
            $table->index('vehicle_id');
            $table->index('loueur_id');
            $table->index('clicked_at');
            $table->index(['event_type', 'clicked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('click_events');
    }
};

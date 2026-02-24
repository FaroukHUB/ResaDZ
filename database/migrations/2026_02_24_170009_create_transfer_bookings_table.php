<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();
            $table->string('departure');
            $table->string('destination');
            $table->date('transfer_date');
            $table->string('transfer_time');
            $table->integer('passengers')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('vehicle_type')->nullable();
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email');
            $table->text('client_notes')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_bookings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            // Période d'essai gratuit
            $table->date('trial_ends_at')->nullable()->after('subscription_expires_at');

            // Suspension du compte
            $table->boolean('is_suspended')->default(false)->after('is_verified');
            $table->string('suspension_reason')->nullable()->after('is_suspended');

            // Commission personnalisée par loueur (si différent du taux global)
            $table->decimal('commission_rate', 5, 2)->nullable()->after('trial_ends_at');

            // Suivi des paiements de commission
            $table->date('commission_paid_until')->nullable()->after('commission_rate');
            $table->text('commission_notes')->nullable()->after('commission_paid_until');
        });

        // Ajouter les champs de commission aux bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('commission_amount', 10, 2)->default(0)->after('total_price');
            $table->decimal('commission_rate', 5, 2)->default(5)->after('commission_amount');
            $table->boolean('commission_paid')->default(false)->after('commission_rate');
            $table->date('commission_paid_at')->nullable()->after('commission_paid');
        });
    }

    public function down(): void
    {
        Schema::table('loueurs', function (Blueprint $table) {
            $table->dropColumn([
                'trial_ends_at',
                'is_suspended',
                'suspension_reason',
                'commission_rate',
                'commission_paid_until',
                'commission_notes',
            ]);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'commission_amount',
                'commission_rate',
                'commission_paid',
                'commission_paid_at',
            ]);
        });
    }
};

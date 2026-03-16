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
        Schema::table('bookings', function (Blueprint $table) {
            // Reminder tracking
            if (!Schema::hasColumn('bookings', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable();
            }

            // Cancellation fields
            if (!Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'cancelled_by')) {
                $table->string('cancelled_by', 20)->nullable(); // client, loueur, system
            }
            if (!Schema::hasColumn('bookings', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable();
            }

            // Refund fields
            if (!Schema::hasColumn('bookings', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'refund_status')) {
                $table->string('refund_status', 20)->default('none'); // none, pending, processed
            }
            if (!Schema::hasColumn('bookings', 'refund_method')) {
                $table->string('refund_method', 50)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'refund_reference')) {
                $table->string('refund_reference')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'refund_processed_at')) {
                $table->timestamp('refund_processed_at')->nullable();
            }

            // Deposit return fields
            if (!Schema::hasColumn('bookings', 'deposit_returned_at')) {
                $table->timestamp('deposit_returned_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'deposit_deduction')) {
                $table->decimal('deposit_deduction', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'deposit_deduction_reason')) {
                $table->text('deposit_deduction_reason')->nullable();
            }

            // Final payment fields
            if (!Schema::hasColumn('bookings', 'final_payment_amount')) {
                $table->decimal('final_payment_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'final_payment_method')) {
                $table->string('final_payment_method', 50)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'final_payment_paid_at')) {
                $table->timestamp('final_payment_paid_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $columns = [
                'reminder_sent_at',
                'cancelled_at',
                'cancelled_by',
                'cancellation_reason',
                'refund_amount',
                'refund_status',
                'refund_method',
                'refund_reference',
                'refund_processed_at',
                'deposit_returned_at',
                'deposit_deduction',
                'deposit_deduction_reason',
                'final_payment_amount',
                'final_payment_method',
                'final_payment_paid_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

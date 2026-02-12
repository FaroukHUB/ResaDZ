<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catégories de dépenses (configurables par loueur)
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loueur_id')->nullable()->constrained()->cascadeOnDelete();
            // null = catégorie globale par défaut
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['loueur_id', 'slug']);
        });

        // Transactions (entrées et sorties)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();

            // Type
            $table->enum('type', ['income', 'expense']);

            // Relations optionnelles
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('expense_category_id')->nullable()->constrained()->nullOnDelete();

            // Détails
            $table->string('description');
            $table->text('notes')->nullable();

            // Montant (multi-devise)
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('DZD');
            $table->decimal('amount_eur', 12, 2)->nullable(); // Équivalent EUR si applicable

            // Méthode de paiement
            $table->string('payment_method')->nullable(); // cash, cib, paypal, bank_transfer, etc.
            $table->string('payment_reference')->nullable(); // Référence transaction

            // Pour les commissions ResaDZ
            $table->boolean('is_commission')->default(false);
            $table->decimal('commission_rate', 5, 2)->nullable(); // Ex: 10.00 = 10%

            // Date de la transaction
            $table->date('transaction_date');

            // Statut
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');

            $table->timestamps();

            // Index pour rapports
            $table->index(['loueur_id', 'type', 'transaction_date']);
            $table->index(['loueur_id', 'vehicle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('expense_categories');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loueurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Identité
            $table->string('company_name');
            $table->string('slug')->unique();
            $table->string('subdomain')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();

            // Contact
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email_contact')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('wilaya')->nullable();

            // Réseaux sociaux
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();

            // Paiements acceptés (JSON - configurable par loueur)
            $table->json('payment_methods')->nullable();
            // Ex: ["cash", "cib", "dahabia", "baridimob", "paypal", "wise", "bank_transfer"]

            // Infos paiement du loueur
            $table->string('paypal_email')->nullable();
            $table->string('iban')->nullable();
            $table->string('wise_email')->nullable();
            $table->string('baridimob_rip')->nullable();

            // Statut
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();

            // Abonnement
            $table->enum('subscription_plan', ['free', 'pro', 'premium', 'business'])->default('free');
            $table->timestamp('subscription_expires_at')->nullable();

            // Stats (dénormalisées pour performance)
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_rentals')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loueurs');
    }
};

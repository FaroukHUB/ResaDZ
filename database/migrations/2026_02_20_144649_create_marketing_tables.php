<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===========================================
        // 1. BOOST / MISE EN AVANT PAYANTE
        // ===========================================

        // Packages de boost disponibles
        Schema::create('boost_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Boost 7 jours", "Boost 30 jours"
            $table->text('description')->nullable();
            $table->integer('duration_days'); // Durée du boost
            $table->decimal('price', 10, 2); // Prix en DA
            $table->string('boost_type')->default('standard'); // standard, premium, featured
            $table->integer('position_boost')->default(0); // Bonus de position dans les résultats
            $table->boolean('show_badge')->default(true); // Afficher badge "Sponsorisé"
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Boosts achetés par les loueurs
        Schema::create('vehicle_boosts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loueur_id')->constrained()->cascadeOnDelete();
            $table->foreignId('boost_package_id')->constrained()->cascadeOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['vehicle_id', 'status', 'ends_at']);
        });

        // ===========================================
        // 2. COLLECTE DE CONTACTS / LEADS
        // ===========================================

        // Contacts collectés (emails, téléphones)
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->string('source'); // popup, footer, landing, whatsapp_cta, telegram_cta
            $table->string('source_page')->nullable(); // URL de la page
            $table->json('interests')->nullable(); // Catégories/marques intéressées
            $table->boolean('subscribed_newsletter')->default(false);
            $table->boolean('subscribed_whatsapp')->default(false);
            $table->boolean('subscribed_telegram')->default(false);
            $table->boolean('subscribed_sms')->default(false);
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('phone');
            $table->index('source');
        });

        // Canaux sociaux (groupes WhatsApp, channels Telegram) à promouvoir
        Schema::create('social_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Groupe WhatsApp Bons Plans"
            $table->enum('type', ['whatsapp', 'telegram', 'facebook', 'instagram']);
            $table->string('url'); // Lien d'invitation
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Image/icône
            $table->integer('member_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // ===========================================
        // 3. PROGRAMME DE PARRAINAGE
        // ===========================================

        // Ajouter champs de parrainage à la table users
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 10)->nullable()->unique()->after('remember_token');
            $table->decimal('referral_credits', 10, 2)->default(0)->after('referral_code');
            $table->foreignId('referred_by')->nullable()->after('referral_credits')
                ->constrained('users')->nullOnDelete();
            $table->integer('referral_count')->default(0)->after('referred_by');
        });

        // Historique des parrainages
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'qualified', 'rewarded', 'cancelled'])->default('pending');
            $table->string('qualification_event')->nullable(); // first_booking, account_verified
            $table->decimal('referrer_reward', 10, 2)->nullable();
            $table->decimal('referred_reward', 10, 2)->nullable();
            $table->timestamp('qualified_at')->nullable();
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();

            $table->unique(['referrer_id', 'referred_id']);
        });

        // Configuration des récompenses de parrainage
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Récompense standard"
            $table->string('event'); // first_booking, account_verified
            $table->decimal('referrer_amount', 10, 2); // Récompense parrain
            $table->decimal('referred_amount', 10, 2); // Récompense filleul
            $table->enum('reward_type', ['credit', 'discount_percent', 'discount_fixed']);
            $table->integer('max_uses_per_user')->nullable(); // Limite par utilisateur
            $table->integer('total_max_uses')->nullable(); // Limite totale
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        // ===========================================
        // 4. NEWSLETTER
        // ===========================================

        // Abonnés newsletter
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'active', 'unsubscribed', 'bounced'])->default('pending');
            $table->string('confirmation_token')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('unsubscribe_reason')->nullable();
            $table->json('preferences')->nullable(); // Types de contenus souhaités
            $table->string('source')->nullable(); // footer, popup, checkout
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // Campagnes newsletter
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('preview_text')->nullable();
            $table->longText('content'); // HTML content
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'cancelled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('recipients_count')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('bounced_count')->default(0);
            $table->integer('unsubscribed_count')->default(0);
            $table->json('target_segments')->nullable(); // Filtres de ciblage
            $table->timestamps();
        });

        // Tracking des envois
        Schema::create('newsletter_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscriber_id')->constrained('newsletter_subscribers')->cascadeOnDelete();
            $table->enum('status', ['pending', 'sent', 'opened', 'clicked', 'bounced', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->string('tracking_token')->unique();
            $table->timestamps();

            $table->index(['newsletter_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_sends');
        Schema::dropIfExists('newsletters');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('referral_rewards');
        Schema::dropIfExists('referrals');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referral_code', 'referral_credits', 'referred_by', 'referral_count']);
        });

        Schema::dropIfExists('social_channels');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('vehicle_boosts');
        Schema::dropIfExists('boost_packages');
    }
};

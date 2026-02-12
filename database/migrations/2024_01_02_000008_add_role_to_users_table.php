<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'loueur', 'client'])->default('client')->after('email');

            // Info supplémentaires utilisateur
            $table->string('phone')->nullable()->after('role');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('whatsapp');

            // Vérification
            $table->boolean('phone_verified')->default(false);
            $table->timestamp('phone_verified_at')->nullable();

            // Documents (pour clients)
            $table->string('id_document')->nullable(); // CNI/Passeport
            $table->string('license_front')->nullable();
            $table->string('license_back')->nullable();
            $table->boolean('documents_verified')->default(false);
            $table->timestamp('documents_verified_at')->nullable();

            // Stats client
            $table->decimal('client_rating', 3, 2)->default(0);
            $table->integer('total_bookings')->default(0);

            // Blacklist
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'phone',
                'whatsapp',
                'avatar',
                'phone_verified',
                'phone_verified_at',
                'id_document',
                'license_front',
                'license_back',
                'documents_verified',
                'documents_verified_at',
                'client_rating',
                'total_bookings',
                'is_blacklisted',
                'blacklist_reason',
            ]);
        });
    }
};

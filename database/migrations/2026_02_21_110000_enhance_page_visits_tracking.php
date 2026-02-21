<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            // Geolocation fields (if not exist)
            if (!Schema::hasColumn('page_visits', 'country_code')) {
                $table->string('country_code', 5)->nullable()->after('country');
            }
            if (!Schema::hasColumn('page_visits', 'region')) {
                $table->string('region')->nullable()->after('city');
            }
            if (!Schema::hasColumn('page_visits', 'region_code')) {
                $table->string('region_code', 10)->nullable()->after('region');
            }
            if (!Schema::hasColumn('page_visits', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('region_code');
            }
            if (!Schema::hasColumn('page_visits', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('page_visits', 'timezone')) {
                $table->string('timezone')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('page_visits', 'isp')) {
                $table->string('isp')->nullable()->after('timezone');
            }

            // Traffic source analysis
            if (!Schema::hasColumn('page_visits', 'traffic_source')) {
                $table->string('traffic_source')->nullable()->after('referer'); // google, facebook, direct, etc.
            }
            if (!Schema::hasColumn('page_visits', 'traffic_medium')) {
                $table->string('traffic_medium')->nullable()->after('traffic_source'); // organic, social, referral, etc.
            }
            if (!Schema::hasColumn('page_visits', 'referrer_domain')) {
                $table->string('referrer_domain')->nullable()->after('traffic_medium');
            }

            // UTM tracking
            if (!Schema::hasColumn('page_visits', 'utm_source')) {
                $table->string('utm_source')->nullable()->after('referrer_domain');
            }
            if (!Schema::hasColumn('page_visits', 'utm_medium')) {
                $table->string('utm_medium')->nullable()->after('utm_source');
            }
            if (!Schema::hasColumn('page_visits', 'utm_campaign')) {
                $table->string('utm_campaign')->nullable()->after('utm_medium');
            }
            if (!Schema::hasColumn('page_visits', 'utm_term')) {
                $table->string('utm_term')->nullable()->after('utm_campaign');
            }
            if (!Schema::hasColumn('page_visits', 'utm_content')) {
                $table->string('utm_content')->nullable()->after('utm_term');
            }

            // Session tracking
            if (!Schema::hasColumn('page_visits', 'session_id')) {
                $table->string('session_id', 64)->nullable()->after('ip_address');
            }

            // OS detection
            if (!Schema::hasColumn('page_visits', 'os')) {
                $table->string('os')->nullable()->after('browser');
            }

            // Landing page & exit tracking
            if (!Schema::hasColumn('page_visits', 'is_landing')) {
                $table->boolean('is_landing')->default(false)->after('page_type');
            }
            if (!Schema::hasColumn('page_visits', 'is_bounce')) {
                $table->boolean('is_bounce')->default(false)->after('is_landing');
            }

            // Add indexes for better query performance
            $table->index('country_code');
            $table->index('traffic_source');
            $table->index('session_id');
            $table->index('utm_source');
            $table->index(['visited_at', 'country_code']);
            $table->index(['visited_at', 'traffic_source']);
        });
    }

    public function down(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->dropIndex(['country_code']);
            $table->dropIndex(['traffic_source']);
            $table->dropIndex(['session_id']);
            $table->dropIndex(['utm_source']);
            $table->dropIndex(['visited_at', 'country_code']);
            $table->dropIndex(['visited_at', 'traffic_source']);

            $columns = [
                'country_code', 'region', 'region_code', 'latitude', 'longitude',
                'timezone', 'isp', 'traffic_source', 'traffic_medium', 'referrer_domain',
                'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
                'session_id', 'os', 'is_landing', 'is_bounce',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('page_visits', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

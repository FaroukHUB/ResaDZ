<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds missing indexes identified in the database audit for performance optimization.
     */
    public function up(): void
    {
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            if (!$this->hasIndex('users', 'users_role_index')) {
                $table->index('role');
            }
        });

        // Vehicles table indexes
        Schema::table('vehicles', function (Blueprint $table) {
            if (!$this->hasIndex('vehicles', 'vehicles_loueur_id_index')) {
                $table->index('loueur_id');
            }
            if (!$this->hasIndex('vehicles', 'vehicles_is_active_status_index')) {
                $table->index(['is_active', 'status']);
            }
            if (!$this->hasIndex('vehicles', 'vehicles_slug_index')) {
                $table->index('slug');
            }
        });

        // Bookings table indexes
        Schema::table('bookings', function (Blueprint $table) {
            if (!$this->hasIndex('bookings', 'bookings_vehicle_id_index')) {
                $table->index('vehicle_id');
            }
            if (!$this->hasIndex('bookings', 'bookings_payment_status_index')) {
                $table->index('payment_status');
            }
            if (!$this->hasIndex('bookings', 'bookings_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('bookings', 'bookings_advance_expires_at_index')) {
                $table->index('advance_expires_at');
            }
            if (!$this->hasIndex('bookings', 'bookings_client_email_index')) {
                $table->index('client_email');
            }
        });

        // Reservations table indexes (if exists)
        if (Schema::hasTable('reservations')) {
            Schema::table('reservations', function (Blueprint $table) {
                if (!$this->hasIndex('reservations', 'reservations_vehicle_id_index')) {
                    $table->index('vehicle_id');
                }
                if (!$this->hasIndex('reservations', 'reservations_status_index')) {
                    $table->index('status');
                }
                if (!$this->hasIndex('reservations', 'reservations_start_date_end_date_index')) {
                    $table->index(['start_date', 'end_date']);
                }
            });
        }

        // Transactions table indexes
        Schema::table('transactions', function (Blueprint $table) {
            if (!$this->hasIndex('transactions', 'transactions_booking_id_index')) {
                $table->index('booking_id');
            }
            if (!$this->hasIndex('transactions', 'transactions_vehicle_id_index')) {
                $table->index('vehicle_id');
            }
            if (!$this->hasIndex('transactions', 'transactions_expense_category_id_index')) {
                $table->index('expense_category_id');
            }
            if (!$this->hasIndex('transactions', 'transactions_is_commission_index')) {
                $table->index('is_commission');
            }
        });

        // Reviews table indexes
        Schema::table('reviews', function (Blueprint $table) {
            if (!$this->hasIndex('reviews', 'reviews_booking_id_index')) {
                $table->index('booking_id');
            }
            if (!$this->hasIndex('reviews', 'reviews_is_approved_index')) {
                $table->index('is_approved');
            }
            if (!$this->hasIndex('reviews', 'reviews_loueur_id_index')) {
                $table->index('loueur_id');
            }
        });

        // Delivery zones table indexes
        Schema::table('delivery_zones', function (Blueprint $table) {
            if (!$this->hasIndex('delivery_zones', 'delivery_zones_loueur_id_index')) {
                $table->index('loueur_id');
            }
        });

        // Transfer routes table indexes
        if (Schema::hasTable('transfer_routes')) {
            Schema::table('transfer_routes', function (Blueprint $table) {
                if (!$this->hasIndex('transfer_routes', 'transfer_routes_loueur_id_index')) {
                    $table->index('loueur_id');
                }
            });
        }

        // Delivery rates table indexes
        if (Schema::hasTable('delivery_rates')) {
            Schema::table('delivery_rates', function (Blueprint $table) {
                if (!$this->hasIndex('delivery_rates', 'delivery_rates_loueur_id_index')) {
                    $table->index('loueur_id');
                }
            });
        }

        // Invoices table indexes
        Schema::table('invoices', function (Blueprint $table) {
            if (!$this->hasIndex('invoices', 'invoices_status_index')) {
                $table->index('status');
            }
            if (!$this->hasIndex('invoices', 'invoices_loueur_id_index')) {
                $table->index('loueur_id');
            }
        });

        // Conversations table indexes
        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table) {
                if (!$this->hasIndex('conversations', 'conversations_loueur_id_index')) {
                    $table->index('loueur_id');
                }
            });
        }

        // Page visits table indexes
        if (Schema::hasTable('page_visits')) {
            Schema::table('page_visits', function (Blueprint $table) {
                if (!$this->hasIndex('page_visits', 'page_visits_loueur_id_index')) {
                    $table->index('loueur_id');
                }
                if (!$this->hasIndex('page_visits', 'page_visits_vehicle_id_index')) {
                    $table->index('vehicle_id');
                }
                if (!$this->hasIndex('page_visits', 'page_visits_created_at_index')) {
                    $table->index('created_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndexIfExists('users_role_index');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndexIfExists('vehicles_loueur_id_index');
            $table->dropIndexIfExists('vehicles_is_active_status_index');
            $table->dropIndexIfExists('vehicles_slug_index');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndexIfExists('bookings_vehicle_id_index');
            $table->dropIndexIfExists('bookings_payment_status_index');
            $table->dropIndexIfExists('bookings_status_index');
            $table->dropIndexIfExists('bookings_advance_expires_at_index');
            $table->dropIndexIfExists('bookings_client_email_index');
        });

        if (Schema::hasTable('reservations')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropIndexIfExists('reservations_vehicle_id_index');
                $table->dropIndexIfExists('reservations_status_index');
                $table->dropIndexIfExists('reservations_start_date_end_date_index');
            });
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndexIfExists('transactions_booking_id_index');
            $table->dropIndexIfExists('transactions_vehicle_id_index');
            $table->dropIndexIfExists('transactions_expense_category_id_index');
            $table->dropIndexIfExists('transactions_is_commission_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndexIfExists('reviews_booking_id_index');
            $table->dropIndexIfExists('reviews_is_approved_index');
            $table->dropIndexIfExists('reviews_loueur_id_index');
        });

        Schema::table('delivery_zones', function (Blueprint $table) {
            $table->dropIndexIfExists('delivery_zones_loueur_id_index');
        });

        if (Schema::hasTable('transfer_routes')) {
            Schema::table('transfer_routes', function (Blueprint $table) {
                $table->dropIndexIfExists('transfer_routes_loueur_id_index');
            });
        }

        if (Schema::hasTable('delivery_rates')) {
            Schema::table('delivery_rates', function (Blueprint $table) {
                $table->dropIndexIfExists('delivery_rates_loueur_id_index');
            });
        }

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndexIfExists('invoices_status_index');
            $table->dropIndexIfExists('invoices_loueur_id_index');
        });

        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->dropIndexIfExists('conversations_loueur_id_index');
            });
        }

        if (Schema::hasTable('page_visits')) {
            Schema::table('page_visits', function (Blueprint $table) {
                $table->dropIndexIfExists('page_visits_loueur_id_index');
                $table->dropIndexIfExists('page_visits_vehicle_id_index');
                $table->dropIndexIfExists('page_visits_created_at_index');
            });
        }
    }

    /**
     * Check if an index exists on a table.
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if ($index['name'] === $indexName) {
                return true;
            }
        }
        return false;
    }
};

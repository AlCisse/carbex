<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fixes schema mismatch: model expects 'status' but migration created 'stripe_status'
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('subscriptions', 'status')) {
                $table->string('status')->default('active')->after('plan');
            }

            // Add missing columns that the model expects
            if (!Schema::hasColumn('subscriptions', 'stripe_subscription_id')) {
                $table->string('stripe_subscription_id')->nullable()->after('stripe_id');
            }

            if (!Schema::hasColumn('subscriptions', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable()->after('stripe_subscription_id');
            }

            if (!Schema::hasColumn('subscriptions', 'stripe_price_id')) {
                if (!Schema::hasColumn('subscriptions', 'stripe_price_id')) {
                    $table->string('stripe_price_id')->nullable()->change();
                }
            }

            // Add limit columns the model expects
            if (!Schema::hasColumn('subscriptions', 'bank_connections_limit')) {
                $table->integer('bank_connections_limit')->nullable()->after('max_bank_connections');
            }

            if (!Schema::hasColumn('subscriptions', 'bank_connections_used')) {
                $table->integer('bank_connections_used')->default(0)->after('bank_connections_limit');
            }

            if (!Schema::hasColumn('subscriptions', 'users_limit')) {
                $table->integer('users_limit')->nullable()->after('max_users');
            }

            if (!Schema::hasColumn('subscriptions', 'users_used')) {
                $table->integer('users_used')->default(0)->after('users_limit');
            }

            if (!Schema::hasColumn('subscriptions', 'sites_limit')) {
                $table->integer('sites_limit')->nullable()->after('max_sites');
            }

            if (!Schema::hasColumn('subscriptions', 'sites_used')) {
                $table->integer('sites_used')->default(0)->after('sites_limit');
            }

            if (!Schema::hasColumn('subscriptions', 'reports_monthly_limit')) {
                $table->integer('reports_monthly_limit')->nullable();
            }

            if (!Schema::hasColumn('subscriptions', 'reports_monthly_used')) {
                $table->integer('reports_monthly_used')->default(0);
            }

            if (!Schema::hasColumn('subscriptions', 'reports_reset_at')) {
                $table->timestamp('reports_reset_at')->nullable();
            }

            if (!Schema::hasColumn('subscriptions', 'quantity')) {
                $table->integer('quantity')->default(1);
            }

            if (!Schema::hasColumn('subscriptions', 'features')) {
                $table->json('features')->nullable();
            }

            if (!Schema::hasColumn('subscriptions', 'cancel_at_period_end')) {
                $table->boolean('cancel_at_period_end')->default(false);
            }

            if (!Schema::hasColumn('subscriptions', 'paused_at')) {
                $table->timestamp('paused_at')->nullable();
            }

            if (!Schema::hasColumn('subscriptions', 'resume_at')) {
                $table->timestamp('resume_at')->nullable();
            }
        });

        // Copy stripe_status to status if both exist
        if (Schema::hasColumn('subscriptions', 'stripe_status') && Schema::hasColumn('subscriptions', 'status')) {
            \DB::statement("UPDATE subscriptions SET status = stripe_status WHERE status = 'active' OR status IS NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $columnsToRemove = [
                'status', 'stripe_subscription_id', 'stripe_customer_id',
                'bank_connections_limit', 'bank_connections_used',
                'users_limit', 'users_used', 'sites_limit', 'sites_used',
                'reports_monthly_limit', 'reports_monthly_used', 'reports_reset_at',
                'quantity', 'features', 'cancel_at_period_end', 'paused_at', 'resume_at'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('subscriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

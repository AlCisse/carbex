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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');

            // Stripe Information
            $table->string('stripe_id')->unique();
            $table->string('stripe_status');
            $table->string('stripe_price_id')->nullable();

            // Plan Information
            $table->enum('plan', [
                'starter',      // 79 EUR/month
                'business',     // 149 EUR/month
                'professional', // 199 EUR/month
                'enterprise',   // 249 EUR/month
            ])->default('starter');

            // Billing
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('EUR');

            // Trial
            $table->timestamp('trial_ends_at')->nullable();

            // Subscription Period
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();

            // Cancellation
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('cancellation_reason')->nullable();

            // Plan Limits
            $table->integer('max_bank_connections')->default(1);
            $table->integer('max_transactions_per_month')->default(500);
            $table->integer('max_users')->default(1);
            $table->integer('max_sites')->default(1);
            $table->boolean('api_access')->default(false);
            $table->boolean('custom_reports')->default(false);
            $table->boolean('priority_support')->default(false);

            // Usage Tracking
            $table->integer('current_bank_connections')->default(0);
            $table->integer('current_transactions_month')->default(0);
            $table->integer('current_users')->default(0);
            $table->timestamp('usage_reset_at')->nullable();

            // Metadata
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            // Indexes
            $table->index('organization_id');
            $table->index('stripe_status');
            $table->index('plan');
            $table->index('ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

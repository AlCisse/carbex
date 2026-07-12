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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('subscription_id')->nullable();

            // Stripe Information
            $table->string('stripe_id')->unique();

            // Invoice Details
            $table->string('number')->nullable();
            $table->enum('status', [
                'draft',
                'open',
                'paid',
                'void',
                'uncollectible',
            ])->default('draft');

            // Amounts
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('currency', 3)->default('EUR');

            // Tax
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->string('tax_type')->nullable(); // VAT, GST, etc.

            // Billing Period
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();

            // Dates
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('voided_at')->nullable();

            // Payment
            $table->string('payment_intent_id')->nullable();
            $table->string('payment_method')->nullable();

            // URLs
            $table->string('invoice_pdf_url')->nullable();
            $table->string('hosted_invoice_url')->nullable();

            // Billing Address
            $table->string('billing_name')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country', 2)->nullable();
            $table->string('billing_vat_number')->nullable();

            // Metadata
            $table->json('line_items')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('subscription_id')
                ->references('id')
                ->on('subscriptions')
                ->onDelete('set null');

            // Indexes
            $table->index('organization_id');
            $table->index('subscription_id');
            $table->index('status');
            $table->index('due_date');
            $table->index(['organization_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

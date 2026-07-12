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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('bank_account_id')->nullable();
            $table->uuid('category_id')->nullable();

            // Provider Information
            $table->string('provider_transaction_id')->nullable();

            // Transaction Details
            $table->date('date');
            $table->date('value_date')->nullable();
            $table->string('description');
            $table->string('clean_description')->nullable(); // AI cleaned
            $table->string('merchant_name')->nullable();

            // Amount
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('EUR');
            $table->decimal('amount_eur', 15, 2)->nullable(); // Converted to EUR

            // MCC Code
            $table->string('mcc_code', 4)->nullable();
            $table->string('mcc_description')->nullable();

            // Categorization
            $table->enum('categorization_source', [
                'mcc',          // Based on MCC code
                'ai',           // AI categorization
                'rule',         // Rule-based
                'manual',       // User manual
                'imported',     // From CSV import
            ])->nullable();
            $table->decimal('categorization_confidence', 3, 2)->nullable(); // 0.00 to 1.00

            // Validation Status
            $table->enum('status', [
                'pending',      // Needs review
                'validated',    // User confirmed
                'excluded',     // Excluded from calculations
                'duplicate',    // Marked as duplicate
            ])->default('pending');
            $table->unsignedBigInteger('validated_by')->nullable();
            $table->timestamp('validated_at')->nullable();

            // Exclusion reason
            $table->string('exclusion_reason')->nullable();

            // Source
            $table->enum('source', [
                'bank_sync',
                'csv_import',
                'manual_entry',
                'api',
            ])->default('bank_sync');

            // Metadata
            $table->json('raw_data')->nullable(); // Original data from provider
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('bank_account_id')
                ->references('id')
                ->on('bank_accounts')
                ->onDelete('set null');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('set null');

            $table->foreign('validated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Indexes
            $table->index('organization_id');
            $table->index('bank_account_id');
            $table->index('category_id');
            $table->index('date');
            $table->index('status');
            $table->index('mcc_code');
            $table->index(['organization_id', 'date']);
            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'category_id', 'date']);

            // Prevent duplicates
            $table->unique(['bank_account_id', 'provider_transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

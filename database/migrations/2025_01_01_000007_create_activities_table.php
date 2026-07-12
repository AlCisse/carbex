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
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('site_id')->nullable();
            $table->uuid('category_id');
            $table->uuid('transaction_id')->nullable(); // Link to transaction if from bank

            // Activity Details
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('date');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            // Quantity & Unit
            $table->decimal('quantity', 15, 4);
            $table->string('unit'); // kWh, liter, km, night, EUR, etc.

            // Monetary Value (if applicable)
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('currency', 3)->default('EUR');

            // Source
            $table->enum('source', [
                'transaction',    // From bank transaction
                'manual',         // Manual entry
                'invoice',        // From invoice/OCR
                'meter',          // From meter reading
                'estimate',       // Estimated value
                'api',            // From external API
            ])->default('transaction');

            // Evidence
            $table->string('evidence_type')->nullable(); // invoice, receipt, contract
            $table->string('evidence_path')->nullable(); // S3 path
            $table->string('evidence_reference')->nullable(); // Invoice number, etc.

            // Metadata
            $table->json('metadata')->nullable();

            // Status
            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('set null');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('restrict');

            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->onDelete('set null');

            $table->foreign('verified_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Indexes
            $table->index('organization_id');
            $table->index('site_id');
            $table->index('category_id');
            $table->index('date');
            $table->index(['organization_id', 'date']);
            $table->index(['organization_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

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
        // Suppliers table
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country', 2)->default('FR');
            $table->string('business_id')->nullable(); // SIRET, VAT number, etc.
            $table->string('sector')->nullable(); // NACE sector code
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->json('categories')->nullable(); // Product/service categories
            $table->decimal('annual_spend', 15, 2)->nullable(); // Annual spend with supplier
            $table->string('currency', 3)->default('EUR');
            $table->enum('status', ['pending', 'invited', 'active', 'inactive'])->default('pending');
            $table->enum('data_quality', ['none', 'estimated', 'supplier_specific', 'verified'])->default('none');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'name']);
        });

        // Supplier invitations table
        Schema::create('supplier_invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id');
            $table->uuid('organization_id');
            $table->unsignedBigInteger('invited_by'); // User who sent invitation
            $table->string('token', 64)->unique();
            $table->string('email');
            $table->enum('status', ['pending', 'sent', 'opened', 'completed', 'expired', 'cancelled'])->default('pending');
            $table->integer('year'); // Reporting year
            $table->json('requested_data')->nullable(); // What data is requested
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at');
            $table->integer('reminder_count')->default(0);
            $table->timestamp('last_reminder_at')->nullable();
            $table->text('message')->nullable(); // Custom message
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('cascade');

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('invited_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index(['token']);
            $table->index(['supplier_id', 'year']);
            $table->index(['status', 'expires_at']);
        });

        // Supplier emission data table
        Schema::create('supplier_emissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id');
            $table->uuid('organization_id');
            $table->uuid('invitation_id')->nullable();
            $table->integer('year');

            // Scope 1 emissions
            $table->decimal('scope1_total', 15, 4)->nullable();
            $table->json('scope1_breakdown')->nullable();

            // Scope 2 emissions
            $table->decimal('scope2_location', 15, 4)->nullable();
            $table->decimal('scope2_market', 15, 4)->nullable();
            $table->json('scope2_breakdown')->nullable();

            // Scope 3 emissions (optional)
            $table->decimal('scope3_total', 15, 4)->nullable();
            $table->json('scope3_breakdown')->nullable();

            // Intensity metrics
            $table->decimal('emission_intensity', 15, 6)->nullable(); // kgCO2e per EUR
            $table->decimal('revenue', 15, 2)->nullable();
            $table->string('revenue_currency', 3)->default('EUR');
            $table->integer('employees')->nullable();

            // Data quality
            $table->enum('data_source', ['estimated', 'supplier_reported', 'verified', 'third_party'])->default('estimated');
            $table->string('verification_standard')->nullable(); // ISO 14064, etc.
            $table->string('verifier_name')->nullable();
            $table->date('verification_date')->nullable();
            $table->decimal('uncertainty_percent', 5, 2)->nullable();

            // Metadata
            $table->json('methodology')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('validated_by')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('cascade');

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('invitation_id')
                ->references('id')
                ->on('supplier_invitations')
                ->onDelete('set null');

            $table->unique(['supplier_id', 'year']);
            $table->index(['organization_id', 'year']);
        });

        // Supplier products/services for allocation
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id');
            $table->uuid('supplier_emission_id')->nullable();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('quantity_purchased', 15, 4)->nullable();
            $table->decimal('spend_amount', 15, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->decimal('emission_factor', 15, 6)->nullable(); // kgCO2e per unit
            $table->string('emission_factor_source')->nullable();
            $table->decimal('allocated_emissions', 15, 4)->nullable();
            $table->integer('year');
            $table->timestamps();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('cascade');

            $table->foreign('supplier_emission_id')
                ->references('id')
                ->on('supplier_emissions')
                ->onDelete('set null');

            $table->index(['supplier_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
        Schema::dropIfExists('supplier_emissions');
        Schema::dropIfExists('supplier_invitations');
        Schema::dropIfExists('suppliers');
    }
};

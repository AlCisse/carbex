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
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Basic Information
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('slug')->unique();

            // Country & Locale
            $table->string('country', 2)->default('FR'); // ISO 3166-1 alpha-2
            $table->string('locale', 5)->default('fr_FR');
            $table->string('timezone')->default('Europe/Paris');
            $table->string('currency', 3)->default('EUR');

            // Business Information
            $table->string('business_id')->nullable(); // SIRET (FR) or HRB (DE)
            $table->string('vat_number')->nullable();
            $table->string('sector')->nullable(); // NACE code
            $table->enum('size', ['micro', 'small', 'medium', 'large'])->default('small');
            $table->integer('employee_count')->nullable();
            $table->decimal('annual_turnover', 15, 2)->nullable();

            // Address
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('state')->nullable();

            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Fiscal Year
            $table->tinyInteger('fiscal_year_start_month')->default(1); // 1 = January

            // Settings (JSON)
            $table->json('settings')->nullable();

            // Onboarding
            $table->boolean('onboarding_completed')->default(false);
            $table->timestamp('onboarded_at')->nullable();

            // Status
            $table->enum('status', ['active', 'suspended', 'deleted'])->default('active');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('country');
            $table->index('status');
            $table->index(['country', 'sector']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};

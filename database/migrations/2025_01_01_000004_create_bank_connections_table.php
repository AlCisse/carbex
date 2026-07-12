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
        Schema::create('bank_connections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');

            // Provider Information
            $table->string('provider'); // bridge, finapi
            $table->string('provider_item_id'); // External ID from provider
            $table->string('bank_id')->nullable(); // Bank identifier
            $table->string('bank_name')->nullable();
            $table->string('bank_logo_url')->nullable();

            // Connection Status
            $table->enum('status', [
                'pending',      // Initial connection in progress
                'active',       // Connection working
                'error',        // Connection has errors
                'expired',      // Tokens expired, needs refresh
                'disconnected', // User disconnected
            ])->default('pending');
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();

            // OAuth Tokens (encrypted)
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();

            // Sync Information
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('last_successful_sync_at')->nullable();
            $table->integer('sync_error_count')->default(0);
            $table->timestamp('next_sync_at')->nullable();

            // Consent
            $table->timestamp('consent_given_at')->nullable();
            $table->timestamp('consent_expires_at')->nullable();

            // Metadata
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            // Indexes
            $table->index('organization_id');
            $table->index('provider');
            $table->index('status');
            $table->index(['organization_id', 'status']);
            $table->unique(['provider', 'provider_item_id']);
        });

        // Bank Accounts table
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bank_connection_id');
            $table->uuid('organization_id');

            // Account Information
            $table->string('provider_account_id');
            $table->string('name');
            $table->string('iban')->nullable();
            $table->string('currency', 3)->default('EUR');

            // Account Type
            $table->enum('type', [
                'checking',
                'savings',
                'credit_card',
                'loan',
                'investment',
                'other',
            ])->default('checking');

            // Balance
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamp('balance_updated_at')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('sync_enabled')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('bank_connection_id')
                ->references('id')
                ->on('bank_connections')
                ->onDelete('cascade');

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            // Indexes
            $table->index('bank_connection_id');
            $table->index('organization_id');
            $table->unique(['bank_connection_id', 'provider_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('bank_connections');
    }
};

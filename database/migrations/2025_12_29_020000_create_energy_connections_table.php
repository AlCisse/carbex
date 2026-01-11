<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energy_connections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('site_id')->nullable();

            // Provider info
            $table->string('provider'); // enedis, grdf
            $table->string('provider_customer_id')->nullable(); // PRM for Enedis, PCE for GRDF
            $table->string('contract_type')->nullable(); // residential, professional
            $table->string('meter_type')->nullable(); // Linky, etc.

            // OAuth tokens (encrypted)
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();

            // Connection status
            $table->string('status')->default('pending'); // pending, active, expired, revoked, error
            $table->text('error_message')->nullable();
            $table->timestamp('connected_at')->nullable();

            // Consent info
            $table->timestamp('consent_expires_at')->nullable();
            $table->json('consent_scopes')->nullable();

            // Sync status
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('next_sync_at')->nullable();
            $table->integer('sync_failures')->default(0);

            // Metadata
            $table->string('label')->nullable(); // User-friendly name
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('set null');

            $table->index(['organization_id', 'provider']);
            $table->index(['provider', 'status']);
            $table->index('next_sync_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_connections');
    }
};

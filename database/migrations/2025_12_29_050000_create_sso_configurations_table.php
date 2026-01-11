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
        Schema::create('sso_configurations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->string('name'); // Display name
            $table->string('provider'); // azure_ad, okta, google_workspace, custom, etc.
            $table->boolean('is_enabled')->default(false);
            $table->boolean('is_primary')->default(false);

            // IdP Configuration
            $table->string('idp_entity_id');
            $table->string('idp_sso_url');
            $table->string('idp_slo_url')->nullable();
            $table->text('idp_x509_certificate');
            $table->json('idp_metadata')->nullable(); // Raw metadata if available

            // Provider-specific settings
            $table->json('provider_settings')->nullable(); // tenant_id, domain, app_id, etc.

            // Attribute mapping overrides
            $table->json('attribute_mapping')->nullable();

            // Role mapping overrides
            $table->json('role_mapping')->nullable();

            // Domain restrictions (only allow emails from these domains)
            $table->json('allowed_domains')->nullable();

            // Auto-provisioning settings
            $table->boolean('auto_provision_users')->default(true);
            $table->boolean('auto_update_users')->default(true);
            $table->string('default_role')->default('member');

            // Usage statistics
            $table->integer('login_count')->default(0);
            $table->timestamp('last_login_at')->nullable();

            // Configuration status
            $table->enum('status', ['pending', 'testing', 'active', 'disabled'])->default('pending');
            $table->text('status_message')->nullable();
            $table->timestamp('tested_at')->nullable();

            $table->timestamps();

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->unique(['organization_id', 'idp_entity_id']);
            $table->index(['organization_id', 'is_enabled']);
        });

        // SSO login attempts for auditing
        Schema::create('sso_login_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sso_configuration_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('name_id')->nullable();
            $table->enum('status', ['success', 'failed', 'error']);
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('saml_attributes')->nullable();
            $table->timestamp('created_at');

            $table->foreign('sso_configuration_id')
                ->references('id')
                ->on('sso_configurations')
                ->onDelete('cascade');

            $table->index(['sso_configuration_id', 'created_at']);
            $table->index(['email', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sso_login_attempts');
        Schema::dropIfExists('sso_configurations');
    }
};

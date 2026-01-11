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
        Schema::create('api_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('key', 64)->unique();
            $table->string('key_prefix', 8); // First 8 chars for display
            $table->text('description')->nullable();

            // Permissions/Scopes
            $table->json('scopes')->nullable(); // ['read:emissions', 'write:transactions', etc.]

            // Rate limiting
            $table->integer('rate_limit_per_minute')->default(60);
            $table->integer('rate_limit_per_day')->default(10000);

            // IP restrictions
            $table->json('allowed_ips')->nullable();

            // Usage tracking
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedBigInteger('total_requests')->default(0);

            // Expiration
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'is_active']);
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};

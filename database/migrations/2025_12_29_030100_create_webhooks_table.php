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
        Schema::create('webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->string('secret', 64); // For HMAC signature verification

            // Events to subscribe to
            $table->json('events'); // ['emission.calculated', 'report.generated', etc.]

            // Configuration
            $table->json('headers')->nullable(); // Custom headers to include
            $table->integer('timeout_seconds')->default(30);
            $table->integer('max_retries')->default(5);

            // Status tracking
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->integer('consecutive_failures')->default(0);
            $table->timestamp('disabled_at')->nullable();
            $table->string('disabled_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'is_active']);
        });

        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('webhook_id')->constrained()->cascadeOnDelete();
            $table->string('event');
            $table->json('payload');

            // Delivery status
            $table->enum('status', ['pending', 'success', 'failed', 'retrying'])->default('pending');
            $table->integer('attempt')->default(0);
            $table->timestamp('next_retry_at')->nullable();

            // Response tracking
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['webhook_id', 'status']);
            $table->index(['status', 'next_retry_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhooks');
    }
};

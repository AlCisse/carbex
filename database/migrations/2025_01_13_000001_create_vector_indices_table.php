<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Vector indices table tracks the state of each uSearch index
     * for monitoring and management purposes.
     */
    public function up(): void
    {
        Schema::create('vector_indices', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->unique();
            $table->string('type', 32)->comment('factors, transactions, documents, actions');
            $table->unsignedInteger('dimensions')->default(1536);
            $table->string('metric', 10)->default('cos')->comment('cos, l2, ip');
            $table->unsignedBigInteger('vector_count')->default(0);
            $table->enum('status', ['active', 'building', 'error', 'disabled'])->default('active');
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('last_error_at')->nullable();
            $table->text('last_error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vector_indices');
    }
};

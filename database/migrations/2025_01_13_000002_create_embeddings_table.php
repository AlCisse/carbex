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
     * Embeddings table tracks which items have been indexed in uSearch.
     * The actual vectors are stored in the uSearch microservice, not here.
     * This table provides a mapping and sync tracking.
     */
    public function up(): void
    {
        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vector_index_id')->constrained('vector_indices')->cascadeOnDelete();
            $table->uuidMorphs('embeddable'); // embeddable_type, embeddable_id (UUID for EmissionFactor compatibility)
            $table->string('content_hash', 64)->comment('SHA256 hash of embedded content for change detection');
            $table->unsignedInteger('dimensions')->default(1536);
            $table->string('model', 64)->nullable()->comment('Embedding model used');
            $table->json('metadata')->nullable()->comment('Additional metadata stored with vector');
            $table->boolean('is_synced')->default(false)->comment('Whether vector is synced to uSearch');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            // Ensure unique embedding per item per index
            $table->unique(['vector_index_id', 'embeddable_type', 'embeddable_id'], 'embeddings_unique');

            // Index for finding unsynced embeddings
            $table->index(['is_synced', 'updated_at']);

            // Index for content change detection
            $table->index('content_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('embeddings');
    }
};

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
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');
            $table->enum('context_type', [
                'emission_entry',
                'action_suggestion',
                'factor_explanation',
                'report_help',
                'general',
            ])->default('general');
            $table->string('title')->nullable();
            $table->json('messages'); // [{role: 'user'|'assistant', content: string, timestamp: datetime}]
            $table->json('metadata')->nullable(); // {scope, category_code, assessment_id, etc.}
            $table->integer('token_count')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['organization_id', 'context_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};

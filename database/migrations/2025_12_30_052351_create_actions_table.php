<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Action (Plan de transition) - Reduction actions for transition planning
     * Constitution Carbex v3.0 - Section 7, 2.8
     */
    public function up(): void
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');

            // Related emission category (optional)
            $table->uuid('category_id')->nullable();

            // Action details
            $table->string('title');
            $table->text('description')->nullable();

            // Status workflow: todo -> in_progress -> completed
            $table->enum('status', ['todo', 'in_progress', 'completed'])->default('todo');

            // Timeline
            $table->date('due_date')->nullable();

            // Impact & cost
            $table->decimal('co2_reduction_percent', 5, 2)->nullable(); // Expected % reduction
            $table->decimal('estimated_cost', 15, 2)->nullable();       // EUR

            // Complexity
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');

            // Prioritization
            $table->unsignedInteger('priority')->default(0);

            // Assignment (references users.id which is bigint)
            $table->unsignedBigInteger('assigned_to')->nullable();

            // Additional data
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Constraints
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('assigned_to')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Indexes for common queries
            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'due_date']);
            $table->index(['organization_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};

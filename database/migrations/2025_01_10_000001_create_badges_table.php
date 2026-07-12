<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for badges and user_badges tables
 *
 * Constitution LinsCarbon v3.0 - Section 9.9 (Gamification)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Table des types de badges
        Schema::create('badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('name_de')->nullable();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_de')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('emerald');
            $table->string('category'); // assessment, reduction, engagement, expert
            $table->json('criteria')->nullable(); // Critères d'obtention
            $table->integer('points')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Table pivot badges obtenus par organisation
        Schema::create('organization_badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('badge_id');
            $table->timestamp('earned_at');
            $table->string('share_token')->unique()->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')
                ->references('id')->on('organizations')
                ->onDelete('cascade');
            $table->foreign('badge_id')
                ->references('id')->on('badges')
                ->onDelete('cascade');
            $table->unique(['organization_id', 'badge_id']);
        });

        // Table pivot badges obtenus par utilisateur
        Schema::create('user_badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->uuid('badge_id');
            $table->timestamp('earned_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('badge_id')
                ->references('id')->on('badges')
                ->onDelete('cascade');
            $table->unique(['user_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('organization_badges');
        Schema::dropIfExists('badges');
    }
};

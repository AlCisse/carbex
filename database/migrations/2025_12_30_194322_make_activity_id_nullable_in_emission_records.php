<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Make activity_id, category_id, and emission_factor_id nullable.
     * EmissionRecords can be created from various sources (transactions, OCR, manual)
     * that don't always require these relationships.
     */
    public function up(): void
    {
        // First drop foreign keys
        Schema::table('emission_records', function (Blueprint $table) {
            $table->dropForeign(['activity_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['emission_factor_id']);
        });

        // Make columns nullable
        Schema::table('emission_records', function (Blueprint $table) {
            $table->uuid('activity_id')->nullable()->change();
            $table->uuid('category_id')->nullable()->change();
            $table->uuid('emission_factor_id')->nullable()->change();
        });

        // Re-add foreign keys with SET NULL on delete
        Schema::table('emission_records', function (Blueprint $table) {
            $table->foreign('activity_id')
                ->references('id')
                ->on('activities')
                ->onDelete('set null');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('set null');

            $table->foreign('emission_factor_id')
                ->references('id')
                ->on('emission_factors')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reliably reverse to NOT NULL without data loss
    }
};

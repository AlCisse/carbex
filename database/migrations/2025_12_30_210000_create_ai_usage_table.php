<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
            $table->date('usage_date')->index();
            $table->unsignedInteger('requests_count')->default(0);
            $table->unsignedInteger('tokens_used')->default(0);
            $table->timestamps();

            $table->unique(['organization_id', 'usage_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage');
    }
};

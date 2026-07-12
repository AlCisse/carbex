<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_usage', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            $table->string('provider', 50)->nullable()->after('usage_date');
            $table->string('model', 100)->nullable()->after('provider');
            $table->string('feature', 50)->nullable()->after('model');
            $table->unsignedBigInteger('input_tokens')->default(0)->after('requests_count');
            $table->unsignedBigInteger('output_tokens')->default(0)->after('input_tokens');
            $table->unsignedInteger('cost_cents')->default(0)->after('tokens_used');
        });
    }

    public function down(): void
    {
        Schema::table('ai_usage', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'provider',
                'model',
                'feature',
                'input_tokens',
                'output_tokens',
                'cost_cents',
            ]);
        });
    }
};

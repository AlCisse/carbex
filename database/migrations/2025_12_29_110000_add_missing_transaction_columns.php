<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'confidence')) {
                $table->decimal('confidence', 5, 4)->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('transactions', 'validated_at')) {
                $table->timestamp('validated_at')->nullable()->after('confidence');
            }
            if (!Schema::hasColumn('transactions', 'validated_by')) {
                $table->unsignedBigInteger('validated_by')->nullable()->after('validated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['confidence', 'validated_at', 'validated_by']);
        });
    }
};

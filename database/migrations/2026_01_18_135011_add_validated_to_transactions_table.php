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
        if (!Schema::hasColumn('transactions', 'validated')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->boolean('validated')->default(false);
            });
        }

        if (!Schema::hasColumn('transactions', 'confidence')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->decimal('confidence', 5, 4)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'validated')) {
                $table->dropColumn('validated');
            }
            if (Schema::hasColumn('transactions', 'confidence')) {
                $table->dropColumn('confidence');
            }
        });
    }
};

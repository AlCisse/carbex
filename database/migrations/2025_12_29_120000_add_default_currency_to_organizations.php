<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'default_currency')) {
                $table->string('default_currency', 3)->default('EUR')->after('currency');
            }
        });
        
        // Sync existing currency values to default_currency
        \DB::statement('UPDATE organizations SET default_currency = currency WHERE default_currency IS NULL OR default_currency = \'EUR\'');
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('default_currency');
        });
    }
};

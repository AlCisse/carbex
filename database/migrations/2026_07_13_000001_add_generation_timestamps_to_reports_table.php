<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Report model (fillable/casts) and report-list view expect these
     * columns, but the original reports table never got them: generation
     * timestamps, download tracking, and parameters/summary payloads.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table): void {
            $table->timestamp('started_at')->nullable()->after('status');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->unsignedInteger('download_count')->default(0)->after('file_size');
            $table->timestamp('last_downloaded_at')->nullable()->after('download_count');
            $table->json('parameters')->nullable()->after('settings');
            $table->json('summary')->nullable()->after('parameters');
            $table->timestamp('expires_at')->nullable()->after('share_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table): void {
            $table->dropColumn([
                'started_at',
                'completed_at',
                'download_count',
                'last_downloaded_at',
                'parameters',
                'summary',
                'expires_at',
            ]);
        });
    }
};

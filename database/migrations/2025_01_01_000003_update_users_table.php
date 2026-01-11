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
        Schema::table('users', function (Blueprint $table) {
            // Add organization relationship
            $table->uuid('organization_id')->nullable()->after('id');

            // User role within organization
            $table->enum('role', [
                'owner',      // Full access, can delete org
                'admin',      // Full access except delete org
                'manager',    // Can manage data, users, reports
                'analyst',    // Can view and analyze data
                'viewer',     // Read-only access
            ])->default('viewer')->after('email');

            // Profile
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->string('avatar')->nullable();

            // Locale preferences
            $table->string('locale', 5)->default('fr_FR');
            $table->string('timezone')->default('Europe/Paris');

            // Notifications
            $table->json('notification_preferences')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            // Two-factor authentication
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();

            // Soft deletes
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            // Indexes
            $table->index('organization_id');
            $table->index(['organization_id', 'role']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn([
                'organization_id',
                'role',
                'first_name',
                'last_name',
                'phone',
                'job_title',
                'department',
                'avatar',
                'locale',
                'timezone',
                'notification_preferences',
                'is_active',
                'last_login_at',
                'last_login_ip',
                'two_factor_enabled',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
                'deleted_at',
            ]);
        });
    }
};

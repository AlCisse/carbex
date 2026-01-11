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
        Schema::table('emission_records', function (Blueprint $table) {
            // Add missing columns that the model expects
            if (!Schema::hasColumn('emission_records', 'co2e_kg')) {
                $table->decimal('co2e_kg', 15, 6)->default(0)->after('factor_source');
            }
            if (!Schema::hasColumn('emission_records', 'co2_kg')) {
                $table->decimal('co2_kg', 15, 6)->default(0)->after('co2e_kg');
            }
            if (!Schema::hasColumn('emission_records', 'ch4_kg')) {
                $table->decimal('ch4_kg', 15, 6)->default(0)->after('co2_kg');
            }
            if (!Schema::hasColumn('emission_records', 'n2o_kg')) {
                $table->decimal('n2o_kg', 15, 6)->default(0)->after('ch4_kg');
            }
            if (!Schema::hasColumn('emission_records', 'transaction_id')) {
                $table->uuid('transaction_id')->nullable()->after('organization_id');
            }
            if (!Schema::hasColumn('emission_records', 'quantity')) {
                $table->decimal('quantity', 15, 4)->default(0)->after('scope_3_category');
            }
            if (!Schema::hasColumn('emission_records', 'unit')) {
                $table->string('unit')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('emission_records', 'period_start')) {
                $table->date('period_start')->nullable()->after('date');
            }
            if (!Schema::hasColumn('emission_records', 'period_end')) {
                $table->date('period_end')->nullable()->after('period_start');
            }
            if (!Schema::hasColumn('emission_records', 'source_type')) {
                $table->string('source_type')->default('transaction')->after('data_quality');
            }
            if (!Schema::hasColumn('emission_records', 'is_estimated')) {
                $table->boolean('is_estimated')->default(false)->after('source_type');
            }
            if (!Schema::hasColumn('emission_records', 'notes')) {
                $table->text('notes')->nullable()->after('is_estimated');
            }
            if (!Schema::hasColumn('emission_records', 'factor_snapshot')) {
                $table->json('factor_snapshot')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('emission_records', 'metadata')) {
                $table->json('metadata')->nullable()->after('factor_snapshot');
            }
        });

        // Copy data from old columns to new columns if they exist
        if (Schema::hasColumn('emission_records', 'emissions_total') && Schema::hasColumn('emission_records', 'co2e_kg')) {
            \DB::statement('UPDATE emission_records SET co2e_kg = emissions_total WHERE co2e_kg = 0 OR co2e_kg IS NULL');
        }
        if (Schema::hasColumn('emission_records', 'emissions_co2') && Schema::hasColumn('emission_records', 'co2_kg')) {
            \DB::statement('UPDATE emission_records SET co2_kg = emissions_co2 WHERE co2_kg = 0 OR co2_kg IS NULL');
        }
        if (Schema::hasColumn('emission_records', 'emissions_ch4') && Schema::hasColumn('emission_records', 'ch4_kg')) {
            \DB::statement('UPDATE emission_records SET ch4_kg = emissions_ch4 WHERE ch4_kg = 0 OR ch4_kg IS NULL');
        }
        if (Schema::hasColumn('emission_records', 'emissions_n2o') && Schema::hasColumn('emission_records', 'n2o_kg')) {
            \DB::statement('UPDATE emission_records SET n2o_kg = emissions_n2o WHERE n2o_kg = 0 OR n2o_kg IS NULL');
        }
        if (Schema::hasColumn('emission_records', 'activity_quantity') && Schema::hasColumn('emission_records', 'quantity')) {
            \DB::statement('UPDATE emission_records SET quantity = activity_quantity WHERE quantity = 0 OR quantity IS NULL');
        }
        if (Schema::hasColumn('emission_records', 'activity_unit') && Schema::hasColumn('emission_records', 'unit')) {
            \DB::statement('UPDATE emission_records SET unit = activity_unit WHERE unit IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emission_records', function (Blueprint $table) {
            $table->dropColumn([
                'co2e_kg',
                'co2_kg', 
                'ch4_kg',
                'n2o_kg',
                'transaction_id',
                'quantity',
                'unit',
                'period_start',
                'period_end',
                'source_type',
                'is_estimated',
                'notes',
                'factor_snapshot',
                'metadata',
            ]);
        });
    }
};

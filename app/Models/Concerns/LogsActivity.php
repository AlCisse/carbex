<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Trait for logging model activity.
 *
 * This trait provides activity logging functionality compatible with
 * spatie/laravel-activitylog. It can be used standalone or extended
 * when the package is installed.
 */
trait LogsActivity
{
    /**
     * Boot the trait.
     */
    public static function bootLogsActivity(): void
    {
        // Skip if activitylog package is not installed
        if (! class_exists(\Spatie\Activitylog\Traits\LogsActivity::class)) {
            static::bootManualLogging();

            return;
        }
    }

    /**
     * Boot manual logging when package is not available.
     */
    protected static function bootManualLogging(): void
    {
        static::created(function (Model $model) {
            static::logManualActivity('created', $model);
        });

        static::updated(function (Model $model) {
            static::logManualActivity('updated', $model);
        });

        static::deleted(function (Model $model) {
            static::logManualActivity('deleted', $model);
        });
    }

    /**
     * Log activity manually to database.
     */
    protected static function logManualActivity(string $event, Model $model): void
    {
        if (! config('activitylog.enabled', true)) {
            return;
        }

        $logName = static::getLogName();
        $description = static::getDescriptionForEvent($event);

        // Get changed attributes
        $properties = [];
        if ($event === 'updated') {
            $properties['old'] = array_intersect_key(
                $model->getOriginal(),
                $model->getDirty()
            );
            $properties['attributes'] = $model->getDirty();
        } elseif ($event === 'created') {
            $properties['attributes'] = $model->getAttributes();
        }

        // Remove sensitive attributes
        $excluded = config('activitylog.linscarbon.excluded_attributes', []);
        foreach (['old', 'attributes'] as $key) {
            if (isset($properties[$key])) {
                $properties[$key] = array_diff_key($properties[$key], array_flip($excluded));
            }
        }

        // Store in database using raw query if Activity model not available
        // Use savepoint to prevent PostgreSQL transaction from being marked as failed
        $tableName = config('activitylog.table_name', 'activity_log');

        // Check if table exists before attempting insert to avoid transaction failure
        if (! \Schema::hasTable($tableName)) {
            return;
        }

        try {
            \DB::table($tableName)->insert([
                'log_name' => $logName,
                'description' => $description,
                'subject_type' => get_class($model),
                'subject_id' => $model->getKey(),
                'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
                'causer_id' => Auth::id(),
                'properties' => json_encode($properties),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - table might have been dropped or have schema issues
            \Log::debug('Activity log failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the log name for this model.
     */
    protected static function getLogName(): string
    {
        $modelName = class_basename(static::class);
        $logNames = config('activitylog.linscarbon.log_names', []);

        return match ($modelName) {
            'Transaction' => $logNames['transactions'] ?? 'default',
            'EmissionRecord' => $logNames['emissions'] ?? 'default',
            'BankConnection', 'BankAccount' => $logNames['banking'] ?? 'default',
            'Organization', 'Site' => $logNames['organization'] ?? 'default',
            'Report' => $logNames['reports'] ?? 'default',
            'User' => $logNames['auth'] ?? 'default',
            default => 'default',
        };
    }

    /**
     * Get description for the event.
     */
    protected static function getDescriptionForEvent(string $event): string
    {
        $modelName = class_basename(static::class);

        return match ($event) {
            'created' => "{$modelName} created",
            'updated' => "{$modelName} updated",
            'deleted' => "{$modelName} deleted",
            default => "{$modelName} {$event}",
        };
    }

    /**
     * Get attributes that should be logged.
     */
    public function getActivitylogOptions(): array
    {
        return [
            'log_name' => static::getLogName(),
            'log_only_dirty' => true,
            'log_attributes_to_ignore' => config('activitylog.linscarbon.excluded_attributes', []),
        ];
    }
}

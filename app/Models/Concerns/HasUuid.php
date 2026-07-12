<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Trait HasUuid
 *
 * Automatically generates UUIDs for models.
 */
trait HasUuid
{
    /**
     * Boot the trait.
     */
    public static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}

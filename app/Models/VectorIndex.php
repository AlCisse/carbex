<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * VectorIndex Model
 *
 * Tracks the state and configuration of vector indexes in uSearch.
 * Used for monitoring, management, and sync tracking.
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $dimensions
 * @property string $metric
 * @property int $vector_count
 * @property string $status
 * @property \Carbon\Carbon|null $last_sync_at
 * @property \Carbon\Carbon|null $last_error_at
 * @property string|null $last_error_message
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class VectorIndex extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'type',
        'dimensions',
        'metric',
        'vector_count',
        'status',
        'last_sync_at',
        'last_error_at',
        'last_error_message',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dimensions' => 'integer',
        'vector_count' => 'integer',
        'last_sync_at' => 'datetime',
        'last_error_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Index types.
     */
    public const TYPE_FACTORS = 'factors';
    public const TYPE_TRANSACTIONS = 'transactions';
    public const TYPE_DOCUMENTS = 'documents';
    public const TYPE_ACTIONS = 'actions';

    /**
     * Index statuses.
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BUILDING = 'building';
    public const STATUS_ERROR = 'error';
    public const STATUS_DISABLED = 'disabled';

    /**
     * Distance metrics.
     */
    public const METRIC_COSINE = 'cos';
    public const METRIC_L2 = 'l2';
    public const METRIC_INNER_PRODUCT = 'ip';

    /**
     * Get the embeddings for this index.
     */
    public function embeddings(): HasMany
    {
        return $this->hasMany(Embedding::class);
    }

    /**
     * Scope a query to only include active indexes.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include indexes of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if the index is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the index has errors.
     */
    public function hasError(): bool
    {
        return $this->status === self::STATUS_ERROR;
    }

    /**
     * Mark the index as building.
     */
    public function markAsBuilding(): void
    {
        $this->update([
            'status' => self::STATUS_BUILDING,
        ]);
    }

    /**
     * Mark the index as active with updated vector count.
     */
    public function markAsActive(int $vectorCount): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'vector_count' => $vectorCount,
            'last_sync_at' => now(),
            'last_error_at' => null,
            'last_error_message' => null,
        ]);
    }

    /**
     * Mark the index as having an error.
     */
    public function markAsError(string $message): void
    {
        $this->update([
            'status' => self::STATUS_ERROR,
            'last_error_at' => now(),
            'last_error_message' => $message,
        ]);
    }

    /**
     * Get or create an index by name.
     */
    public static function findOrCreateByName(string $name, string $type, int $dimensions = 1536): self
    {
        return self::firstOrCreate(
            ['name' => $name],
            [
                'type' => $type,
                'dimensions' => $dimensions,
                'metric' => self::METRIC_COSINE,
                'status' => self::STATUS_ACTIVE,
            ]
        );
    }
}

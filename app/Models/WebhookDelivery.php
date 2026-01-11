<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
    use HasFactory;
    use HasUuids;

    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_RETRYING = 'retrying';

    protected $fillable = [
        'webhook_id',
        'event',
        'payload',
        'status',
        'attempt',
        'next_retry_at',
        'response_status',
        'response_body',
        'response_time_ms',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'next_retry_at' => 'datetime',
        'attempt' => 'integer',
        'response_status' => 'integer',
        'response_time_ms' => 'integer',
    ];

    /**
     * Calculate next retry delay using exponential backoff.
     *
     * Retry delays: 1min, 5min, 30min, 2h, 12h
     */
    public function calculateNextRetryDelay(): int
    {
        $delays = [60, 300, 1800, 7200, 43200]; // seconds

        $index = min($this->attempt, count($delays) - 1);

        return $delays[$index];
    }

    /**
     * Schedule next retry.
     */
    public function scheduleRetry(): void
    {
        $delay = $this->calculateNextRetryDelay();

        $this->update([
            'status' => self::STATUS_RETRYING,
            'next_retry_at' => now()->addSeconds($delay),
        ]);
    }

    /**
     * Mark as successful.
     */
    public function markAsSuccess(int $statusCode, ?string $body, int $responseTimeMs): void
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'response_status' => $statusCode,
            'response_body' => $body ? substr($body, 0, 10000) : null,
            'response_time_ms' => $responseTimeMs,
            'next_retry_at' => null,
            'error_message' => null,
        ]);
    }

    /**
     * Mark as failed.
     */
    public function markAsFailed(string $errorMessage, ?int $statusCode = null, ?string $body = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'response_status' => $statusCode,
            'response_body' => $body ? substr($body, 0, 10000) : null,
            'error_message' => $errorMessage,
            'next_retry_at' => null,
        ]);
    }

    /**
     * Check if delivery can be retried.
     */
    public function canRetry(): bool
    {
        return $this->attempt < $this->webhook->max_retries;
    }

    /**
     * Check if delivery was successful (2xx status).
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS
            || ($this->response_status >= 200 && $this->response_status < 300);
    }

    /**
     * Relationship: Webhook.
     */
    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}

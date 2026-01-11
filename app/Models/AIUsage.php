<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AIUsage extends Model
{
    use BelongsToOrganization, HasUuid;

    protected $table = 'ai_usage';

    protected $fillable = [
        'organization_id',
        'usage_date',
        'requests_count',
        'tokens_used',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'requests_count' => 'integer',
        'tokens_used' => 'integer',
    ];

    /**
     * Get or create today's usage record for an organization.
     */
    public static function getOrCreateToday(string $organizationId): self
    {
        return self::firstOrCreate(
            [
                'organization_id' => $organizationId,
                'usage_date' => Carbon::today(),
            ],
            [
                'requests_count' => 0,
                'tokens_used' => 0,
            ]
        );
    }

    /**
     * Increment the request count.
     */
    public function incrementRequests(int $tokens = 0): void
    {
        $this->increment('requests_count');

        if ($tokens > 0) {
            $this->increment('tokens_used', $tokens);
        }
    }

    /**
     * Get today's request count for an organization.
     */
    public static function getTodayCount(string $organizationId): int
    {
        return self::where('organization_id', $organizationId)
            ->where('usage_date', Carbon::today())
            ->value('requests_count') ?? 0;
    }

    /**
     * Get monthly request count for an organization.
     */
    public static function getMonthlyCount(string $organizationId): int
    {
        return self::where('organization_id', $organizationId)
            ->where('usage_date', '>=', Carbon::now()->startOfMonth())
            ->sum('requests_count');
    }

    /**
     * Scope for a specific date range.
     */
    public function scopeForPeriod($query, Carbon $start, Carbon $end)
    {
        return $query->whereBetween('usage_date', [$start, $end]);
    }

    /**
     * Scope for today.
     */
    public function scopeToday($query)
    {
        return $query->where('usage_date', Carbon::today());
    }

    /**
     * Scope for this month.
     */
    public function scopeThisMonth($query)
    {
        return $query->where('usage_date', '>=', Carbon::now()->startOfMonth());
    }
}

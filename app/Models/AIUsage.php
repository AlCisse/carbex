<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class AIUsage extends Model
{
    use BelongsToOrganization, HasUuid;

    protected $table = 'ai_usage';

    protected $fillable = [
        'organization_id',
        'user_id',
        'usage_date',
        'provider',
        'model',
        'feature',
        'requests_count',
        'input_tokens',
        'output_tokens',
        'tokens_used',
        'cost_cents',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'requests_count' => 'integer',
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
        'tokens_used' => 'integer',
        'cost_cents' => 'integer',
    ];

    /**
     * Token costs per provider (in cents per 1000 tokens).
     */
    public static array $tokenCosts = [
        'anthropic' => [
            'claude-sonnet-4-20250514' => ['input' => 0.3, 'output' => 1.5],
            'claude-3-5-sonnet-20241022' => ['input' => 0.3, 'output' => 1.5],
            'claude-3-5-haiku-20241022' => ['input' => 0.08, 'output' => 0.4],
            'claude-3-haiku-20240307' => ['input' => 0.025, 'output' => 0.125],
            'claude-3-opus-20240229' => ['input' => 1.5, 'output' => 7.5],
        ],
        'openai' => [
            'gpt-4.5-preview' => ['input' => 7.5, 'output' => 15.0],
            'gpt-4o' => ['input' => 0.25, 'output' => 1.0],
            'gpt-4o-mini' => ['input' => 0.015, 'output' => 0.06],
            'o3-mini' => ['input' => 0.11, 'output' => 0.44],
            'o1' => ['input' => 1.5, 'output' => 6.0],
            'o1-mini' => ['input' => 0.3, 'output' => 1.2],
            'gpt-4-turbo' => ['input' => 1.0, 'output' => 3.0],
            'gpt-3.5-turbo' => ['input' => 0.05, 'output' => 0.15],
        ],
        'google' => [
            'gemini-2.0-flash' => ['input' => 0.01, 'output' => 0.04],
            'gemini-2.0-flash-lite' => ['input' => 0.0075, 'output' => 0.03],
            'gemini-1.5-pro' => ['input' => 0.125, 'output' => 0.5],
            'gemini-1.5-flash' => ['input' => 0.0075, 'output' => 0.03],
            'gemini-pro' => ['input' => 0.05, 'output' => 0.15],
        ],
        'deepseek' => [
            'deepseek-chat' => ['input' => 0.014, 'output' => 0.028],
            'deepseek-coder' => ['input' => 0.014, 'output' => 0.028],
        ],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate cost for a request.
     */
    public static function calculateCost(string $provider, string $model, int $inputTokens, int $outputTokens): int
    {
        $costs = self::$tokenCosts[$provider][$model] ?? ['input' => 0.1, 'output' => 0.3];

        $inputCost = ($inputTokens / 1000) * $costs['input'];
        $outputCost = ($outputTokens / 1000) * $costs['output'];

        return (int) round(($inputCost + $outputCost) * 100);
    }

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
                'input_tokens' => 0,
                'output_tokens' => 0,
                'cost_cents' => 0,
            ]
        );
    }

    /**
     * Log an AI usage request.
     */
    public static function logRequest(
        string $organizationId,
        ?int $userId,
        string $provider,
        string $model,
        string $feature,
        int $inputTokens,
        int $outputTokens
    ): void {
        $costCents = self::calculateCost($provider, $model, $inputTokens, $outputTokens);
        $totalTokens = $inputTokens + $outputTokens;

        $usage = self::getOrCreateToday($organizationId);
        $usage->increment('requests_count');
        $usage->increment('tokens_used', $totalTokens);
        $usage->increment('input_tokens', $inputTokens);
        $usage->increment('output_tokens', $outputTokens);
        $usage->increment('cost_cents', $costCents);

        // Update provider/model info
        $usage->update([
            'provider' => $provider,
            'model' => $model,
            'feature' => $feature,
            'user_id' => $userId,
        ]);
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
     * Get monthly token count for an organization.
     */
    public static function getMonthlyTokens(string $organizationId): int
    {
        return self::where('organization_id', $organizationId)
            ->where('usage_date', '>=', Carbon::now()->startOfMonth())
            ->sum('tokens_used');
    }

    /**
     * Check if organization has reached monthly token limit.
     */
    public static function hasReachedLimit(string $organizationId, int $monthlyTokenLimit): bool
    {
        $used = self::getMonthlyTokens($organizationId);
        return $used >= $monthlyTokenLimit;
    }

    /**
     * Get remaining tokens for the month.
     */
    public static function getRemainingTokens(string $organizationId, ?int $monthlyLimit): ?int
    {
        if ($monthlyLimit === null) {
            return null; // Unlimited
        }

        $used = self::getMonthlyTokens($organizationId);
        return max(0, $monthlyLimit - $used);
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

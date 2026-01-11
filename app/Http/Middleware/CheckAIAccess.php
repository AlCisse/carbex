<?php

namespace App\Http\Middleware;

use App\Models\AIUsage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check AI Access Middleware
 *
 * Enforces AI access based on subscription plan:
 * - Checks if plan has AI access enabled
 * - Checks daily and monthly quotas
 * - Tracks usage in database
 *
 * Usage in routes:
 * Route::post('/ai/chat', ...)->middleware('ai.access');
 */
class CheckAIAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized($request, 'Authentification requise.');
        }

        $organization = $user->organization;

        if (!$organization) {
            return $this->unauthorized($request, 'Organisation non trouvée.');
        }

        // Get organization's plan
        $subscription = $organization->subscription;
        $plan = $subscription?->plan ?? 'free';

        // Get quota config for this plan
        $quotas = config("ai.plan_quotas.{$plan}", config('ai.plan_quotas.free'));

        // Check if AI is enabled for this plan
        if (!($quotas['enabled'] ?? false)) {
            return $this->forbidden($request, 'L\'accès à l\'IA n\'est pas inclus dans votre plan. Passez à Premium pour en bénéficier.');
        }

        // Check daily limit
        $dailyLimit = $quotas['daily_limit'] ?? 0;
        if ($dailyLimit !== -1) {
            $todayCount = AIUsage::getTodayCount($organization->id);
            if ($todayCount >= $dailyLimit) {
                return $this->quotaExceeded($request, 'Quota journalier atteint.', [
                    'limit' => $dailyLimit,
                    'used' => $todayCount,
                    'reset_at' => now()->endOfDay()->toIso8601String(),
                ]);
            }
        }

        // Check monthly limit
        $monthlyLimit = $quotas['monthly_limit'] ?? 0;
        if ($monthlyLimit !== -1) {
            $monthlyCount = AIUsage::getMonthlyCount($organization->id);
            if ($monthlyCount >= $monthlyLimit) {
                return $this->quotaExceeded($request, 'Quota mensuel atteint.', [
                    'limit' => $monthlyLimit,
                    'used' => $monthlyCount,
                    'reset_at' => now()->endOfMonth()->toIso8601String(),
                ]);
            }
        }

        // Store quota info in request for later use
        $request->attributes->set('ai_quota', [
            'plan' => $plan,
            'daily_limit' => $dailyLimit,
            'daily_used' => AIUsage::getTodayCount($organization->id),
            'monthly_limit' => $monthlyLimit,
            'monthly_used' => AIUsage::getMonthlyCount($organization->id),
        ]);

        // Process the request
        $response = $next($request);

        // Increment usage counter on successful response
        if ($response->isSuccessful()) {
            $usage = AIUsage::getOrCreateToday($organization->id);
            $usage->incrementRequests();
        }

        return $response;
    }

    /**
     * Return unauthorized response.
     */
    private function unauthorized(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => 'unauthenticated',
            ], 401);
        }

        return redirect()->route('login')->with('error', $message);
    }

    /**
     * Return forbidden response (no AI access).
     */
    private function forbidden(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => 'ai_access_denied',
                'upgrade_url' => url('/settings/billing'),
            ], 403);
        }

        return redirect()
            ->route('settings.billing')
            ->with('error', $message);
    }

    /**
     * Return quota exceeded response.
     */
    private function quotaExceeded(Request $request, string $message, array $quota): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => 'ai_quota_exceeded',
                'quota' => $quota,
                'upgrade_url' => url('/settings/billing'),
            ], 429);
        }

        return redirect()
            ->back()
            ->with('error', $message);
    }
}

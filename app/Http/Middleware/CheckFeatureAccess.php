<?php

namespace App\Http\Middleware;

use App\Services\Billing\PlanLimitsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check Feature Access Middleware
 *
 * Enforces subscription plan feature access:
 * - CSV/Excel imports
 * - Advanced reports
 * - API access
 * - SSO
 *
 * Usage in routes:
 * Route::post('/import/csv', ...)->middleware('feature:csv_import');
 * Route::get('/api/v1/...', ...)->middleware('feature:api_access');
 */
class CheckFeatureAccess
{
    public function __construct(
        private PlanLimitsService $planLimits
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param string $feature The required feature
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $organization = $user->organization;

        if (!$organization) {
            return $next($request);
        }

        // Check if feature is available
        if (!$this->planLimits->hasFeature($organization, $feature)) {
            $message = __('This feature is not available on your current plan. Please upgrade to access it.');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error' => 'feature_not_available',
                    'required_feature' => $feature,
                    'upgrade_url' => url('/settings/billing'),
                ], 403);
            }

            return redirect()
                ->route('settings.billing')
                ->with('error', $message);
        }

        return $next($request);
    }
}

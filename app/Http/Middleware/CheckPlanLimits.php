<?php

namespace App\Http\Middleware;

use App\Services\Billing\PlanLimitsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check Plan Limits Middleware
 *
 * Enforces subscription plan limits for specific actions:
 * - Creating bank connections
 * - Inviting users
 * - Creating sites
 * - Generating reports
 *
 * Usage in routes:
 * Route::post('/bank-connections', ...)->middleware('plan.limit:bank_connection');
 * Route::post('/users/invite', ...)->middleware('plan.limit:user');
 */
class CheckPlanLimits
{
    public function __construct(
        private PlanLimitsService $planLimits
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param string|null $resource The resource type being created
     */
    public function handle(Request $request, Closure $next, ?string $resource = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $organization = $user->organization;

        if (!$organization) {
            return $next($request);
        }

        // Map resource to action
        $action = $this->getAction($resource);

        if (!$action) {
            return $next($request);
        }

        // Check if action is allowed
        if (!$this->planLimits->canPerformAction($organization, $action)) {
            $message = $this->planLimits->getLimitMessage($resource);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error' => 'plan_limit_exceeded',
                    'upgrade_url' => url('/settings/billing'),
                ], 403);
            }

            return redirect()
                ->route('settings.billing')
                ->with('error', $message);
        }

        return $next($request);
    }

    /**
     * Map resource type to action name.
     */
    private function getAction(?string $resource): ?string
    {
        return match ($resource) {
            'bank_connection' => 'add_bank_connection',
            'user' => 'add_user',
            'site' => 'add_site',
            'report' => 'generate_report',
            default => null,
        };
    }
}

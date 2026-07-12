<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensure Active Subscription Middleware
 *
 * Restricts access to subscription-required routes.
 * Allows access during trial or with active subscription.
 *
 * Usage in routes:
 * Route::middleware('subscribed')->group(function () { ... });
 */
class EnsureActiveSubscription
{
    /**
     * Routes that are always accessible (even without subscription).
     */
    private array $allowedRoutes = [
        'settings.billing',
        'settings.billing.*',
        'subscription.*',
        'logout',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Check if current route is in allowed routes
        $currentRoute = $request->route()?->getName();
        if ($this->isAllowedRoute($currentRoute)) {
            return $next($request);
        }

        $organization = $user->organization;

        if (!$organization) {
            return $next($request);
        }

        // Check for active subscription or trial
        if (!$organization->hasActiveSubscription() && !$organization->onTrial()) {
            // Check grace period
            $subscription = $organization->subscription;

            $inGracePeriod = $subscription
                && $subscription->current_period_end
                && $subscription->current_period_end
                    ->addDays(config('cashier.grace_period_days', 3))
                    ->isFuture();

            if (!$inGracePeriod) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Your subscription has expired. Please renew to continue using the platform.'),
                        'error' => 'subscription_required',
                        'checkout_url' => url('/settings/billing'),
                    ], 402); // Payment Required
                }

                return redirect()
                    ->route('settings.billing')
                    ->with('warning', __('Your subscription has expired. Please renew to continue using the platform.'));
            }
        }

        return $next($request);
    }

    /**
     * Check if route is in allowed list.
     */
    private function isAllowedRoute(?string $routeName): bool
    {
        if (!$routeName) {
            return false;
        }

        foreach ($this->allowedRoutes as $pattern) {
            if (str_ends_with($pattern, '*')) {
                $prefix = rtrim($pattern, '*');
                if (str_starts_with($routeName, $prefix)) {
                    return true;
                }
            } elseif ($routeName === $pattern) {
                return true;
            }
        }

        return false;
    }
}

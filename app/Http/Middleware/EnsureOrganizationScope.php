<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationScope
{
    /**
     * Handle an incoming request.
     *
     * Ensures that:
     * 1. User is authenticated
     * 2. User belongs to an organization
     * 3. User's organization is active
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check user is authenticated
        if (! $user) {
            return response()->json([
                'message' => __('auth.unauthenticated'),
            ], 401);
        }

        // Check user belongs to an organization
        if (! $user->organization_id) {
            return response()->json([
                'message' => __('auth.no_organization'),
                'error' => 'USER_NO_ORGANIZATION',
            ], 403);
        }

        // Load organization if not loaded
        if (! $user->relationLoaded('organization')) {
            $user->load('organization');
        }

        // Check organization exists and is active
        if (! $user->organization) {
            return response()->json([
                'message' => __('auth.organization_not_found'),
                'error' => 'ORGANIZATION_NOT_FOUND',
            ], 404);
        }

        if (! $user->organization->is_active) {
            return response()->json([
                'message' => __('auth.organization_inactive'),
                'error' => 'ORGANIZATION_INACTIVE',
            ], 403);
        }

        // Check user is active
        if (! $user->is_active) {
            return response()->json([
                'message' => __('auth.user_inactive'),
                'error' => 'USER_INACTIVE',
            ], 403);
        }

        // Store organization in request for easy access
        $request->merge([
            'organization' => $user->organization,
            'organization_id' => $user->organization_id,
        ]);

        return $next($request);
    }
}

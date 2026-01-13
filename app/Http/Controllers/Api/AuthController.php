<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user and organization.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Generate unique slug
        $baseSlug = Str::slug($validated['organization_name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Organization::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        // Create organization
        $organization = Organization::create([
            'name' => $validated['organization_name'],
            'slug' => $slug,
            'country' => $validated['country'],
            'sector' => $validated['sector'] ?? null,
            'size' => $validated['organization_size'] ?? null,
            'fiscal_year_start_month' => 1,
            'default_currency' => $validated['country'] === 'FR' ? 'EUR' : 'EUR',
            'timezone' => $validated['country'] === 'FR' ? 'Europe/Paris' : 'Europe/Berlin',
            'settings' => [
                'onboarding_completed' => false,
                'setup_step' => 1,
            ],
        ]);

        // Create user as organization owner
        $user = User::create([
            'organization_id' => $organization->id,
            'name' => $validated['name'],
            'first_name' => $validated['first_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'owner',
            'locale' => $validated['country'] === 'FR' ? 'fr' : 'de',
            'timezone' => $organization->timezone,
            'is_active' => true,
        ]);

        event(new Registered($user));

        // Create API token with full abilities
        $abilities = config('sanctum.role_abilities.owner', ['*']);
        $token = $user->createToken(
            'auth_token',
            $abilities,
            now()->addDays(7)
        );

        return response()->json([
            'message' => __('auth.registered'),
            'user' => $this->formatUser($user),
            'organization' => $this->formatOrganization($organization),
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->accessToken->expires_at?->toIso8601String(),
        ], 201);
    }

    /**
     * Login user and create token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'string|max:255',
            'remember' => 'boolean',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => [__('auth.deactivated')],
            ]);
        }

        // Record login
        $user->recordLogin($request->ip());

        // Revoke old tokens if not remember
        if (! $request->boolean('remember')) {
            $user->tokens()->delete();
        }

        // Get abilities based on role
        $abilities = config("sanctum.role_abilities.{$user->role}", ['*']);

        // Create new token
        $expiration = $request->boolean('remember') ? now()->addDays(30) : now()->addDays(7);
        $token = $user->createToken(
            $request->device_name ?? 'auth_token',
            $abilities,
            $expiration
        );

        return response()->json([
            'message' => __('auth.login_success'),
            'user' => $this->formatUser($user),
            'organization' => $this->formatOrganization($user->organization),
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->accessToken->expires_at?->toIso8601String(),
        ]);
    }

    /**
     * Logout user and revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => __('auth.logout_success'),
        ]);
    }

    /**
     * Get current authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('organization');

        return response()->json([
            'user' => $this->formatUser($user),
            'organization' => $this->formatOrganization($user->organization),
            'abilities' => $request->user()->currentAccessToken()->abilities ?? [],
        ]);
    }

    /**
     * Update current user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'locale' => 'sometimes|string|in:fr,de,en',
            'timezone' => 'sometimes|string|timezone',
            'notification_preferences' => 'sometimes|array',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => __('auth.profile_updated'),
            'user' => $this->formatUser($user->fresh()),
        ]);
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => [__('auth.password_incorrect')],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke all tokens except current
        $currentTokenId = $request->user()->currentAccessToken()->id;
        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        return response()->json([
            'message' => __('auth.password_changed'),
        ]);
    }

    /**
     * Verify email address.
     */
    public function verifyEmail(Request $request, string $id, string $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json([
                'message' => __('auth.invalid_verification_link'),
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => __('auth.already_verified'),
            ]);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => __('auth.email_verified'),
        ]);
    }

    /**
     * Resend email verification notification.
     */
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => __('auth.already_verified'),
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => __('auth.verification_sent'),
        ]);
    }

    /**
     * Format user for response.
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'role' => $user->role,
            'phone' => $user->phone,
            'job_title' => $user->job_title,
            'department' => $user->department,
            'avatar' => $user->avatar,
            'locale' => $user->locale,
            'timezone' => $user->timezone,
            'notification_preferences' => $user->notification_preferences,
            'is_active' => $user->is_active,
            'two_factor_enabled' => $user->two_factor_enabled,
            'last_login_at' => $user->last_login_at?->toIso8601String(),
            'created_at' => $user->created_at->toIso8601String(),
        ];
    }

    /**
     * Format organization for response.
     */
    private function formatOrganization(?Organization $organization): ?array
    {
        if (! $organization) {
            return null;
        }

        return [
            'id' => $organization->id,
            'name' => $organization->name,
            'country' => $organization->country,
            'sector' => $organization->sector,
            'size' => $organization->size,
            'settings' => $organization->settings,
            'onboarding_completed' => $organization->settings['onboarding_completed'] ?? false,
        ];
    }
}

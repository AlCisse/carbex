<?php

namespace App\Services\Organization;

use App\Models\Organization;
use App\Models\User;
use App\Notifications\UserInvitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserInvitationService
{
    /**
     * Create and send an invitation to a new user.
     */
    public function invite(
        Organization $organization,
        string $email,
        string $role = 'member',
        ?string $name = null,
        ?User $invitedBy = null
    ): User {
        // Generate invitation token
        $token = Str::random(64);
        $temporaryPassword = Str::random(16);

        // Create user with temporary password
        $user = User::create([
            'organization_id' => $organization->id,
            'name' => $name ?? Str::before($email, '@'),
            'email' => $email,
            'password' => Hash::make($temporaryPassword),
            'role' => $this->validateRole($role),
            'locale' => $organization->country === 'FR' ? 'fr' : 'de',
            'timezone' => $organization->timezone,
            'is_active' => true,
            'notification_preferences' => [
                'invitation_token' => $token,
                'invitation_expires_at' => now()->addDays(7)->toIso8601String(),
                'invited_by' => $invitedBy?->id,
            ],
        ]);

        // Send invitation email
        $user->notify(new UserInvitation($organization, $invitedBy, $token));

        // Update subscription usage
        $subscription = $organization->subscription;
        if ($subscription) {
            $subscription->increment('users_used');
        }

        return $user;
    }

    /**
     * Resend invitation email.
     */
    public function resendInvitation(User $user, ?User $resendBy = null): void
    {
        // Generate new token
        $token = Str::random(64);

        // Update invitation token
        $preferences = $user->notification_preferences ?? [];
        $preferences['invitation_token'] = $token;
        $preferences['invitation_expires_at'] = now()->addDays(7)->toIso8601String();
        $preferences['resent_by'] = $resendBy?->id;
        $preferences['resent_at'] = now()->toIso8601String();

        $user->update(['notification_preferences' => $preferences]);

        // Send new invitation email
        $user->notify(new UserInvitation($user->organization, $resendBy, $token));
    }

    /**
     * Accept an invitation.
     */
    public function acceptInvitation(string $token, string $password, ?string $name = null): ?User
    {
        // Find user with this token
        $user = User::whereJsonContains('notification_preferences->invitation_token', $token)->first();

        if (! $user) {
            return null;
        }

        // Check if token is expired
        $preferences = $user->notification_preferences ?? [];
        $expiresAt = $preferences['invitation_expires_at'] ?? null;

        if ($expiresAt && now()->isAfter($expiresAt)) {
            return null;
        }

        // Update user
        $updates = [
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ];

        if ($name) {
            $updates['name'] = $name;
        }

        // Clear invitation data
        unset($preferences['invitation_token']);
        unset($preferences['invitation_expires_at']);
        $updates['notification_preferences'] = $preferences;

        $user->update($updates);

        return $user;
    }

    /**
     * Cancel a pending invitation.
     */
    public function cancelInvitation(User $user): void
    {
        // Only cancel if not yet verified
        if ($user->hasVerifiedEmail()) {
            return;
        }

        // Update subscription usage
        $subscription = $user->organization?->subscription;
        if ($subscription && $subscription->users_used > 0) {
            $subscription->decrement('users_used');
        }

        // Delete the user
        $user->forceDelete();
    }

    /**
     * Check if invitation token is valid.
     */
    public function validateToken(string $token): ?User
    {
        $user = User::whereJsonContains('notification_preferences->invitation_token', $token)->first();

        if (! $user) {
            return null;
        }

        $preferences = $user->notification_preferences ?? [];
        $expiresAt = $preferences['invitation_expires_at'] ?? null;

        if ($expiresAt && now()->isAfter($expiresAt)) {
            return null;
        }

        return $user;
    }

    /**
     * Validate and return a valid role.
     */
    private function validateRole(string $role): string
    {
        $validRoles = ['admin', 'manager', 'member', 'viewer'];

        return in_array($role, $validRoles) ? $role : 'member';
    }
}

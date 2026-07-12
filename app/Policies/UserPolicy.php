<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->canManage();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view themselves
        if ($user->id === $model->id) {
            return true;
        }

        // Can view users in same organization
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        return $user->canManage();
    }

    /**
     * Determine whether the user can create users.
     * Only admins can invite/create users.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the user.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Must be same organization
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Only admins can update other users
        if (! $user->isAdmin()) {
            return false;
        }

        // Cannot update owner unless you are the owner
        if ($model->isOwner() && ! $user->isOwner()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the user.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }

        // Must be same organization
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Only admins can delete users
        if (! $user->isAdmin()) {
            return false;
        }

        // Cannot delete owner
        if ($model->isOwner()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can change roles.
     */
    public function changeRole(User $user, User $model): bool
    {
        // Cannot change own role
        if ($user->id === $model->id) {
            return false;
        }

        // Must be same organization
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Only owner can change admin roles
        if ($model->isAdmin() || $model->isOwner()) {
            return $user->isOwner();
        }

        // Admins can change non-admin roles
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can transfer ownership.
     */
    public function transferOwnership(User $user, User $model): bool
    {
        // Only owner can transfer
        if (! $user->isOwner()) {
            return false;
        }

        // Must be same organization
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Cannot transfer to self
        return $user->id !== $model->id;
    }

    /**
     * Determine whether the user can resend invitation.
     */
    public function resendInvitation(User $user, User $model): bool
    {
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Only for unverified users
        if ($model->hasVerifiedEmail()) {
            return false;
        }

        return $user->isAdmin();
    }
}

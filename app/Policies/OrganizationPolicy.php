<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any organizations.
     * Only admins and super_admins can list all organizations.
     */
    public function viewAny(User $user): bool
    {
        // Regular users only see their own organization
        return true;
    }

    /**
     * Determine whether the user can view the organization.
     * Users can only view their own organization.
     */
    public function view(User $user, Organization $organization): bool
    {
        return $user->organization_id === $organization->id;
    }

    /**
     * Determine whether the user can create organizations.
     * Only during registration (no authenticated user).
     */
    public function create(User $user): bool
    {
        // Organizations are created during registration
        return false;
    }

    /**
     * Determine whether the user can update the organization.
     * Only owners and admins can update organization settings.
     */
    public function update(User $user, Organization $organization): bool
    {
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the organization.
     * Only the owner can delete the organization.
     */
    public function delete(User $user, Organization $organization): bool
    {
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        return $user->isOwner();
    }

    /**
     * Determine whether the user can manage organization settings.
     */
    public function manageSettings(User $user, Organization $organization): bool
    {
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage billing.
     * Only owners can manage billing and subscription.
     */
    public function manageBilling(User $user, Organization $organization): bool
    {
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        return $user->isOwner();
    }

    /**
     * Determine whether the user can invite team members.
     */
    public function inviteMembers(User $user, Organization $organization): bool
    {
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage team members.
     */
    public function manageMembers(User $user, Organization $organization): bool
    {
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view organization statistics.
     */
    public function viewStats(User $user, Organization $organization): bool
    {
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        return $user->canManage();
    }

    /**
     * Determine whether the user can export organization data.
     */
    public function exportData(User $user, Organization $organization): bool
    {
        if ($user->organization_id !== $organization->id) {
            return false;
        }

        return $user->isAdmin();
    }
}

<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SitePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any sites.
     * All authenticated users can view sites in their organization.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the site.
     */
    public function view(User $user, Site $site): bool
    {
        return $user->organization_id === $site->organization_id;
    }

    /**
     * Determine whether the user can create sites.
     * Admins and managers can create sites.
     */
    public function create(User $user): bool
    {
        return $user->canManage();
    }

    /**
     * Determine whether the user can update the site.
     * Admins and managers can update sites.
     */
    public function update(User $user, Site $site): bool
    {
        if ($user->organization_id !== $site->organization_id) {
            return false;
        }

        return $user->canManage();
    }

    /**
     * Determine whether the user can delete the site.
     * Only admins can delete sites.
     */
    public function delete(User $user, Site $site): bool
    {
        if ($user->organization_id !== $site->organization_id) {
            return false;
        }

        // Cannot delete primary site
        if ($site->is_primary) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can set the site as primary.
     */
    public function setPrimary(User $user, Site $site): bool
    {
        if ($user->organization_id !== $site->organization_id) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view site emissions.
     */
    public function viewEmissions(User $user, Site $site): bool
    {
        return $user->organization_id === $site->organization_id;
    }

    /**
     * Determine whether the user can add activities to the site.
     */
    public function addActivities(User $user, Site $site): bool
    {
        if ($user->organization_id !== $site->organization_id) {
            return false;
        }

        return $user->canManage();
    }
}

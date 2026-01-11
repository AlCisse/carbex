<?php

namespace App\Policies;

use App\Models\BankConnection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankConnectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any bank connections.
     */
    public function viewAny(User $user): bool
    {
        return $user->canManage();
    }

    /**
     * Determine whether the user can view the bank connection.
     */
    public function view(User $user, BankConnection $bankConnection): bool
    {
        // User must belong to the same organization
        if ($user->organization_id !== $bankConnection->organization_id) {
            return false;
        }

        return $user->canManage();
    }

    /**
     * Determine whether the user can create bank connections.
     */
    public function create(User $user): bool
    {
        // Must be at least manager or admin to connect bank accounts
        return $user->canManage();
    }

    /**
     * Determine whether the user can update the bank connection.
     */
    public function update(User $user, BankConnection $bankConnection): bool
    {
        if ($user->organization_id !== $bankConnection->organization_id) {
            return false;
        }

        return $user->canManage();
    }

    /**
     * Determine whether the user can delete the bank connection.
     */
    public function delete(User $user, BankConnection $bankConnection): bool
    {
        if ($user->organization_id !== $bankConnection->organization_id) {
            return false;
        }

        // Only admins can delete bank connections
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the bank connection.
     */
    public function restore(User $user, BankConnection $bankConnection): bool
    {
        if ($user->organization_id !== $bankConnection->organization_id) {
            return false;
        }

        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the bank connection.
     */
    public function forceDelete(User $user, BankConnection $bankConnection): bool
    {
        if ($user->organization_id !== $bankConnection->organization_id) {
            return false;
        }

        return $user->isOwner();
    }

    /**
     * Determine whether the user can trigger a sync.
     */
    public function sync(User $user, BankConnection $bankConnection): bool
    {
        if ($user->organization_id !== $bankConnection->organization_id) {
            return false;
        }

        return $user->canManage();
    }

    /**
     * Determine whether the user can view sync history.
     */
    public function viewSyncHistory(User $user, BankConnection $bankConnection): bool
    {
        if ($user->organization_id !== $bankConnection->organization_id) {
            return false;
        }

        return $user->canManage();
    }

    /**
     * Determine whether the user can reconnect an expired connection.
     */
    public function reconnect(User $user, BankConnection $bankConnection): bool
    {
        if ($user->organization_id !== $bankConnection->organization_id) {
            return false;
        }

        return $user->canManage();
    }

    /**
     * Determine whether the user can view linked accounts.
     */
    public function viewAccounts(User $user, BankConnection $bankConnection): bool
    {
        if ($user->organization_id !== $bankConnection->organization_id) {
            return false;
        }

        return $user->canManage();
    }

    /**
     * Determine whether the user can manage account settings.
     */
    public function manageAccounts(User $user, BankConnection $bankConnection): bool
    {
        if ($user->organization_id !== $bankConnection->organization_id) {
            return false;
        }

        return $user->isAdmin();
    }
}

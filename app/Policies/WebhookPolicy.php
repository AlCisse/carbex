<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Webhook;

class WebhookPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Webhook $webhook): bool
    {
        return $user->organization_id === $webhook->organization_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Webhook $webhook): bool
    {
        return $user->organization_id === $webhook->organization_id
            && in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Webhook $webhook): bool
    {
        return $user->organization_id === $webhook->organization_id
            && in_array($user->role, ['admin', 'super_admin']);
    }
}

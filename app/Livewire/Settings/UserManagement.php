<?php

namespace App\Livewire\Settings;

use App\Models\User;
use App\Services\Organization\UserInvitationService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Gestion de l\'equipe - Carbex')]
class UserManagement extends Component
{
    public $users = [];

    // Invite form
    public bool $showInviteForm = false;
    public string $inviteEmail = '';
    public string $inviteName = '';
    public string $inviteRole = 'member';

    // Edit form
    public bool $showEditForm = false;
    public ?string $editingUserId = null;
    public string $editName = '';
    public string $editRole = 'member';

    // Delete confirmation
    public bool $showDeleteModal = false;
    public ?string $deletingUserId = null;

    public function mount(): void
    {
        $this->loadUsers();
    }

    public function loadUsers(): void
    {
        $this->users = User::with(['organization'])
            ->orderByRaw("array_position(ARRAY['owner', 'admin', 'manager', 'member', 'viewer'], role)")
            ->orderBy('name')
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at?->toIso8601String(),
                'created_at' => $user->created_at->toIso8601String(),
                'is_current' => $user->id === auth()->id(),
            ])
            ->toArray();
    }

    public function openInviteForm(): void
    {
        Gate::authorize('create', User::class);

        $this->resetInviteForm();
        $this->showInviteForm = true;
    }

    public function closeInviteForm(): void
    {
        $this->showInviteForm = false;
        $this->resetInviteForm();
    }

    public function resetInviteForm(): void
    {
        $this->inviteEmail = '';
        $this->inviteName = '';
        $this->inviteRole = 'member';
    }

    public function invite(UserInvitationService $invitationService): void
    {
        Gate::authorize('create', User::class);

        $this->validate([
            'inviteEmail' => 'required|email|unique:users,email',
            'inviteName' => 'nullable|string|max:255',
            'inviteRole' => 'required|string|in:admin,manager,member,viewer',
        ], [
            'inviteEmail.unique' => __('auth.email_taken'),
        ]);

        // Check subscription limits
        $subscription = auth()->user()->organization->subscription;
        if ($subscription && ! $subscription->canAddUser()) {
            session()->flash('error', __('carbex.subscription.users_limit_reached'));
            $this->closeInviteForm();

            return;
        }

        $invitationService->invite(
            auth()->user()->organization,
            $this->inviteEmail,
            $this->inviteRole,
            $this->inviteName ?: null,
            auth()->user()
        );

        $this->closeInviteForm();
        $this->loadUsers();
        session()->flash('success', __('carbex.users.invitation_sent'));
    }

    public function resendInvitation(string $userId, UserInvitationService $invitationService): void
    {
        $user = User::findOrFail($userId);
        Gate::authorize('resendInvitation', $user);

        $invitationService->resendInvitation($user, auth()->user());

        session()->flash('success', __('carbex.users.invitation_resent'));
    }

    public function openEditForm(string $userId): void
    {
        $user = User::findOrFail($userId);
        Gate::authorize('update', $user);

        $this->editingUserId = $userId;
        $this->editName = $user->name;
        $this->editRole = $user->role;
        $this->showEditForm = true;
    }

    public function closeEditForm(): void
    {
        $this->showEditForm = false;
        $this->editingUserId = null;
        $this->editName = '';
        $this->editRole = 'member';
    }

    public function saveUser(): void
    {
        $user = User::findOrFail($this->editingUserId);
        Gate::authorize('update', $user);

        $this->validate([
            'editName' => 'required|string|max:255',
            'editRole' => 'required|string|in:admin,manager,member,viewer',
        ]);

        // Check if role change is allowed
        if ($user->role !== $this->editRole) {
            Gate::authorize('changeRole', $user);
        }

        $user->update([
            'name' => $this->editName,
            'role' => $this->editRole,
        ]);

        $this->closeEditForm();
        $this->loadUsers();
        session()->flash('success', __('carbex.users.updated'));
    }

    public function confirmDelete(string $userId): void
    {
        $user = User::findOrFail($userId);
        Gate::authorize('delete', $user);

        $this->deletingUserId = $userId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingUserId = null;
    }

    public function deleteUser(): void
    {
        if (! $this->deletingUserId) {
            return;
        }

        $user = User::findOrFail($this->deletingUserId);
        Gate::authorize('delete', $user);

        // Update subscription usage
        $subscription = auth()->user()->organization->subscription;
        if ($subscription && $subscription->users_used > 0) {
            $subscription->decrement('users_used');
        }

        $user->delete();

        $this->cancelDelete();
        $this->loadUsers();
        session()->flash('success', __('carbex.users.deleted'));
    }

    public function toggleActive(string $userId): void
    {
        $user = User::findOrFail($userId);
        Gate::authorize('update', $user);

        $user->update(['is_active' => ! $user->is_active]);

        $this->loadUsers();
        session()->flash('success', $user->is_active
            ? __('carbex.users.activated')
            : __('carbex.users.deactivated'));
    }

    public function getRoleLabel(string $role): string
    {
        return match ($role) {
            'owner' => __('carbex.roles.owner'),
            'admin' => __('carbex.roles.admin'),
            'manager' => __('carbex.roles.manager'),
            'member' => __('carbex.roles.member'),
            'viewer' => __('carbex.roles.viewer'),
            default => ucfirst($role),
        };
    }

    public function getRoleBadgeVariant(string $role): string
    {
        return match ($role) {
            'owner' => 'primary',
            'admin' => 'info',
            'manager' => 'success',
            'member' => 'default',
            'viewer' => 'warning',
            default => 'default',
        };
    }

    public function render()
    {
        return view('livewire.settings.user-management');
    }
}

<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('carbex.settings.team') }}</h1>
            @can('create', App\Models\User::class)
            <x-button wire:click="openInviteForm" type="button">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                </svg>
                {{ __('carbex.users.invite') }}
            </x-button>
            @endcan
        </div>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" dismissible class="mb-6">{{ session('success') }}</x-alert>
    @endif

    @if (session('error'))
        <x-alert type="error" dismissible class="mb-6">{{ session('error') }}</x-alert>
    @endif

    <!-- Users Table -->
    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">{{ __('carbex.users.member') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('carbex.users.role') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('carbex.users.status') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ __('carbex.users.last_login') }}</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach ($users as $user)
                    <tr class="{{ $user['is_current'] ? 'bg-green-50' : '' }}">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                            <div class="flex items-center">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full {{ $user['is_current'] ? 'bg-green-200 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                    {{ strtoupper(substr($user['name'], 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-medium text-gray-900">{{ $user['name'] }}</span>
                                        @if ($user['is_current'])
                                            <x-badge variant="primary" size="sm">{{ __('carbex.users.you') }}</x-badge>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $user['email'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            <x-badge :variant="$this->getRoleBadgeVariant($user['role'])">
                                {{ $this->getRoleLabel($user['role']) }}
                            </x-badge>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            @if (!$user['email_verified_at'])
                                <x-badge variant="warning">{{ __('carbex.users.pending') }}</x-badge>
                            @elseif (!$user['is_active'])
                                <x-badge variant="danger">{{ __('carbex.common.inactive') }}</x-badge>
                            @else
                                <x-badge variant="success">{{ __('carbex.common.active') }}</x-badge>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            @if ($user['last_login_at'])
                                {{ \Carbon\Carbon::parse($user['last_login_at'])->diffForHumans() }}
                            @else
                                <span class="text-gray-400">{{ __('carbex.users.never') }}</span>
                            @endif
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <div class="flex items-center justify-end space-x-2">
                                @if (!$user['email_verified_at'] && !$user['is_current'])
                                    @can('resendInvitation', App\Models\User::find($user['id']))
                                    <button wire:click="resendInvitation('{{ $user['id'] }}')" type="button" class="text-green-600 hover:text-green-900" title="{{ __('carbex.users.resend_invitation') }}">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                    </button>
                                    @endcan
                                @endif

                                @if (!$user['is_current'])
                                    @can('update', App\Models\User::find($user['id']))
                                    <button wire:click="openEditForm('{{ $user['id'] }}')" type="button" class="text-gray-400 hover:text-gray-600" title="{{ __('carbex.common.edit') }}">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>

                                    <button wire:click="toggleActive('{{ $user['id'] }}')" type="button" class="{{ $user['is_active'] ? 'text-yellow-500 hover:text-yellow-700' : 'text-green-500 hover:text-green-700' }}" title="{{ $user['is_active'] ? __('carbex.users.deactivate') : __('carbex.users.activate') }}">
                                        @if ($user['is_active'])
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        @else
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        @endif
                                    </button>
                                    @endcan

                                    @can('delete', App\Models\User::find($user['id']))
                                    @if ($user['role'] !== 'owner')
                                    <button wire:click="confirmDelete('{{ $user['id'] }}')" type="button" class="text-gray-400 hover:text-red-600" title="{{ __('carbex.common.delete') }}">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                    @endif
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Invite Modal -->
    @if ($showInviteForm)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
            <div wire:click="closeInviteForm" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                <form wire:submit="invite">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">{{ __('carbex.users.invite_member') }}</h3>

                        <div class="space-y-4">
                            <x-input wire:model="inviteEmail" name="inviteEmail" type="email" :label="__('carbex.auth.email')" required :error="$errors->first('inviteEmail')" />

                            <x-input wire:model="inviteName" name="inviteName" :label="__('carbex.auth.name')" :hint="__('carbex.users.name_optional')" />

                            <x-select wire:model="inviteRole" name="inviteRole" :label="__('carbex.users.role')" required>
                                <option value="admin">{{ __('carbex.roles.admin') }} - {{ __('carbex.roles.admin_desc') }}</option>
                                <option value="manager">{{ __('carbex.roles.manager') }} - {{ __('carbex.roles.manager_desc') }}</option>
                                <option value="member">{{ __('carbex.roles.member') }} - {{ __('carbex.roles.member_desc') }}</option>
                                <option value="viewer">{{ __('carbex.roles.viewer') }} - {{ __('carbex.roles.viewer_desc') }}</option>
                            </x-select>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <x-button type="submit" class="sm:ml-3">
                            {{ __('carbex.users.send_invitation') }}
                        </x-button>
                        <x-button type="button" variant="secondary" wire:click="closeInviteForm">
                            {{ __('carbex.common.cancel') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Modal -->
    @if ($showEditForm)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
            <div wire:click="closeEditForm" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                <form wire:submit="saveUser">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">{{ __('carbex.users.edit_member') }}</h3>

                        <div class="space-y-4">
                            <x-input wire:model="editName" name="editName" :label="__('carbex.auth.name')" required :error="$errors->first('editName')" />

                            <x-select wire:model="editRole" name="editRole" :label="__('carbex.users.role')" required>
                                <option value="admin">{{ __('carbex.roles.admin') }}</option>
                                <option value="manager">{{ __('carbex.roles.manager') }}</option>
                                <option value="member">{{ __('carbex.roles.member') }}</option>
                                <option value="viewer">{{ __('carbex.roles.viewer') }}</option>
                            </x-select>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <x-button type="submit" class="sm:ml-3">
                            {{ __('carbex.common.save') }}
                        </x-button>
                        <x-button type="button" variant="secondary" wire:click="closeEditForm">
                            {{ __('carbex.common.cancel') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
            <div wire:click="cancelDelete" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">{{ __('carbex.users.delete_title') }}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">{{ __('carbex.users.delete_confirm') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <x-button type="button" variant="danger" wire:click="deleteUser" class="sm:ml-3">
                        {{ __('carbex.common.delete') }}
                    </x-button>
                    <x-button type="button" variant="secondary" wire:click="cancelDelete">
                        {{ __('carbex.common.cancel') }}
                    </x-button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

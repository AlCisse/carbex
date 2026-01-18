<div>
    @if (!$isValid)
        <x-slot name="heading">
            {{ __('linscarbon.auth.invitation_invalid_title') }}
        </x-slot>

        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        {{ __('linscarbon.auth.invitation_invalid') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:text-green-500">
                {{ __('linscarbon.auth.back_to_login') }}
            </a>
        </div>
    @else
        <x-slot name="heading">
            {{ __('linscarbon.auth.accept_invitation_title') }}
        </x-slot>

        <x-slot name="subheading">
            {{ __('linscarbon.auth.accept_invitation_subtitle', ['email' => $invitation['email'] ?? '']) }}
        </x-slot>

        @if ($userExists)
            <div class="rounded-md bg-blue-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            {{ __('linscarbon.auth.account_exists') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit="acceptInvitation" class="space-y-6">
            @if (!$userExists)
                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('linscarbon.auth.first_name') }}
                    </label>
                    <div class="mt-2">
                        <input wire:model="first_name" id="first_name" name="first_name" type="text" autocomplete="given-name" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('first_name') ring-red-300 @enderror">
                    </div>
                    @error('first_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('linscarbon.auth.last_name') }}
                    </label>
                    <div class="mt-2">
                        <input wire:model="last_name" id="last_name" name="last_name" type="text" autocomplete="family-name" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('last_name') ring-red-300 @enderror">
                    </div>
                    @error('last_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('linscarbon.auth.password') }}
                    </label>
                    <div class="mt-2">
                        <input wire:model="password" id="password" name="password" type="password" autocomplete="new-password" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('password') ring-red-300 @enderror">
                    </div>
                    @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('linscarbon.auth.confirm_password') }}
                    </label>
                    <div class="mt-2">
                        <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
            @endif

            <!-- Submit Button -->
            <div>
                <button type="submit"
                    class="flex w-full justify-center rounded-md bg-green-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 disabled:opacity-50"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('linscarbon.auth.accept_invitation_button') }}</span>
                    <span wire:loading>{{ __('linscarbon.common.loading') }}</span>
                </button>
            </div>
        </form>
    @endif
</div>

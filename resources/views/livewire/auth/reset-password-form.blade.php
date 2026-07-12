<div>
    <x-slot name="heading">
        {{ __('linscarbon.auth.reset_password_title') }}
    </x-slot>

    <x-slot name="subheading">
        {{ __('linscarbon.auth.reset_password_subtitle') }}
    </x-slot>

    <form wire:submit="resetPassword" class="space-y-6">
        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                {{ __('linscarbon.auth.email') }}
            </label>
            <div class="mt-2">
                <input wire:model="email" id="email" name="email" type="email" autocomplete="email" required
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('email') ring-red-300 @enderror">
            </div>
            @error('email')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                {{ __('linscarbon.auth.new_password') }}
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

        <!-- Submit Button -->
        <div>
            <button type="submit"
                class="flex w-full justify-center rounded-md bg-green-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 disabled:opacity-50"
                wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('linscarbon.auth.reset_password_button') }}</span>
                <span wire:loading>{{ __('linscarbon.common.loading') }}</span>
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm font-semibold text-green-600 hover:text-green-500">
            {{ __('linscarbon.auth.back_to_login') }}
        </a>
    </div>
</div>

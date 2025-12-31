<div>
    <x-slot name="heading">
        {{ __('carbex.auth.login_title') }}
    </x-slot>

    <x-slot name="subheading">
        {{ __('carbex.auth.login_subtitle') }}
    </x-slot>

    <form wire:submit="login" class="space-y-6">
        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                {{ __('carbex.auth.email') }}
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
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('carbex.auth.password') }}
                </label>
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-semibold text-green-600 hover:text-green-500">
                        {{ __('carbex.auth.forgot_password') }}
                    </a>
                </div>
            </div>
            <div class="mt-2">
                <input wire:model="password" id="password" name="password" type="password" autocomplete="current-password" required
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('password') ring-red-300 @enderror">
            </div>
            @error('password')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input wire:model="remember" id="remember" name="remember" type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-600">
            <label for="remember" class="ml-3 block text-sm leading-6 text-gray-900">
                {{ __('carbex.auth.remember_me') }}
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                class="flex w-full justify-center rounded-md bg-green-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 disabled:opacity-50"
                wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('carbex.auth.login_button') }}</span>
                <span wire:loading>{{ __('carbex.common.loading') }}</span>
            </button>
        </div>
    </form>
</div>

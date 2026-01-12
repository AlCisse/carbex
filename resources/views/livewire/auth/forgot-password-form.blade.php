<div>
    <x-slot name="heading">
        {{ __('carbex.auth.forgot_password_title') }}
    </x-slot>

    <x-slot name="subheading">
        {{ __('carbex.auth.forgot_password_subtitle') }}
    </x-slot>

    @if ($emailSent)
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ __('carbex.auth.reset_link_sent') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:text-green-500">
                {{ __('carbex.auth.back_to_login') }}
            </a>
        </div>
    @else
        <form wire:submit="sendResetLink" class="space-y-6">
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('carbex.auth.email') }}
                </label>
                <div class="mt-2">
                    <input wire:model="email" id="email" name="email" type="email" autocomplete="email" required
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('email') ring-red-300 @enderror"
                        placeholder="{{ __('carbex.auth.email_placeholder') }}">
                </div>
                @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                    class="flex w-full justify-center rounded-md bg-green-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 disabled:opacity-50"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('carbex.auth.send_reset_link') }}</span>
                    <span wire:loading>{{ __('carbex.common.loading') }}</span>
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-green-600 hover:text-green-500">
                {{ __('carbex.auth.back_to_login') }}
            </a>
        </div>
    @endif
</div>

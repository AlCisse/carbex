<div>
    <x-slot name="heading">
        {{ __('carbex.auth.register_title') }}
    </x-slot>

    <x-slot name="subheading">
        {{ __('carbex.auth.register_subtitle') }}
    </x-slot>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span class="flex h-8 w-8 items-center justify-center rounded-full {{ $step >= 1 ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600' }}">1</span>
                <span class="ml-2 text-sm font-medium {{ $step >= 1 ? 'text-green-600' : 'text-gray-500' }}">{{ __('carbex.auth.step_account') }}</span>
            </div>
            <div class="flex-1 mx-4 h-0.5 {{ $step >= 2 ? 'bg-green-600' : 'bg-gray-200' }}"></div>
            <div class="flex items-center">
                <span class="flex h-8 w-8 items-center justify-center rounded-full {{ $step >= 2 ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600' }}">2</span>
                <span class="ml-2 text-sm font-medium {{ $step >= 2 ? 'text-green-600' : 'text-gray-500' }}">{{ __('carbex.auth.step_organization') }}</span>
            </div>
        </div>
    </div>

    <form wire:submit="{{ $step === 2 ? 'register' : 'nextStep' }}" class="space-y-6">
        @if($step === 1)
        <!-- Step 1: Account Information -->
        <div class="space-y-4">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('carbex.auth.name') }} *
                </label>
                <div class="mt-2">
                    <input wire:model="name" id="name" name="name" type="text" required
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('name') ring-red-300 @enderror">
                </div>
                @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('carbex.auth.email') }} *
                </label>
                <div class="mt-2">
                    <input wire:model="email" id="email" name="email" type="email" required
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('email') ring-red-300 @enderror">
                </div>
                @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('carbex.auth.password') }} *
                </label>
                <div class="mt-2">
                    <input wire:model="password" id="password" name="password" type="password" required
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('password') ring-red-300 @enderror">
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ __('carbex.auth.password_requirements') }}</p>
                @error('password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('carbex.auth.confirm_password') }} *
                </label>
                <div class="mt-2">
                    <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" required
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                </div>
            </div>
        </div>

        @else
        <!-- Step 2: Organization Information -->
        <div class="space-y-4">
            <!-- Organization Name -->
            <div>
                <label for="organization_name" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('carbex.auth.organization_name') }} *
                </label>
                <div class="mt-2">
                    <input wire:model="organization_name" id="organization_name" name="organization_name" type="text" required
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('organization_name') ring-red-300 @enderror">
                </div>
                @error('organization_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Country -->
            <div>
                <label for="country" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('carbex.auth.country') }} *
                </label>
                <div class="mt-2">
                    <select wire:model="country" id="country" required
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                        <option value="DE">{{ __('carbex.emission_factors.countries.de') }}</option>
                        <option value="FR">{{ __('carbex.emission_factors.countries.fr') }}</option>
                        <option value="AT">{{ __('carbex.emission_factors.countries.at') }}</option>
                        <option value="CH">{{ __('carbex.emission_factors.countries.ch') }}</option>
                    </select>
                </div>
            </div>

            <!-- Sector -->
            <div>
                <label for="sector" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('carbex.auth.sector') }}
                </label>
                <div class="mt-2">
                    <select wire:model="sector" id="sector"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                        <option value="">{{ __('carbex.auth.select_sector') }}</option>
                        @foreach(config('countries.sectors', []) as $key => $sector)
                        <option value="{{ $key }}">{{ __("carbex.sectors.{$key}") }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Organization Size -->
            <div>
                <label for="organization_size" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ __('carbex.auth.organization_size') }}
                </label>
                <div class="mt-2">
                    <select wire:model="organization_size" id="organization_size"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6">
                        <option value="">{{ __('carbex.auth.select_size') }}</option>
                        <option value="1-10">1-10 {{ __('carbex.auth.employees') }}</option>
                        <option value="11-50">11-50 {{ __('carbex.auth.employees') }}</option>
                        <option value="51-250">51-250 {{ __('carbex.auth.employees') }}</option>
                        <option value="251-500">251-500 {{ __('carbex.auth.employees') }}</option>
                        <option value="500+">500+ {{ __('carbex.auth.employees') }}</option>
                    </select>
                </div>
            </div>

            <!-- Terms and Privacy -->
            <div class="space-y-3 pt-4">
                <div class="flex items-start">
                    <input wire:model="accept_terms" id="accept_terms" name="accept_terms" type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-600 mt-0.5">
                    <label for="accept_terms" class="ml-3 text-sm text-gray-600">
                        {!! __('carbex.auth.accept_terms_html') !!} *
                    </label>
                </div>
                @error('accept_terms')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

                <div class="flex items-start">
                    <input wire:model="accept_privacy" id="accept_privacy" name="accept_privacy" type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-600 mt-0.5">
                    <label for="accept_privacy" class="ml-3 text-sm text-gray-600">
                        {!! __('carbex.auth.accept_privacy_html') !!} *
                    </label>
                </div>
                @error('accept_privacy')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex {{ $step > 1 ? 'justify-between' : 'justify-end' }} pt-4">
            @if($step > 1)
            <button type="button" wire:click="previousStep"
                class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                {{ __('carbex.common.back') }}
            </button>
            @endif

            <button type="submit"
                class="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 disabled:opacity-50"
                wire:loading.attr="disabled">
                <span wire:loading.remove>
                    {{ $step === 2 ? __('carbex.auth.create_account') : __('carbex.common.next') }}
                </span>
                <span wire:loading>{{ __('carbex.common.loading') }}</span>
            </button>
        </div>
    </form>
</div>

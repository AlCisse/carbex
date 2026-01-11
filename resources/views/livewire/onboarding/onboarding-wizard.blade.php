<div class="max-w-2xl mx-auto py-8">
    {{-- Progress Indicator --}}
    <div class="progress-indicator mb-8 flex justify-center items-center gap-4">
        @for ($i = 1; $i <= 4; $i++)
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $step >= $i ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                    {{ $i }}
                </div>
                @if ($i < 4)
                    <div class="w-12 h-1 {{ $step > $i ? 'bg-green-600' : 'bg-gray-200' }}"></div>
                @endif
            </div>
        @endfor
    </div>

    {{-- Step 1: Company Info --}}
    @if ($step === 1)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" dusk="onboarding-step-1">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('carbex.onboarding.company_info', [], 'de') }}</h2>
            <p class="text-sm text-gray-500 mb-6">{{ __('carbex.onboarding.company_info_desc', [], 'de') }}</p>

            <form wire:submit="nextStep" class="space-y-4">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700">{{ __('carbex.onboarding.company_name', [], 'de') }} *</label>
                    <input wire:model="company_name" type="text" id="company_name" name="company_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                    @error('company_name')
                        <p class="validation-error mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="siret" class="block text-sm font-medium text-gray-700">{{ __('carbex.onboarding.siret', [], 'de') }}</label>
                    <input wire:model="siret" type="text" id="siret" name="siret" maxlength="14" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                    @error('siret')
                        <p class="validation-error mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sector" class="block text-sm font-medium text-gray-700">{{ __('carbex.onboarding.sector', [], 'de') }} *</label>
                    <select wire:model="sector" id="sector" name="sector" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="">{{ __('carbex.onboarding.select_sector', [], 'de') }}</option>
                        <option value="technology">{{ __('carbex.sectors.technology', [], 'de') }}</option>
                        <option value="manufacturing">{{ __('carbex.sectors.manufacturing', [], 'de') }}</option>
                        <option value="services">{{ __('carbex.sectors.services', [], 'de') }}</option>
                        <option value="retail">{{ __('carbex.sectors.retail', [], 'de') }}</option>
                        <option value="healthcare">{{ __('carbex.sectors.healthcare', [], 'de') }}</option>
                        <option value="construction">{{ __('carbex.sectors.construction', [], 'de') }}</option>
                        <option value="transport">{{ __('carbex.sectors.transport', [], 'de') }}</option>
                        <option value="energy">{{ __('carbex.sectors.energy', [], 'de') }}</option>
                        <option value="other">{{ __('carbex.sectors.other', [], 'de') }}</option>
                    </select>
                    @error('sector')
                        <p class="validation-error mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700">{{ __('carbex.onboarding.company_size', [], 'de') }} *</label>
                    <select wire:model="size" id="size" name="size" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        <option value="">{{ __('carbex.onboarding.select_size', [], 'de') }}</option>
                        <option value="small">{{ __('carbex.sizes.small', [], 'de') }}</option>
                        <option value="medium">{{ __('carbex.sizes.medium', [], 'de') }}</option>
                        <option value="large">{{ __('carbex.sizes.large', [], 'de') }}</option>
                        <option value="enterprise">{{ __('carbex.sizes.enterprise', [], 'de') }}</option>
                    </select>
                    @error('size')
                        <p class="validation-error mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        {{ __('carbex.next', [], 'de') }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Step 2: Site Configuration --}}
    @if ($step === 2)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" dusk="onboarding-step-2">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('carbex.onboarding.site_config', [], 'de') }}</h2>
            <p class="text-sm text-gray-500 mb-6">{{ __('carbex.onboarding.site_config_desc', [], 'de') }}</p>

            <form wire:submit="nextStep" class="space-y-4">
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700">{{ __('carbex.onboarding.site_name', [], 'de') }} *</label>
                    <input wire:model="site_name" type="text" id="site_name" name="site_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                    @error('site_name')
                        <p class="validation-error mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="site_address" class="block text-sm font-medium text-gray-700">{{ __('carbex.onboarding.address', [], 'de') }} *</label>
                    <input wire:model="site_address" type="text" id="site_address" name="site_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                    @error('site_address')
                        <p class="validation-error mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="site_city" class="block text-sm font-medium text-gray-700">{{ __('carbex.onboarding.city', [], 'de') }} *</label>
                        <input wire:model="site_city" type="text" id="site_city" name="site_city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('site_city')
                            <p class="validation-error mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="site_postal_code" class="block text-sm font-medium text-gray-700">{{ __('carbex.onboarding.postal_code', [], 'de') }} *</label>
                        <input wire:model="site_postal_code" type="text" id="site_postal_code" name="site_postal_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('site_postal_code')
                            <p class="validation-error mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <button type="button" wire:click="previousStep" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                        {{ __('carbex.previous', [], 'de') }}
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        {{ __('carbex.next', [], 'de') }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Step 3: Bank Connection --}}
    @if ($step === 3)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" dusk="onboarding-step-3">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('carbex.onboarding.bank_connection', [], 'de') }}</h2>
            <p class="text-sm text-gray-500 mb-6">{{ __('carbex.onboarding.bank_connection_desc', [], 'de') }}</p>

            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <p class="text-gray-600 mb-6">{{ __('carbex.onboarding.bank_connection_info', [], 'de') }}</p>

                <button class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 mb-4">
                    {{ __('carbex.onboarding.connect_bank', [], 'de') }}
                </button>
            </div>

            <div class="flex justify-between pt-4 border-t">
                <button type="button" wire:click="previousStep" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                    {{ __('carbex.previous', [], 'de') }}
                </button>
                <button type="button" wire:click="skipStep" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    {{ __('carbex.onboarding.skip_step', [], 'de') }}
                </button>
            </div>
        </div>
    @endif

    {{-- Step 4: Completion --}}
    @if ($step === 4)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center" dusk="onboarding-step-4">
            <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ __('carbex.onboarding.congratulations', [], 'de') }}</h2>
            <p class="text-sm text-gray-500 mb-6">{{ __('carbex.onboarding.setup_complete', [], 'de') }}</p>

            <button wire:click="completeOnboarding" class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700">
                {{ __('carbex.onboarding.go_to_dashboard', [], 'de') }}
            </button>
        </div>
    @endif
</div>

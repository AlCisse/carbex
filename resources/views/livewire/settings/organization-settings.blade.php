<div>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('carbex.settings.organization') }}</h1>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" dismissible class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    <form wire:submit="save" class="space-y-8">
        <!-- General Information -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900">{{ __('carbex.organization.general_info') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.organization.general_info_desc') }}</p>
            </x-slot>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input
                        wire:model="name"
                        name="name"
                        :label="__('carbex.organization.name')"
                        required
                        :error="$errors->first('name')"
                    />
                </div>

                <div>
                    <x-input
                        wire:model="legal_name"
                        name="legal_name"
                        :label="__('carbex.organization.legal_name')"
                        :error="$errors->first('legal_name')"
                    />
                </div>

                <div>
                    <x-select
                        wire:model="sector"
                        name="sector"
                        :label="__('carbex.organization.sector')"
                        :placeholder="__('carbex.common.select')"
                    >
                        <option value="technology">{{ __('carbex.sectors.technology') }}</option>
                        <option value="manufacturing">{{ __('carbex.sectors.manufacturing') }}</option>
                        <option value="retail">{{ __('carbex.sectors.retail') }}</option>
                        <option value="services">{{ __('carbex.sectors.services') }}</option>
                        <option value="healthcare">{{ __('carbex.sectors.healthcare') }}</option>
                        <option value="finance">{{ __('carbex.sectors.finance') }}</option>
                        <option value="construction">{{ __('carbex.sectors.construction') }}</option>
                        <option value="transport">{{ __('carbex.sectors.transport') }}</option>
                        <option value="hospitality">{{ __('carbex.sectors.hospitality') }}</option>
                        <option value="other">{{ __('carbex.sectors.other') }}</option>
                    </x-select>
                </div>

                <div>
                    <x-select
                        wire:model="size"
                        name="size"
                        :label="__('carbex.organization.size')"
                        :placeholder="__('carbex.common.select')"
                    >
                        <option value="1-10">1-10 {{ __('carbex.auth.employees') }}</option>
                        <option value="11-50">11-50 {{ __('carbex.auth.employees') }}</option>
                        <option value="51-250">51-250 {{ __('carbex.auth.employees') }}</option>
                        <option value="251-500">251-500 {{ __('carbex.auth.employees') }}</option>
                        <option value="500+">500+ {{ __('carbex.auth.employees') }}</option>
                    </x-select>
                </div>

                <div>
                    <x-input
                        wire:model="website"
                        name="website"
                        type="url"
                        :label="__('carbex.organization.website')"
                        placeholder="https://"
                        :error="$errors->first('website')"
                    />
                </div>
            </div>
        </x-card>

        <!-- Legal Information -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900">{{ __('carbex.organization.legal_info') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.organization.legal_info_desc') }}</p>
            </x-slot>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <x-input
                        wire:model="registration_number"
                        name="registration_number"
                        :label="$countryConfig['name'] === 'France' ? 'SIRET' : 'Handelsregisternummer'"
                        :placeholder="$countryConfig['name'] === 'France' ? '123 456 789 00012' : 'HRB 12345'"
                        :error="$errors->first('registration_number')"
                    />
                </div>

                <div>
                    <x-input
                        wire:model="vat_number"
                        name="vat_number"
                        :label="__('carbex.organization.vat_number')"
                        :placeholder="$countryConfig['name'] === 'France' ? 'FR12345678901' : 'DE123456789'"
                        :error="$errors->first('vat_number')"
                    />
                </div>
            </div>
        </x-card>

        <!-- Contact Information -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900">{{ __('carbex.organization.contact_info') }}</h2>
            </x-slot>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <x-input
                        wire:model="email"
                        name="email"
                        type="email"
                        :label="__('carbex.auth.email')"
                        :error="$errors->first('email')"
                    />
                </div>

                <div>
                    <x-input
                        wire:model="phone"
                        name="phone"
                        type="tel"
                        :label="__('carbex.organization.phone')"
                        :error="$errors->first('phone')"
                    />
                </div>

                <div class="sm:col-span-2">
                    <x-input
                        wire:model="address_line_1"
                        name="address_line_1"
                        :label="__('carbex.organization.address')"
                        :error="$errors->first('address_line_1')"
                    />
                </div>

                <div class="sm:col-span-2">
                    <x-input
                        wire:model="address_line_2"
                        name="address_line_2"
                        :label="__('carbex.organization.address_line_2')"
                        :error="$errors->first('address_line_2')"
                    />
                </div>

                <div>
                    <x-input
                        wire:model="postal_code"
                        name="postal_code"
                        :label="__('carbex.organization.postal_code')"
                        :error="$errors->first('postal_code')"
                    />
                </div>

                <div>
                    <x-input
                        wire:model="city"
                        name="city"
                        :label="__('carbex.organization.city')"
                        :error="$errors->first('city')"
                    />
                </div>
            </div>
        </x-card>

        <!-- Fiscal Settings -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900">{{ __('carbex.organization.fiscal_settings') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.organization.fiscal_settings_desc') }}</p>
            </x-slot>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <x-select
                        wire:model="fiscal_year_start_month"
                        name="fiscal_year_start_month"
                        :label="__('carbex.organization.fiscal_year_start')"
                    >
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                        @endfor
                    </x-select>
                </div>

                <div>
                    <x-select
                        wire:model="default_currency"
                        name="default_currency"
                        :label="__('carbex.organization.default_currency')"
                    >
                        <option value="EUR">{{ __('carbex.organization.currencies.eur') }}</option>
                        <option value="CHF">{{ __('carbex.organization.currencies.chf') }}</option>
                        <option value="GBP">{{ __('carbex.organization.currencies.gbp') }}</option>
                    </x-select>
                </div>
            </div>
        </x-card>

        <!-- Display Settings -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900">{{ __('carbex.organization.display_settings') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.organization.display_settings_desc') }}</p>
            </x-slot>

            <div class="space-y-6">
                <!-- Navigation Mode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        {{ __('carbex.organization.navigation_mode') }}
                    </label>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Scopes Navigation -->
                        <label class="relative flex cursor-pointer rounded-lg border p-4 shadow-sm focus:outline-none {{ $navigation_mode === 'scopes' ? 'border-emerald-500 ring-2 ring-emerald-500' : 'border-gray-300' }}">
                            <input type="radio" wire:model.live="navigation_mode" value="scopes" class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="flex items-center">
                                        <svg class="h-6 w-6 {{ $navigation_mode === 'scopes' ? 'text-emerald-600' : 'text-gray-400' }} mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        <span class="block text-sm font-medium text-gray-900">{{ __('carbex.navigation.mode.scopes') }}</span>
                                    </span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500">
                                        {{ __('carbex.organization.navigation_scopes_desc') }}
                                    </span>
                                </span>
                            </span>
                            @if($navigation_mode === 'scopes')
                                <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </label>

                        <!-- 5 Pillars Navigation -->
                        <label class="relative flex cursor-pointer rounded-lg border p-4 shadow-sm focus:outline-none {{ $navigation_mode === 'pillars' ? 'border-emerald-500 ring-2 ring-emerald-500' : 'border-gray-300' }}">
                            <input type="radio" wire:model.live="navigation_mode" value="pillars" class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="flex items-center">
                                        <svg class="h-6 w-6 {{ $navigation_mode === 'pillars' ? 'text-emerald-600' : 'text-gray-400' }} mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                                        </svg>
                                        <span class="block text-sm font-medium text-gray-900">{{ __('carbex.navigation.mode.pillars') }}</span>
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">NEW</span>
                                    </span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500">
                                        {{ __('carbex.organization.navigation_pillars_desc') }}
                                    </span>
                                </span>
                            </span>
                            @if($navigation_mode === 'pillars')
                                <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </label>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Country Configuration (Read-only) -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900">{{ __('carbex.organization.country_config') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.organization.country_config_desc') }}</p>
            </x-slot>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-sm font-medium text-gray-500">{{ __('carbex.organization.country') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $countryConfig['name'] ?? $organization->country }}</dd>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-sm font-medium text-gray-500">{{ __('carbex.organization.timezone') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $organization->timezone }}</dd>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <dt class="text-sm font-medium text-gray-500">{{ __('carbex.organization.vat_rate') }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $countryConfig['vat_standard'] ?? 20 }}%</dd>
                </div>
            </div>
        </x-card>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <x-button type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('carbex.common.save') }}</span>
                <span wire:loading>{{ __('carbex.common.saving') }}</span>
            </x-button>
        </div>
    </form>
</div>

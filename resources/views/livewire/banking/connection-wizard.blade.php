<div class="max-w-4xl mx-auto">
    {{-- Progress Steps --}}
    <nav aria-label="Progress" class="mb-8">
        <ol class="flex items-center justify-center space-x-5">
            @foreach([1 => __('carbex.banking_wizard.country'), 2 => __('carbex.banking_wizard.bank'), 3 => __('carbex.banking_wizard.authorize'), 4 => __('carbex.banking_wizard.connect'), 5 => __('carbex.banking_wizard.done')] as $stepNum => $stepName)
                <li class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium
                        {{ $step >= $stepNum ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                        @if($step > $stepNum)
                            <x-heroicon-s-check class="w-5 h-5" />
                        @else
                            {{ $stepNum }}
                        @endif
                    </span>
                    <span class="ml-2 text-sm {{ $step >= $stepNum ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}">
                        {{ $stepName }}
                    </span>
                    @if($stepNum < 5)
                        <x-heroicon-s-chevron-right class="w-5 h-5 ml-5 text-gray-400" />
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    {{-- Error Message --}}
    @if($errorMessage)
        <x-alert type="error" class="mb-6">
            {{ $errorMessage }}
        </x-alert>
    @endif

    {{-- Step 1: Select Country --}}
    @if($step === 1)
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">{{ __('carbex.banking_wizard.select_country') }}</h3>
                <p class="text-sm text-gray-500">{{ __('carbex.banking_wizard.supported_countries') }}</p>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button wire:click="selectCountry('FR')"
                    class="flex items-center p-6 border-2 rounded-lg hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition
                        {{ $country === 'FR' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                    <span class="text-4xl mr-4">🇫🇷</span>
                    <div class="text-left">
                        <span class="block font-semibold">France</span>
                        <span class="text-sm text-gray-500">{{ __('carbex.banking_wizard.via') }} Bridge.io</span>
                    </div>
                </button>

                <button wire:click="selectCountry('DE')"
                    class="flex items-center p-6 border-2 rounded-lg hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition
                        {{ $country === 'DE' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                    <span class="text-4xl mr-4">🇩🇪</span>
                    <div class="text-left">
                        <span class="block font-semibold">Deutschland</span>
                        <span class="text-sm text-gray-500">{{ __('carbex.banking_wizard.via') }} Finapi</span>
                    </div>
                </button>
            </div>
        </x-card>
    @endif

    {{-- Step 2: Select Bank --}}
    @if($step === 2)
        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('carbex.banking_wizard.select_bank') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('carbex.banking_wizard.supported_banks') }}</p>
                    </div>
                    <button wire:click="previousStep" class="text-sm text-gray-500 hover:text-gray-700">
                        &larr; {{ __('carbex.back') }}
                    </button>
                </div>
            </x-slot>

            {{-- Search --}}
            <div class="mb-6">
                <x-input
                    wire:model.live.debounce.300ms="searchQuery"
                    type="search"
                    placeholder="{{ __('carbex.banking_wizard.search_banks') }}"
                    class="w-full"
                />
            </div>

            {{-- Banks Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-96 overflow-y-auto">
                @forelse($this->banks as $bank)
                    <button wire:click="selectBank('{{ $bank['id'] }}')"
                        class="flex flex-col items-center p-4 border rounded-lg hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition
                            {{ $selectedBankId === $bank['id'] ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                        @if($bank['logo_url'])
                            <img src="{{ $bank['logo_url'] }}" alt="{{ $bank['name'] }}" class="w-12 h-12 object-contain mb-2">
                        @else
                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-2">
                                <x-heroicon-o-building-library class="w-6 h-6 text-gray-400" />
                            </div>
                        @endif
                        <span class="text-sm text-center font-medium truncate w-full">{{ $bank['name'] }}</span>
                    </button>
                @empty
                    <div class="col-span-full text-center py-8 text-gray-500">
                        {{ __('carbex.banking_wizard.no_banks_found') }}
                    </div>
                @endforelse
            </div>
        </x-card>
    @endif

    {{-- Step 3: Authorization Info --}}
    @if($step === 3)
        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('carbex.banking_wizard.authorize_connection') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('carbex.banking_wizard.redirect_to_bank') }}</p>
                    </div>
                    <button wire:click="previousStep" class="text-sm text-gray-500 hover:text-gray-700">
                        &larr; {{ __('carbex.back') }}
                    </button>
                </div>
            </x-slot>

            <div class="space-y-6">
                {{-- Security Info --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex">
                        <x-heroicon-s-shield-check class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0" />
                        <div>
                            <h4 class="font-semibold text-blue-800 dark:text-blue-200">{{ __('carbex.banking_wizard.secure_connection') }}</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                {{ __('carbex.banking_wizard.psd2_info') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- What we access --}}
                <div>
                    <h4 class="font-semibold mb-3">{{ __('carbex.banking_wizard.what_we_access') }}</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-green-500 mr-2" />
                            {{ __('carbex.banking_wizard.account_balances') }}
                        </li>
                        <li class="flex items-center text-sm">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-green-500 mr-2" />
                            {{ __('carbex.banking_wizard.transaction_history') }}
                        </li>
                        <li class="flex items-center text-sm">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-green-500 mr-2" />
                            {{ __('carbex.banking_wizard.transaction_details') }}
                        </li>
                    </ul>
                </div>

                {{-- What we don't access --}}
                <div>
                    <h4 class="font-semibold mb-3">{{ __('carbex.banking_wizard.what_we_dont_access') }}</h4>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-s-x-circle class="w-5 h-5 text-red-500 mr-2" />
                            {{ __('carbex.banking_wizard.login_credentials') }}
                        </li>
                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-s-x-circle class="w-5 h-5 text-red-500 mr-2" />
                            {{ __('carbex.banking_wizard.ability_transfers') }}
                        </li>
                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-s-x-circle class="w-5 h-5 text-red-500 mr-2" />
                            {{ __('carbex.banking_wizard.investment_details') }}
                        </li>
                    </ul>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t dark:border-gray-700">
                    <x-button wire:click="previousStep" variant="secondary">
                        {{ __('carbex.cancel') }}
                    </x-button>
                    <x-button wire:click="initiateConnection" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="initiateConnection">
                            {{ __('carbex.banking_wizard.continue_to_bank') }}
                        </span>
                        <span wire:loading wire:target="initiateConnection">
                            {{ __('carbex.banking_wizard.redirecting') }}
                        </span>
                    </x-button>
                </div>
            </div>
        </x-card>
    @endif

    {{-- Step 4: Redirect --}}
    @if($step === 4 && $redirectUrl)
        <x-card class="text-center py-12">
            <div class="animate-pulse">
                <x-heroicon-o-arrow-top-right-on-square class="w-16 h-16 mx-auto text-green-600 mb-4" />
                <h3 class="text-lg font-semibold mb-2">{{ __('carbex.banking_wizard.redirecting_to_bank') }}</h3>
                <p class="text-gray-500 mb-6">{{ __('carbex.banking_wizard.complete_authorization') }}</p>
                <a href="{{ $redirectUrl }}" target="_blank" class="text-green-600 hover:underline">
                    {{ __('carbex.banking_wizard.click_if_not_redirected') }}
                </a>
            </div>
        </x-card>

        <script>
            window.open('{{ $redirectUrl }}', '_blank');
        </script>
    @endif

    {{-- Step 5: Success --}}
    @if($step === 5 && $connectionSuccess)
        <x-card class="text-center py-12">
            <div class="text-green-600 mb-4">
                <x-heroicon-s-check-circle class="w-20 h-20 mx-auto" />
            </div>
            <h3 class="text-2xl font-bold mb-2">{{ __('carbex.banking_wizard.bank_connected') }}</h3>
            <p class="text-gray-500 mb-6">{{ __('carbex.banking_wizard.sync_in_progress') }}</p>

            <div class="flex justify-center space-x-4">
                <x-button wire:click="resetWizard" variant="secondary">
                    {{ __('carbex.banking_wizard.connect_another') }}
                </x-button>
                <x-button href="{{ route('dashboard') }}">
                    {{ __('carbex.import.go_to_dashboard') }}
                </x-button>
            </div>
        </x-card>
    @endif

    {{-- Existing Connections --}}
    @if($this->hasActiveConnections && $step === 1)
        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-4">{{ __('carbex.banking_wizard.connected_banks') }}</h3>
            <div class="space-y-3">
                @foreach($this->connections as $connection)
                    <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mr-3">
                                <x-heroicon-o-building-library class="w-5 h-5 text-gray-500" />
                            </div>
                            <div>
                                <span class="font-medium">{{ $connection->bank_name }}</span>
                                <div class="flex items-center text-sm text-gray-500">
                                    <span class="mr-2">{{ $connection->accounts->count() }} {{ __('carbex.banking_wizard.accounts') }}</span>
                                    @if($connection->last_sync_at)
                                        <span>{{ __('carbex.banking_wizard.synced') }} {{ $connection->last_sync_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <x-badge :variant="$connection->status === 'active' ? 'success' : 'warning'">
                                {{ $connection->status }}
                            </x-badge>
                            <button wire:click="syncConnection('{{ $connection->id }}')"
                                class="p-2 text-gray-500 hover:text-green-600" title="{{ __('carbex.banking_wizard.sync_now') }}">
                                <x-heroicon-o-arrow-path class="w-5 h-5" />
                            </button>
                            <button wire:click="disconnectBank('{{ $connection->id }}')"
                                wire:confirm="{{ __('carbex.banking_wizard.disconnect_confirm') }}"
                                class="p-2 text-gray-500 hover:text-red-600" title="{{ __('carbex.banking_wizard.disconnect') }}">
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

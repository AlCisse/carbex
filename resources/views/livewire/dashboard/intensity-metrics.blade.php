<div>
    <x-card>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('linscarbon.dashboard.emission_intensity') }}
                </h3>
                <x-heroicon-o-information-circle
                    class="w-5 h-5 text-gray-400 cursor-help"
                    title="{{ __('linscarbon.dashboard.intensity_help') }}"
                />
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Per Employee --}}
            <div class="p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <x-heroicon-o-users class="w-6 h-6 text-green-600" />
                    </div>
                    <span class="text-xs font-medium text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/40 px-2 py-1 rounded">
                        {{ __('linscarbon.dashboard.per_employee') }}
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ number_format($this->intensity['per_employee']['tonnes'], 2) }}
                    <span class="text-lg font-normal text-gray-500">t CO₂e</span>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ number_format($this->intensity['per_employee']['kg'], 0, ',', ' ') }} kg {{ __('linscarbon.dashboard.per_employee') }}
                </div>
                <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-800">
                    <div class="flex items-center text-sm">
                        <x-heroicon-o-user-group class="w-4 h-4 text-gray-400 mr-2" />
                        <span class="text-gray-600 dark:text-gray-400">
                            {{ $this->intensity['employee_count'] }} {{ __('linscarbon.dashboard.employees') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Per €1000 Spend --}}
            <div class="p-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                        <x-heroicon-o-banknotes class="w-6 h-6 text-blue-600" />
                    </div>
                    <span class="text-xs font-medium text-blue-700 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded">
                        {{ __('linscarbon.dashboard.per_1000_eur') }}
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ number_format($this->intensity['per_1000_eur']['kg'], 1) }}
                    <span class="text-lg font-normal text-gray-500">kg CO₂e</span>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('linscarbon.dashboard.emission_intensity_per_1000') }}
                </div>
                <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-800">
                    <div class="flex items-center text-sm">
                        <x-heroicon-o-currency-euro class="w-4 h-4 text-gray-400 mr-2" />
                        <span class="text-gray-600 dark:text-gray-400">
                            {{ number_format($this->intensity['total_spend'], 0, ',', ' ') }} € {{ __('linscarbon.dashboard.total_spend') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Benchmarks --}}
        <div class="mt-6 pt-6 border-t dark:border-gray-700">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                {{ __('linscarbon.dashboard.industry_benchmarks') }}
            </h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <span class="text-gray-600 dark:text-gray-400">{{ __('linscarbon.dashboard.sme_services') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">2,5 t/{{ __('linscarbon.dashboard.per_employee') }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <span class="text-gray-600 dark:text-gray-400">{{ __('linscarbon.dashboard.sme_manufacturing') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">8,2 t/{{ __('linscarbon.dashboard.per_employee') }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <span class="text-gray-600 dark:text-gray-400">{{ __('linscarbon.dashboard.sme_retail') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">4,1 t/{{ __('linscarbon.dashboard.per_employee') }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <span class="text-gray-600 dark:text-gray-400">{{ __('linscarbon.dashboard.sme_it') }}</span>
                    <span class="font-medium text-gray-900 dark:text-white">1,8 t/{{ __('linscarbon.dashboard.per_employee') }}</span>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500">
                {{ __('linscarbon.dashboard.benchmarks_source') }}
            </p>
        </div>
    </x-card>
</div>

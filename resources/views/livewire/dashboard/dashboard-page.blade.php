<div>
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('carbex.dashboard.title') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('carbex.dashboard.overview_for', ['organization' => auth()->user()->organization->name ?? '']) }}
                </p>
            </div>

            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-3">
                <livewire:dashboard.filters.site-filter :selected-site="$siteId" />
                <livewire:dashboard.filters.period-selector
                    :start-date="$startDate"
                    :end-date="$endDate"
                />
                <button
                    wire:click="refreshDashboard"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                    title="{{ __('carbex.dashboard.refresh_data') }}"
                >
                    <x-heroicon-o-arrow-path
                        class="w-5 h-5"
                        wire:loading.class="animate-spin"
                        wire:target="refreshDashboard"
                    />
                </button>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="mb-8">
        <livewire:dashboard.emission-overview
            :site-id="$siteId"
            :start-date="$startDate"
            :end-date="$endDate"
        />
    </div>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Scope Breakdown --}}
        <livewire:dashboard.scope-breakdown
            :site-id="$siteId"
            :start-date="$startDate"
            :end-date="$endDate"
        />

        {{-- Top Categories --}}
        <livewire:dashboard.top-categories
            :site-id="$siteId"
            :start-date="$startDate"
            :end-date="$endDate"
        />
    </div>

    {{-- Trend Chart (Full Width) --}}
    <div class="mb-8">
        <livewire:dashboard.trend-chart
            :site-id="$siteId"
            :start-date="$startDate"
            :end-date="$endDate"
        />
    </div>

    {{-- Recent Transactions --}}
    <x-card>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('carbex.dashboard.recent_transactions') }}
                </h3>
                <a href="{{ route('transactions.index') }}" class="text-sm text-green-600 hover:text-green-700 dark:text-green-400">
                    {{ __('carbex.common.view_all') }}
                    <x-heroicon-s-arrow-right class="w-4 h-4 inline ml-1" />
                </a>
            </div>
        </x-slot>

        @if(count($this->recentTransactions) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('carbex.transactions.date') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('carbex.transactions.description') }}
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('carbex.transactions.category') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('carbex.transactions.amount') }}
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('carbex.transactions.emissions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->recentTransactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($transaction['date'])->format('M j, Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    <span class="truncate block max-w-xs">{{ $transaction['description'] }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @if($transaction['category'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ match($transaction['scope']) {
                                                1 => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                2 => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                                3 => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                                            } }}">
                                            {{ $transaction['category'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right {{ $transaction['amount'] < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    {{ number_format($transaction['amount'], 2) }} {{ $transaction['currency'] }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                    @if($transaction['emissions_kg'] > 0)
                                        {{ number_format($transaction['emissions_kg'], 1) }} kg
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <x-heroicon-o-banknotes class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('carbex.dashboard.no_transactions') }}
                </h4>
                <p class="text-gray-500 mb-4">
                    {{ __('carbex.dashboard.connect_bank_prompt') }}
                </p>
                <a href="{{ route('banking.connect') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                    <x-heroicon-s-plus class="w-4 h-4 mr-2" />
                    {{ __('carbex.banking.connect') }}
                </a>
            </div>
        @endif
    </x-card>
</div>

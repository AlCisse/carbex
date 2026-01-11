<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    {{-- Total Emissions Card --}}
    <x-card class="relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 -mr-8 -mt-8">
            <div class="w-full h-full bg-green-100 dark:bg-green-900/30 rounded-full opacity-50"></div>
        </div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ __('carbex.dashboard.total_emissions') }}
                </span>
                @if($this->kpis['total_emissions']['trend_direction'] !== 'stable')
                    <span class="flex items-center text-sm font-medium
                        {{ $this->kpis['total_emissions']['trend_direction'] === 'down' ? 'text-green-600' : 'text-red-600' }}">
                        @if($this->kpis['total_emissions']['trend_direction'] === 'down')
                            <x-heroicon-s-arrow-trending-down class="w-4 h-4 mr-1" />
                        @else
                            <x-heroicon-s-arrow-trending-up class="w-4 h-4 mr-1" />
                        @endif
                        {{ abs($this->kpis['total_emissions']['trend_percent']) }}%
                    </span>
                @endif
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ number_format($this->kpis['total_emissions']['tonnes'], 1) }}
                <span class="text-lg font-normal text-gray-500">t CO₂e</span>
            </div>
            <div class="text-sm text-gray-500 mt-1">
                {{ number_format($this->kpis['total_emissions']['kg'], 0, ',', ' ') }} kg
            </div>
        </div>
    </x-card>

    {{-- Scope 1 Card --}}
    <x-card>
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ __('carbex.dashboard.scope_1') }}
            </span>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                {{ $this->kpis['scope_1']['percent'] }}%
            </span>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ number_format($this->kpis['scope_1']['tonnes'], 2) }}
            <span class="text-sm font-normal text-gray-500">t CO₂e</span>
        </div>
        <div class="mt-2">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ min($this->kpis['scope_1']['percent'], 100) }}%"></div>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">{{ __('carbex.dashboard.direct_emissions') }}</p>
    </x-card>

    {{-- Scope 2 Card --}}
    <x-card>
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ __('carbex.dashboard.scope_2') }}
            </span>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                {{ $this->kpis['scope_2']['percent'] }}%
            </span>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ number_format($this->kpis['scope_2']['tonnes'], 2) }}
            <span class="text-sm font-normal text-gray-500">t CO₂e</span>
        </div>
        <div class="mt-2">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ min($this->kpis['scope_2']['percent'], 100) }}%"></div>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">{{ __('carbex.dashboard.indirect_energy') }}</p>
    </x-card>

    {{-- Scope 3 Card --}}
    <x-card>
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ __('carbex.dashboard.scope_3') }}
            </span>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                {{ $this->kpis['scope_3']['percent'] }}%
            </span>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ number_format($this->kpis['scope_3']['tonnes'], 2) }}
            <span class="text-sm font-normal text-gray-500">t CO₂e</span>
        </div>
        <div class="mt-2">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ min($this->kpis['scope_3']['percent'], 100) }}%"></div>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">{{ __('carbex.dashboard.value_chain') }}</p>
    </x-card>

    {{-- Transaction Coverage Card --}}
    <x-card class="md:col-span-2 lg:col-span-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ __('carbex.dashboard.transaction_coverage') }}
                </h3>
                <div class="mt-1 flex items-baseline">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $this->kpis['transactions']['coverage_percent'] }}%
                    </span>
                    <span class="ml-2 text-sm text-gray-500">
                        {{ __('carbex.dashboard.categorized_of_total', [
                            'categorized' => number_format($this->kpis['transactions']['categorized']),
                            'total' => number_format($this->kpis['transactions']['total']),
                        ]) }}
                    </span>
                </div>
            </div>
            @if($this->kpis['transactions']['pending'] > 0)
                <a href="{{ route('transactions.index', ['filter' => 'pending']) }}"
                   class="text-sm text-green-600 hover:text-green-700 dark:text-green-400">
                    {{ __('carbex.dashboard.pending_count', ['count' => $this->kpis['transactions']['pending']]) }}
                    <x-heroicon-s-arrow-right class="w-4 h-4 inline ml-1" />
                </a>
            @endif
        </div>
        <div class="mt-4">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full transition-all duration-500"
                     style="width: {{ min($this->kpis['transactions']['coverage_percent'], 100) }}%"></div>
            </div>
        </div>
    </x-card>
</div>

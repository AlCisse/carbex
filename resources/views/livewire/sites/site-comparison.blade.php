<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('carbex.sites.comparison.title') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.subtitle') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Year Selector -->
                <select wire:model.live="selectedYear" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @foreach($yearOptions as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

                <!-- Sort Options -->
                <select wire:model.live="sortBy" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="emissions">{{ __('carbex.sites.comparison.sort_by_emissions') }}</option>
                    <option value="per_m2">{{ __('carbex.sites.comparison.sort_by_m2') }}</option>
                    <option value="per_employee">{{ __('carbex.sites.comparison.sort_by_employee') }}</option>
                    <option value="name">{{ __('carbex.sites.comparison.sort_by_name') }}</option>
                </select>

                <button wire:click="toggleSortOrder" class="p-2 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                    @if($sortOrder === 'desc')
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                        </svg>
                    @endif
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-card class="text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ count($sites) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.total_sites') }}</div>
            </x-card>

            <x-card class="text-center">
                <div class="text-3xl font-bold text-emerald-600">{{ number_format($this->getTotalOrganizationEmissions(), 1) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.total_emissions_t') }}</div>
            </x-card>

            <x-card class="text-center">
                @if($this->getAverageEmissionsPerM2())
                    <div class="text-3xl font-bold text-blue-600">{{ number_format($this->getAverageEmissionsPerM2(), 1) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.avg_per_m2') }}</div>
                @else
                    <div class="text-2xl font-bold text-gray-400">-</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.avg_per_m2') }}</div>
                @endif
            </x-card>

            <x-card class="text-center">
                @if($this->getAverageEmissionsPerEmployee())
                    <div class="text-3xl font-bold text-purple-600">{{ number_format($this->getAverageEmissionsPerEmployee(), 2) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.avg_per_employee') }}</div>
                @else
                    <div class="text-2xl font-bold text-gray-400">-</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.avg_per_employee') }}</div>
                @endif
            </x-card>
        </div>

        <!-- Recommendations -->
        @if(count($recommendations) > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($recommendations as $rec)
            <div class="p-4 rounded-lg border
                @if($rec['type'] === 'warning') border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-700
                @elseif($rec['type'] === 'success') border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 dark:border-emerald-700
                @else border-blue-200 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-700
                @endif">
                <div class="flex items-start space-x-3">
                    @if($rec['icon'] === 'exclamation-triangle')
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    @elseif($rec['icon'] === 'light-bulb')
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    @endif
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ $rec['title'] }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $rec['message'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Chart Section -->
        @if(count($siteEmissions) > 0)
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('carbex.sites.comparison.emissions_by_site') }}</h2>
            </x-slot>

            <div class="h-80" wire:ignore>
                <canvas id="siteComparisonChart"></canvas>
            </div>
        </x-card>
        @endif

        <!-- Sites Table -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('carbex.sites.comparison.detailed_breakdown') }}</h2>
            </x-slot>

            @if(count($sites) === 0)
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ __('carbex.sites.comparison.no_sites') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.no_sites_desc') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('settings.sites') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium text-sm">
                            {{ __('carbex.sites.add') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('carbex.sites.name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('carbex.common.location') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Scope 1</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Scope 2</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Scope 3</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('carbex.sites.comparison.total_t') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">kg/m2</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('carbex.sites.comparison.rating') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($siteEmissions as $emission)
                                @php
                                    $siteIndex = array_search($emission['site_id'], array_column($sites, 'id'));
                                    $site = $sites[$siteIndex] ?? null;
                                @endphp
                                @if($site)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $site['name'] }}
                                                    @if($site['is_primary'])
                                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                            {{ __('carbex.sites.primary') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $site['code'] ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $site['city'] ?? '-' }}, {{ $site['country'] ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400">
                                        {{ number_format($emission['scope1'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-amber-600 dark:text-amber-400">
                                        {{ number_format($emission['scope2'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($emission['scope3'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($emission['total'], 2) }} t
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                        {{ $emission['per_m2'] !== null ? number_format($emission['per_m2'], 1) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white font-bold text-sm
                                            @if($emission['efficiency_label'] === 'A') bg-green-500
                                            @elseif($emission['efficiency_label'] === 'B') bg-lime-500
                                            @elseif($emission['efficiency_label'] === 'C') bg-yellow-400
                                            @elseif($emission['efficiency_label'] === 'D') bg-amber-500
                                            @elseif($emission['efficiency_label'] === 'E') bg-orange-500
                                            @elseif($emission['efficiency_label'] === 'F') bg-red-400
                                            @elseif($emission['efficiency_label'] === 'G') bg-red-600
                                            @else bg-gray-400
                                            @endif">
                                            {{ $emission['efficiency_label'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>

    @if(count($siteEmissions) > 0)
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:navigated', initChart);
        document.addEventListener('DOMContentLoaded', initChart);

        function initChart() {
            const ctx = document.getElementById('siteComparisonChart');
            if (!ctx) return;

            // Destroy existing chart if any
            if (window.siteChart) {
                window.siteChart.destroy();
            }

            const chartData = @json($chartData);

            window.siteChart = new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'Tonnes CO2e'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw.toFixed(2) + ' t CO2e';
                                }
                            }
                        }
                    }
                }
            });
        }

        Livewire.on('chartDataUpdated', () => {
            initChart();
        });
    </script>
    @endpush
    @endif
</div>

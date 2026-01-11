<div class="space-y-6">
    {{-- Page Header --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('carbex.sites.comparison.title') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('carbex.sites.comparison.subtitle') }}
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('settings.sites') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-gray-700">
                <x-heroicon-o-cog-6-tooth class="h-5 w-5" />
                {{ __('carbex.sites.manage_sites') }}
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Sites --}}
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0 rounded-lg bg-green-100 p-3 dark:bg-green-900/30">
                    <x-heroicon-o-building-office-2 class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.total_sites') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->sites->count() }}</p>
                </div>
            </div>
        </div>

        {{-- Total Emissions --}}
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0 rounded-lg bg-blue-100 p-3 dark:bg-blue-900/30">
                    <x-heroicon-o-cloud class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.total_emissions') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->totalEmissions, 1) }} <span class="text-sm font-normal text-gray-500">t CO₂e</span></p>
                </div>
            </div>
        </div>

        {{-- Top Emitter --}}
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0 rounded-lg bg-amber-100 p-3 dark:bg-amber-900/30">
                    <x-heroicon-o-arrow-trending-up class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.top_emitter') }}</p>
                    @if($this->topEmitter)
                        <p class="text-lg font-bold text-gray-900 dark:text-white truncate" title="{{ $this->topEmitter['name'] }}">{{ Str::limit($this->topEmitter['name'], 20) }}</p>
                        <p class="text-sm text-gray-500">{{ number_format($this->topEmitter['total_co2e_tonnes'], 1) }} t CO₂e</p>
                    @else
                        <p class="text-lg font-bold text-gray-400">-</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Average per Site --}}
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0 rounded-lg bg-purple-100 p-3 dark:bg-purple-900/30">
                    <x-heroicon-o-chart-bar class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.average_per_site') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->averagePerSite, 1) }} <span class="text-sm font-normal text-gray-500">t CO₂e</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Year Filter --}}
            <div>
                <label for="year" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('carbex.sites.comparison.year') }}</label>
                <select wire:model.live="selectedYear" id="year" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach($this->availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Scope Filter --}}
            <div>
                <label for="scope" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('carbex.sites.comparison.scope') }}</label>
                <select wire:model.live="selectedScope" id="scope" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('carbex.sites.comparison.all_scopes') }}</option>
                    <option value="1">Scope 1</option>
                    <option value="2">Scope 2</option>
                    <option value="3">Scope 3</option>
                </select>
            </div>

            {{-- Metric Filter --}}
            <div>
                <label for="metric" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('carbex.sites.comparison.metric') }}</label>
                <select wire:model.live="comparisonMetric" id="metric" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="total">{{ __('carbex.sites.comparison.metric_total') }}</option>
                    <option value="per_m2">{{ __('carbex.sites.comparison.metric_per_m2') }}</option>
                    <option value="per_employee">{{ __('carbex.sites.comparison.metric_per_employee') }}</option>
                </select>
            </div>

            {{-- Sort By --}}
            <div>
                <label for="sort" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('carbex.sites.comparison.sort_by') }}</label>
                <select wire:model.live="sortBy" id="sort" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="emissions_desc">{{ __('carbex.sites.comparison.sort_emissions_desc') }}</option>
                    <option value="emissions_asc">{{ __('carbex.sites.comparison.sort_emissions_asc') }}</option>
                    <option value="name_asc">{{ __('carbex.sites.comparison.sort_name_asc') }}</option>
                    <option value="name_desc">{{ __('carbex.sites.comparison.sort_name_desc') }}</option>
                    <option value="intensity_desc">{{ __('carbex.sites.comparison.sort_intensity_desc') }}</option>
                    <option value="intensity_asc">{{ __('carbex.sites.comparison.sort_intensity_asc') }}</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('carbex.sites.comparison.chart_title') }}</h2>
        @if($this->sites->count() > 0)
            <div id="site-comparison-chart" wire:ignore></div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <x-heroicon-o-building-office class="h-12 w-12 text-gray-400" />
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('carbex.sites.comparison.no_sites') }}</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.sites.comparison.no_sites_description') }}</p>
                <a href="{{ route('settings.sites') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                    <x-heroicon-o-plus class="h-5 w-5" />
                    {{ __('carbex.sites.add_site') }}
                </a>
            </div>
        @endif
    </div>

    {{-- Comparison Table --}}
    @if($this->siteEmissions->count() > 0)
    <div class="rounded-lg bg-white shadow dark:bg-gray-800">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('carbex.sites.comparison.table_title') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('carbex.sites.comparison.site') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Scope 1
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Scope 2
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Scope 3
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('carbex.sites.comparison.total') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('carbex.sites.comparison.intensity') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('carbex.sites.comparison.share') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @foreach($this->siteEmissions as $site)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($site['is_primary'])
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                                            <x-heroicon-o-star class="h-4 w-4 text-green-600 dark:text-green-400" />
                                        </span>
                                    @else
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                            <x-heroicon-o-building-office class="h-4 w-4 text-gray-600 dark:text-gray-400" />
                                        </span>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $site['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $site['city'] ?? '-' }}
                                        @if($site['floor_area_m2'])
                                            &bull; {{ number_format($site['floor_area_m2']) }} m²
                                        @endif
                                        @if($site['employee_count'])
                                            &bull; {{ $site['employee_count'] }} {{ __('carbex.sites.employees') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            <span class="font-medium text-gray-900 dark:text-white">{{ number_format($site['scope_1'] / 1000, 2) }}</span>
                            <span class="text-xs text-gray-500">t</span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            <span class="font-medium text-gray-900 dark:text-white">{{ number_format($site['scope_2'] / 1000, 2) }}</span>
                            <span class="text-xs text-gray-500">t</span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            <span class="font-medium text-gray-900 dark:text-white">{{ number_format($site['scope_3'] / 1000, 2) }}</span>
                            <span class="text-xs text-gray-500">t</span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($site['total_co2e_tonnes'], 2) }}</span>
                            <span class="text-xs text-gray-500">t CO₂e</span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            @if($site['floor_area_m2'] > 0)
                                <span class="font-medium text-gray-900 dark:text-white">{{ number_format($site['per_m2'], 1) }}</span>
                                <span class="text-xs text-gray-500">kg/m²</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-center">
                            @php
                                $share = $this->totalEmissions > 0 ? ($site['total_co2e_tonnes'] / $this->totalEmissions) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-center gap-2">
                                <div class="h-2 w-16 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div class="h-full rounded-full bg-green-500" style="width: {{ min($share, 100) }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($share, 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <td class="whitespace-nowrap px-6 py-4 font-bold text-gray-900 dark:text-white">
                            {{ __('carbex.sites.comparison.total') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                            {{ number_format($this->siteEmissions->sum('scope_1') / 1000, 2) }} t
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                            {{ number_format($this->siteEmissions->sum('scope_2') / 1000, 2) }} t
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                            {{ number_format($this->siteEmissions->sum('scope_3') / 1000, 2) }} t
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                            {{ number_format($this->totalEmissions, 2) }} t CO₂e
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-gray-500">-</td>
                        <td class="whitespace-nowrap px-6 py-4 text-center font-bold text-gray-900 dark:text-white">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- Recommendations --}}
    @if($this->recommendations->count() > 0)
    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
            <x-heroicon-o-light-bulb class="inline-block h-5 w-5 mr-2 text-amber-500" />
            {{ __('carbex.sites.comparison.recommendations') }}
        </h2>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach($this->recommendations as $siteId => $data)
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <h3 class="font-medium text-gray-900 dark:text-white mb-3">{{ $data['site_name'] }}</h3>
                    <ul class="space-y-2">
                        @foreach($data['items'] as $rec)
                            <li class="flex items-start gap-2 text-sm">
                                @if($rec['type'] === 'warning')
                                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 flex-shrink-0 text-amber-500" />
                                @elseif($rec['type'] === 'success')
                                    <x-heroicon-o-check-circle class="h-5 w-5 flex-shrink-0 text-green-500" />
                                @elseif($rec['type'] === 'info')
                                    <x-heroicon-o-information-circle class="h-5 w-5 flex-shrink-0 text-blue-500" />
                                @else
                                    <x-heroicon-o-light-bulb class="h-5 w-5 flex-shrink-0 text-gray-400" />
                                @endif
                                <span class="text-gray-600 dark:text-gray-300">{{ $rec['message'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('livewire:initialized', function() {
    initSiteComparisonChart();

    Livewire.on('chartDataUpdated', () => {
        initSiteComparisonChart();
    });
});

function initSiteComparisonChart() {
    const chartData = @json($chartData);
    const chartEl = document.querySelector('#site-comparison-chart');

    if (!chartEl || !chartData.categories || chartData.categories.length === 0) return;

    // Destroy existing chart if any
    if (window.siteComparisonChart) {
        window.siteComparisonChart.destroy();
    }

    const options = {
        series: chartData.series,
        chart: {
            type: 'bar',
            height: 400,
            stacked: true,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            },
            fontFamily: 'Inter, sans-serif',
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '60%',
                borderRadius: 4,
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: chartData.categories,
            labels: {
                style: {
                    colors: '#6B7280',
                    fontSize: '12px'
                },
                rotate: -45,
                rotateAlways: chartData.categories.length > 5
            }
        },
        yaxis: {
            title: {
                text: '{{ __("carbex.sites.comparison.emissions_unit") }}',
                style: {
                    color: '#6B7280',
                    fontSize: '12px'
                }
            },
            labels: {
                style: {
                    colors: '#6B7280',
                    fontSize: '12px'
                },
                formatter: function(val) {
                    return val.toFixed(2);
                }
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val.toFixed(2) + ' t CO₂e';
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            labels: {
                colors: '#6B7280'
            }
        },
        colors: ['#10B981', '#3B82F6', '#8B5CF6'],
        grid: {
            borderColor: '#E5E7EB',
            strokeDashArray: 4
        }
    };

    window.siteComparisonChart = new ApexCharts(chartEl, options);
    window.siteComparisonChart.render();
}
</script>
@endpush

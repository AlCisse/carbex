<div>
    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('carbex.dashboard.emissions_by_site') }}
            </h3>
        </x-slot>

        @if(count($this->siteData) > 0)
            {{-- Bar Chart --}}
            <div
                x-data="{
                    chart: null,
                    init() {
                        const isDark = document.documentElement.classList.contains('dark');
                        const sites = {{ json_encode($this->siteData) }};

                        this.chart = new ApexCharts(this.$refs.chart, {
                            series: [{
                                name: '{{ __('carbex.dashboard.emissions') }}',
                                data: sites.map(s => s.value)
                            }],
                            chart: {
                                type: 'bar',
                                height: 300,
                                fontFamily: 'Inter, sans-serif',
                                toolbar: {
                                    show: false
                                }
                            },
                            colors: ['#10B981'],
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '60%',
                                    borderRadius: 4
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            xaxis: {
                                categories: sites.map(s => s.name),
                                labels: {
                                    style: {
                                        colors: isDark ? '#9CA3AF' : '#6B7280',
                                        fontSize: '12px'
                                    },
                                    rotate: -45,
                                    rotateAlways: sites.length > 4
                                }
                            },
                            yaxis: {
                                labels: {
                                    style: {
                                        colors: isDark ? '#9CA3AF' : '#6B7280'
                                    },
                                    formatter: function(val) {
                                        return val.toFixed(1) + ' t';
                                    }
                                }
                            },
                            grid: {
                                borderColor: isDark ? '#374151' : '#E5E7EB',
                                strokeDashArray: 4
                            },
                            tooltip: {
                                theme: isDark ? 'dark' : 'light',
                                y: {
                                    formatter: function(val) {
                                        return val.toFixed(2) + ' tonnes CO₂e';
                                    }
                                }
                            }
                        });
                        this.chart.render();
                    },
                    destroy() {
                        if (this.chart) {
                            this.chart.destroy();
                        }
                    }
                }"
                x-init="init()"
                x-on:destroy="destroy()"
                wire:key="sites-chart-{{ $this->startDate }}-{{ $this->endDate }}"
            >
                <div x-ref="chart"></div>
            </div>

            {{-- Site List --}}
            <div class="mt-6 pt-6 border-t dark:border-gray-700">
                <div class="space-y-3">
                    @foreach($this->siteData as $index => $site)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center min-w-0">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium flex items-center justify-center mr-3">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <span class="font-medium text-gray-900 dark:text-white truncate block">
                                        {{ $site['name'] }}
                                    </span>
                                    @if($site['city'])
                                        <span class="text-xs text-gray-500">{{ $site['city'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 ml-4">
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($site['value'], 2) }} t
                                </span>
                                <span class="block text-xs text-gray-500">
                                    {{ $this->total > 0 ? number_format(($site['value'] / $this->total) * 100, 1) : 0 }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <x-heroicon-o-building-office-2 class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('carbex.dashboard.no_site_data') }}
                </h4>
                <p class="text-gray-500">
                    {{ __('carbex.dashboard.add_sites_prompt') }}
                </p>
            </div>
        @endif
    </x-card>
</div>

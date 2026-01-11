<div>
    <x-card>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('carbex.dashboard.emission_trends') }}
                </h3>
                <div class="flex items-center space-x-2">
                    {{-- Chart Type Toggle --}}
                    <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 p-1">
                        <button
                            wire:click="setChartType('area')"
                            class="px-3 py-1 text-sm rounded-md transition
                                {{ $chartType === 'area' ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700' }}"
                        >
                            <x-heroicon-s-chart-bar-square class="w-4 h-4" />
                        </button>
                        <button
                            wire:click="setChartType('line')"
                            class="px-3 py-1 text-sm rounded-md transition
                                {{ $chartType === 'line' ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700' }}"
                        >
                            <x-heroicon-s-presentation-chart-line class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </x-slot>

        @if($this->hasData)
            <div
                x-data="{
                    chart: null,
                    chartType: @entangle('chartType'),
                    init() {
                        this.renderChart();
                        this.$watch('chartType', () => {
                            this.renderChart();
                        });
                    },
                    renderChart() {
                        if (this.chart) {
                            this.chart.destroy();
                        }

                        const isDark = document.documentElement.classList.contains('dark');

                        this.chart = new ApexCharts(this.$refs.chart, {
                            series: {{ json_encode($this->trendData['series']) }},
                            chart: {
                                type: this.chartType,
                                height: 350,
                                stacked: this.chartType === 'area',
                                fontFamily: 'Inter, sans-serif',
                                toolbar: {
                                    show: true,
                                    tools: {
                                        download: true,
                                        selection: false,
                                        zoom: true,
                                        zoomin: true,
                                        zoomout: true,
                                        pan: false,
                                        reset: true
                                    }
                                },
                                background: 'transparent'
                            },
                            colors: ['#10B981', '#3B82F6', '#8B5CF6'],
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                                width: this.chartType === 'area' ? 2 : 3
                            },
                            fill: {
                                type: this.chartType === 'area' ? 'gradient' : 'solid',
                                gradient: {
                                    opacityFrom: 0.5,
                                    opacityTo: 0.1,
                                }
                            },
                            xaxis: {
                                categories: {{ json_encode($this->trendData['categories']) }},
                                labels: {
                                    style: {
                                        colors: isDark ? '#9CA3AF' : '#6B7280',
                                        fontSize: '12px'
                                    }
                                },
                                axisBorder: {
                                    show: false
                                },
                                axisTicks: {
                                    show: false
                                }
                            },
                            yaxis: {
                                labels: {
                                    style: {
                                        colors: isDark ? '#9CA3AF' : '#6B7280',
                                        fontSize: '12px'
                                    },
                                    formatter: function(val) {
                                        return val.toFixed(1) + ' t';
                                    }
                                }
                            },
                            grid: {
                                borderColor: isDark ? '#374151' : '#E5E7EB',
                                strokeDashArray: 4,
                                padding: {
                                    left: 10,
                                    right: 10
                                }
                            },
                            legend: {
                                position: 'top',
                                horizontalAlign: 'right',
                                labels: {
                                    colors: isDark ? '#D1D5DB' : '#374151'
                                },
                                markers: {
                                    radius: 12
                                }
                            },
                            tooltip: {
                                theme: isDark ? 'dark' : 'light',
                                shared: true,
                                intersect: false,
                                y: {
                                    formatter: function(val) {
                                        return val.toFixed(2) + ' tonnes CO₂e';
                                    }
                                }
                            },
                            responsive: [{
                                breakpoint: 640,
                                options: {
                                    chart: {
                                        height: 280
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }]
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
                wire:key="trend-chart-{{ $this->siteId }}-{{ $this->startDate }}-{{ $this->endDate }}"
            >
                <div x-ref="chart"></div>
            </div>

            {{-- Summary Stats --}}
            <div class="mt-6 pt-6 border-t dark:border-gray-700">
                <div class="grid grid-cols-3 gap-4 text-center">
                    @php
                        $scopeTotals = collect($this->trendData['series'])->map(fn($s) => [
                            'name' => $s['name'],
                            'total' => array_sum($s['data']),
                            'color' => $s['color'],
                        ]);
                    @endphp
                    @foreach($scopeTotals as $scope)
                        <div>
                            <div class="inline-flex items-center">
                                <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $scope['color'] }}"></div>
                                <span class="text-sm text-gray-500">{{ $scope['name'] }}</span>
                            </div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ number_format($scope['total'], 1) }} t
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <x-heroicon-o-chart-bar class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('carbex.dashboard.no_trend_data') }}
                </h4>
                <p class="text-gray-500">
                    {{ __('carbex.dashboard.trend_data_hint') }}
                </p>
            </div>
        @endif
    </x-card>
</div>

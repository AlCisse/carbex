<div>
    <x-card>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('carbex.dashboard.emissions_by_scope') }}
                </h3>
                <span class="text-sm text-gray-500">
                    {{ number_format($this->total, 1) }} t CO₂e
                </span>
            </div>
        </x-slot>

        @if(count($this->breakdown) > 0)
            <div class="flex flex-col lg:flex-row items-center gap-8">
                {{-- Donut Chart --}}
                <div class="w-full lg:w-1/2">
                    <div
                        x-data="{
                            chart: null,
                            init() {
                                this.chart = new ApexCharts(this.$refs.chart, {
                                    series: {{ json_encode($this->chartData['series']) }},
                                    labels: {{ json_encode($this->chartData['labels']) }},
                                    colors: {{ json_encode($this->chartData['colors']) }},
                                    chart: {
                                        type: 'donut',
                                        height: 280,
                                        fontFamily: 'Inter, sans-serif',
                                    },
                                    plotOptions: {
                                        pie: {
                                            donut: {
                                                size: '65%',
                                                labels: {
                                                    show: true,
                                                    name: {
                                                        show: true,
                                                        fontSize: '14px',
                                                        color: '#6B7280',
                                                    },
                                                    value: {
                                                        show: true,
                                                        fontSize: '24px',
                                                        fontWeight: 'bold',
                                                        color: '#111827',
                                                        formatter: function(val) {
                                                            return parseFloat(val).toFixed(1) + ' t';
                                                        }
                                                    },
                                                    total: {
                                                        show: true,
                                                        label: '{{ __('carbex.dashboard.total') }}',
                                                        fontSize: '14px',
                                                        color: '#6B7280',
                                                        formatter: function(w) {
                                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toFixed(1) + ' t';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    legend: {
                                        show: false
                                    },
                                    stroke: {
                                        width: 2,
                                        colors: ['#fff']
                                    },
                                    tooltip: {
                                        y: {
                                            formatter: function(val) {
                                                return val.toFixed(2) + ' tonnes CO₂e';
                                            }
                                        }
                                    },
                                    responsive: [{
                                        breakpoint: 480,
                                        options: {
                                            chart: {
                                                height: 240
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
                        wire:key="scope-chart-{{ $this->siteId }}-{{ $this->startDate }}-{{ $this->endDate }}"
                    >
                        <div x-ref="chart"></div>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="w-full lg:w-1/2 space-y-4">
                    @foreach($this->breakdown as $scope)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $scope['color'] }}"></div>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $scope['label'] }}</span>
                                    <span class="block text-xs text-gray-500">{{ $scope['count'] }} {{ __('carbex.dashboard.records') }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($scope['value'], 2) }} t</span>
                                <span class="block text-xs text-gray-500">{{ $scope['percent'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Scope Descriptions --}}
            <div class="mt-6 pt-6 border-t dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <div>
                        <span class="font-medium text-green-600 dark:text-green-400">Scope 1:</span>
                        {{ __('carbex.dashboard.scope1_desc') }}
                    </div>
                    <div>
                        <span class="font-medium text-blue-600 dark:text-blue-400">Scope 2:</span>
                        {{ __('carbex.dashboard.scope2_desc') }}
                    </div>
                    <div>
                        <span class="font-medium text-purple-600 dark:text-purple-400">Scope 3:</span>
                        {{ __('carbex.dashboard.scope3_desc') }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <x-heroicon-o-chart-pie class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('carbex.dashboard.no_data') }}
                </h4>
                <p class="text-gray-500">
                    {{ __('carbex.dashboard.connect_bank_prompt') }}
                </p>
            </div>
        @endif
    </x-card>
</div>

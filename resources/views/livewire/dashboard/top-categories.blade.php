<div>
    <x-card>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('carbex.dashboard.top_categories') }}
                </h3>
                <div class="flex items-center space-x-2">
                    {{-- View Mode Toggle --}}
                    <div class="inline-flex rounded-lg border border-gray-200 dark:border-gray-700 p-1">
                        <button
                            wire:click="setViewMode('treemap')"
                            class="px-3 py-1 text-sm rounded-md transition
                                {{ $viewMode === 'treemap' ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700' }}"
                            title="{{ __('carbex.dashboard.treemap') }}"
                        >
                            <x-heroicon-s-squares-2x2 class="w-4 h-4" />
                        </button>
                        <button
                            wire:click="setViewMode('bar')"
                            class="px-3 py-1 text-sm rounded-md transition
                                {{ $viewMode === 'bar' ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700' }}"
                            title="{{ __('carbex.dashboard.bar_chart') }}"
                        >
                            <x-heroicon-s-chart-bar class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </x-slot>

        @if(count($this->categories) > 0)
            @if($viewMode === 'treemap')
                {{-- Treemap View --}}
                <div
                    x-data="{
                        chart: null,
                        init() {
                            const isDark = document.documentElement.classList.contains('dark');

                            this.chart = new ApexCharts(this.$refs.chart, {
                                series: [{
                                    data: {{ json_encode($this->treemapData) }}
                                }],
                                chart: {
                                    type: 'treemap',
                                    height: 350,
                                    fontFamily: 'Inter, sans-serif',
                                    toolbar: {
                                        show: false
                                    }
                                },
                                colors: [
                                    '#10B981', '#3B82F6', '#8B5CF6', '#F59E0B',
                                    '#EF4444', '#EC4899', '#14B8A6', '#6366F1',
                                    '#84CC16', '#06B6D4'
                                ],
                                plotOptions: {
                                    treemap: {
                                        distributed: true,
                                        enableShades: false
                                    }
                                },
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        fontSize: '12px',
                                        fontWeight: 'bold'
                                    },
                                    formatter: function(text, op) {
                                        return [text, op.value.toFixed(1) + ' t'];
                                    },
                                    offsetY: -4
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
                    wire:key="treemap-{{ $this->siteId }}-{{ $this->startDate }}-{{ $this->endDate }}"
                >
                    <div x-ref="chart"></div>
                </div>
            @else
                {{-- Bar Chart View --}}
                <div
                    x-data="{
                        chart: null,
                        init() {
                            const isDark = document.documentElement.classList.contains('dark');
                            const categories = {{ json_encode($this->categories) }};

                            this.chart = new ApexCharts(this.$refs.chart, {
                                series: [{
                                    name: '{{ __('carbex.emissions.title') }}',
                                    data: categories.map(c => c.value)
                                }],
                                chart: {
                                    type: 'bar',
                                    height: 350,
                                    fontFamily: 'Inter, sans-serif',
                                    toolbar: {
                                        show: false
                                    }
                                },
                                colors: categories.map(c => c.color),
                                plotOptions: {
                                    bar: {
                                        horizontal: true,
                                        distributed: true,
                                        borderRadius: 4,
                                        barHeight: '70%'
                                    }
                                },
                                dataLabels: {
                                    enabled: true,
                                    textAnchor: 'start',
                                    formatter: function(val) {
                                        return val.toFixed(1) + ' t';
                                    },
                                    offsetX: 5,
                                    style: {
                                        colors: ['#fff']
                                    }
                                },
                                xaxis: {
                                    categories: categories.map(c => c.name),
                                    labels: {
                                        style: {
                                            colors: isDark ? '#9CA3AF' : '#6B7280'
                                        },
                                        formatter: function(val) {
                                            return val.toFixed(1) + ' t';
                                        }
                                    }
                                },
                                yaxis: {
                                    labels: {
                                        style: {
                                            colors: isDark ? '#9CA3AF' : '#6B7280'
                                        }
                                    }
                                },
                                grid: {
                                    borderColor: isDark ? '#374151' : '#E5E7EB',
                                    xaxis: {
                                        lines: {
                                            show: true
                                        }
                                    },
                                    yaxis: {
                                        lines: {
                                            show: false
                                        }
                                    }
                                },
                                legend: {
                                    show: false
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
                    wire:key="bar-{{ $this->siteId }}-{{ $this->startDate }}-{{ $this->endDate }}"
                >
                    <div x-ref="chart"></div>
                </div>
            @endif

            {{-- Category List --}}
            <div class="mt-6 pt-6 border-t dark:border-gray-700">
                <div class="space-y-2">
                    @foreach($this->categories as $index => $category)
                        <div class="flex items-center justify-between py-2 {{ $index < count($this->categories) - 1 ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
                            <div class="flex items-center min-w-0">
                                <div class="w-3 h-3 rounded-full mr-3 flex-shrink-0" style="background-color: {{ $category['color'] }}"></div>
                                <div class="min-w-0">
                                    <span class="font-medium text-gray-900 dark:text-white truncate block">
                                        {{ $category['name'] }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Scope {{ $category['scope'] }} &bull; {{ $category['count'] }} {{ __('carbex.dashboard.transactions') }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 ml-4">
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($category['value'], 2) }} t
                                </span>
                                <span class="block text-xs text-gray-500">
                                    {{ $this->total > 0 ? number_format(($category['value'] / $this->total) * 100, 1) : 0 }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <x-heroicon-o-rectangle-group class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('carbex.dashboard.no_category_data') }}
                </h4>
                <p class="text-gray-500">
                    {{ __('carbex.dashboard.category_data_hint') }}
                </p>
            </div>
        @endif
    </x-card>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ __('linscarbon.trajectory.chart_title') }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ __('linscarbon.trajectory.chart_subtitle') }}</p>
            </div>

            @if($this->targets->count() > 1)
                <div>
                    <label for="target-select" class="sr-only">{{ __('linscarbon.trajectory.select_target') }}</label>
                    <select
                        id="target-select"
                        wire:model.live="selectedTargetId"
                        class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                    >
                        @foreach($this->targets as $target)
                            <option value="{{ $target->id }}">
                                {{ $target->baseline_year }} - {{ $target->target_year }}
                                @if($target->is_sbti_aligned) (SBTi) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    @if($this->hasData)
        <!-- Chart -->
        <div class="p-6">
            <div
                x-data="{
                    chart: null,
                    init() {
                        this.renderChart();
                    },
                    renderChart() {
                        if (this.chart) {
                            this.chart.destroy();
                        }

                        const chartData = {{ Js::from($this->chartData) }};

                        this.chart = new ApexCharts(this.$refs.chart, {
                            series: chartData.series,
                            chart: {
                                type: 'line',
                                height: 400,
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
                                background: 'transparent',
                                animations: {
                                    enabled: true,
                                    speed: 800,
                                    animateGradually: {
                                        enabled: true,
                                        delay: 150
                                    }
                                }
                            },
                            colors: chartData.series.map(s => s.color),
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                                width: [3, 3],
                                dashArray: [0, 5]
                            },
                            markers: {
                                size: [5, 0],
                                strokeWidth: 2,
                                hover: {
                                    size: 8
                                }
                            },
                            xaxis: {
                                categories: chartData.categories,
                                title: {
                                    text: '{{ __('linscarbon.trajectory.axis_years') }}',
                                    style: {
                                        fontSize: '12px',
                                        fontWeight: 500,
                                        color: '#6B7280'
                                    }
                                },
                                labels: {
                                    style: {
                                        colors: '#6B7280',
                                        fontSize: '12px'
                                    }
                                },
                                axisBorder: {
                                    show: true,
                                    color: '#E5E7EB'
                                },
                                axisTicks: {
                                    show: true,
                                    color: '#E5E7EB'
                                }
                            },
                            yaxis: {
                                title: {
                                    text: '{{ __('linscarbon.trajectory.axis_emissions') }}',
                                    style: {
                                        fontSize: '12px',
                                        fontWeight: 500,
                                        color: '#6B7280'
                                    }
                                },
                                labels: {
                                    style: {
                                        colors: '#6B7280',
                                        fontSize: '12px'
                                    },
                                    formatter: function(val) {
                                        if (val === null) return '';
                                        return val.toLocaleString('fr-FR', {maximumFractionDigits: 0}) + ' t';
                                    }
                                },
                                min: 0
                            },
                            grid: {
                                borderColor: '#E5E7EB',
                                strokeDashArray: 4,
                                padding: {
                                    left: 10,
                                    right: 10
                                }
                            },
                            legend: {
                                position: 'top',
                                horizontalAlign: 'center',
                                labels: {
                                    colors: '#374151'
                                },
                                markers: {
                                    radius: 12
                                }
                            },
                            tooltip: {
                                theme: 'light',
                                shared: true,
                                intersect: false,
                                y: {
                                    formatter: function(val) {
                                        if (val === null) return '{{ __('linscarbon.trajectory.no_data') }}';
                                        return val.toLocaleString('fr-FR', {maximumFractionDigits: 1}) + ' tCO\u2082e';
                                    }
                                }
                            },
                            annotations: {
                                xaxis: [{
                                    x: {{ (int) date('Y') }},
                                    borderColor: '#9CA3AF',
                                    borderWidth: 2,
                                    label: {
                                        text: '{{ __('linscarbon.trajectory.today') }}',
                                        style: {
                                            color: '#fff',
                                            background: '#6B7280'
                                        }
                                    }
                                }]
                            },
                            responsive: [{
                                breakpoint: 640,
                                options: {
                                    chart: {
                                        height: 300
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
                wire:key="trajectory-chart-{{ $selectedTargetId }}"
            >
                <div x-ref="chart"></div>
            </div>
        </div>

        <!-- Summary Stats -->
        @if($this->summary)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Baseline -->
                    <div class="text-center">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            {{ __('linscarbon.trajectory.baseline') }}
                        </p>
                        <p class="mt-1 text-xl font-semibold text-gray-900">
                            {{ number_format($this->summary['baseline_total'], 0, ',', ' ') }} t
                        </p>
                    </div>

                    <!-- Target -->
                    <div class="text-center">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            {{ __('linscarbon.trajectory.target') }} {{ $this->selectedTarget->target_year }}
                        </p>
                        <p class="mt-1 text-xl font-semibold text-green-600">
                            {{ number_format($this->summary['target_total'], 0, ',', ' ') }} t
                        </p>
                    </div>

                    <!-- Reduction -->
                    <div class="text-center">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            {{ __('linscarbon.trajectory.reduction') }}
                        </p>
                        <p class="mt-1 text-xl font-semibold text-blue-600">
                            -{{ number_format($this->summary['reduction_total'], 0, ',', ' ') }} t
                        </p>
                    </div>

                    <!-- Status -->
                    <div class="text-center">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            {{ __('linscarbon.trajectory.status') }}
                        </p>
                        @if($this->summary['current_emissions'] !== null)
                            @if($this->summary['on_track'])
                                <p class="mt-1 text-xl font-semibold text-green-600 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('linscarbon.trajectory.on_track') }}
                                </p>
                            @else
                                <p class="mt-1 text-xl font-semibold text-red-600 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('linscarbon.trajectory.off_track') }}
                                </p>
                            @endif
                        @else
                            <p class="mt-1 text-lg font-medium text-gray-400">
                                {{ __('linscarbon.trajectory.years_left', ['years' => $this->summary['years_remaining']]) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Legend -->
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-center space-x-6 text-sm">
                <div class="flex items-center">
                    <span class="w-8 h-0.5 bg-green-600 mr-2"></span>
                    <span class="text-gray-600">{{ __('linscarbon.trajectory.actual_emissions') }}</span>
                </div>
                <div class="flex items-center">
                    <span class="w-8 h-0.5 bg-red-600 mr-2" style="background: repeating-linear-gradient(to right, #DC2626 0, #DC2626 4px, transparent 4px, transparent 8px);"></span>
                    <span class="text-gray-600">{{ __('linscarbon.trajectory.target_trajectory') }}</span>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('linscarbon.trajectory.empty') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('linscarbon.trajectory.empty_description') }}</p>
            <div class="mt-6">
                <a href="{{ route('transition-plan.trajectory') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('linscarbon.trajectory.create_target') }}
                </a>
            </div>
        </div>
    @endif
</div>

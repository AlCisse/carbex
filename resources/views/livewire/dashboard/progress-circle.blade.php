<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('carbex.dashboard.progress_title') }}</h3>

    <div class="flex items-center justify-center mb-6">
        <!-- SVG Circle Progress -->
        <div class="relative w-40 h-40">
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                <!-- Background circle -->
                <circle
                    cx="50"
                    cy="50"
                    r="45"
                    fill="none"
                    stroke="#e5e7eb"
                    stroke-width="8"
                />
                <!-- Progress circle -->
                <circle
                    cx="50"
                    cy="50"
                    r="45"
                    fill="none"
                    stroke="#22c55e"
                    stroke-width="8"
                    stroke-linecap="round"
                    stroke-dasharray="{{ 2 * 3.14159 * 45 }}"
                    stroke-dashoffset="{{ 2 * 3.14159 * 45 * (1 - $progress['percentage'] / 100) }}"
                    class="transition-all duration-1000 ease-out"
                />
            </svg>
            <!-- Center text -->
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-3xl font-bold text-gray-900">{{ $progress['percentage'] }}%</span>
                <span class="text-sm text-gray-500">{{ __('carbex.dashboard.completed') }}</span>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="text-center mb-6">
        <p class="text-sm text-gray-600">
            <span class="font-semibold text-gray-900">{{ $progress['completed'] }}</span>
            {{ __('carbex.dashboard.of') }}
            <span class="font-semibold text-gray-900">{{ $progress['applicable_total'] }}</span>
            {{ __('carbex.dashboard.categories') }}
        </p>
    </div>

    <!-- Legend -->
    <div class="flex justify-center space-x-6 text-sm">
        <div class="flex items-center">
            <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
            <span class="text-gray-600">{{ __('carbex.dashboard.legend_completed') }} ({{ $progress['completed'] }})</span>
        </div>
        <div class="flex items-center">
            <span class="w-3 h-3 rounded-full bg-yellow-400 mr-2"></span>
            <span class="text-gray-600">{{ __('carbex.dashboard.legend_todo') }} ({{ $progress['in_progress'] }})</span>
        </div>
        @if($progress['not_applicable'] > 0)
        <div class="flex items-center">
            <span class="w-3 h-3 rounded-full bg-gray-300 mr-2"></span>
            <span class="text-gray-600">{{ __('carbex.dashboard.legend_na') }} ({{ $progress['not_applicable'] }})</span>
        </div>
        @endif
    </div>

    <!-- Scope Progress Bars -->
    <div class="mt-6 space-y-3">
        @foreach($scopeProgress as $scope => $data)
            <div>
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700">Scope {{ $scope }}</span>
                    <span class="text-gray-500">{{ $data['completed'] }}/{{ $data['total'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500 {{ match($scope) {
                        1 => 'bg-orange-500',
                        2 => 'bg-yellow-500',
                        3 => 'bg-blue-500',
                    } }}" style="width: {{ $data['percentage'] }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" dusk="carbon-equivalents">
    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('carbex.dashboard.equivalents_title') }}</h3>
    <p class="text-sm text-gray-500 mb-6">{{ __('carbex.dashboard.equivalents_subtitle') }}</p>

    @if($totalKg > 0)
        <div class="grid grid-cols-2 gap-4">
            @foreach($equivalents as $equivalent)
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <!-- Icon -->
                    <div class="mb-2">
                        @switch($equivalent['icon'])
                            @case('airplane')
                                <svg class="w-8 h-8 mx-auto text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                @break
                            @case('globe')
                                <svg class="w-8 h-8 mx-auto text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @break
                            @case('building')
                                <svg class="w-8 h-8 mx-auto text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                @break
                            @case('car')
                                <svg class="w-8 h-8 mx-auto text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-8 5h8m-4-9a9 9 0 110 18 9 9 0 010-18z" />
                                </svg>
                                @break
                            @case('user')
                                <svg class="w-8 h-8 mx-auto text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                @break
                            @case('tree')
                                <svg class="w-8 h-8 mx-auto text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                @break
                            @case('play')
                                <svg class="w-8 h-8 mx-auto text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @break
                            @default
                                <svg class="w-8 h-8 mx-auto text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                        @endswitch
                    </div>

                    <!-- Value -->
                    <p class="text-2xl font-bold text-gray-900">
                        @if($equivalent['value'] >= 1000)
                            {{ number_format($equivalent['value'] / 1000, 1, ',', ' ') }}K
                        @else
                            {{ number_format($equivalent['value'], $equivalent['value'] < 10 ? 1 : 0, ',', ' ') }}
                        @endif
                    </p>

                    <!-- Label -->
                    <p class="text-xs text-gray-500 mt-1">
                        {{ __($equivalent['label']) }}
                    </p>
                </div>
            @endforeach
        </div>

        <!-- Total emissions reminder -->
        <div class="mt-4 pt-4 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-500">
                {{ __('carbex.dashboard.total_emissions') }}:
                <span class="font-semibold text-gray-900">
                    @if($totalKg >= 1000)
                        {{ number_format($totalKg / 1000, 2, ',', ' ') }} {{ __('carbex.units.tonnes') }}
                    @else
                        {{ number_format($totalKg, 0, ',', ' ') }} kg
                    @endif
                    CO₂e
                </span>
            </p>
        </div>
    @else
        <!-- Empty state -->
        <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p class="text-gray-500">{{ __('carbex.dashboard.no_emissions') }}</p>
        </div>
    @endif
</div>

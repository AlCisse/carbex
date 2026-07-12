<div x-data="{ open: false }" class="relative" dusk="year-selector">
    {{-- Trigger Button --}}
    <button
        @click="open = !open"
        type="button"
        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
    >
        <x-heroicon-o-calendar class="w-5 h-5 mr-2 text-gray-400" />
        <span>{{ $this->currentLabel }}</span>
        <x-heroicon-s-chevron-down class="w-4 h-4 ml-2 text-gray-400" />
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-72 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        style="display: none;"
    >
        <div class="p-2">
            {{-- Preset Options --}}
            <div class="space-y-1">
                @foreach(['ytd', 'last_month', 'last_quarter'] as $presetKey)
                    <button
                        wire:click="applyPreset('{{ $presetKey }}')"
                        @click="open = false"
                        class="w-full text-left px-3 py-2 text-sm rounded-md transition
                            {{ $preset === $presetKey ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    >
                        {{ $this->presets[$presetKey]['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Quarterly Options --}}
            <div class="border-t dark:border-gray-700 my-2 pt-2">
                <p class="px-3 py-1 text-xs font-semibold text-gray-400 uppercase">{{ __('linscarbon.dashboard.quarters') }}</p>
                <div class="grid grid-cols-2 gap-1">
                    @foreach(['q1', 'q2', 'q3', 'q4'] as $quarter)
                        <button
                            wire:click="applyPreset('{{ $quarter }}')"
                            @click="open = false"
                            class="text-left px-3 py-2 text-sm rounded-md transition
                                {{ $preset === $quarter ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        >
                            {{ $this->presets[$quarter]['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Year Options --}}
            <div class="border-t dark:border-gray-700 my-2 pt-2">
                @foreach(['last_year', 'last_12_months'] as $presetKey)
                    <button
                        wire:click="applyPreset('{{ $presetKey }}')"
                        @click="open = false"
                        class="w-full text-left px-3 py-2 text-sm rounded-md transition
                            {{ $preset === $presetKey ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    >
                        {{ $this->presets[$presetKey]['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Custom Range --}}
            <div class="border-t dark:border-gray-700 my-2 pt-2">
                <button
                    wire:click="applyPreset('custom')"
                    class="w-full text-left px-3 py-2 text-sm rounded-md transition
                        {{ $preset === 'custom' ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                >
                    {{ __('linscarbon.dashboard.custom_range') }}
                </button>

                @if($showCustom)
                    <div class="mt-3 px-3 space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('linscarbon.dashboard.start_date') }}</label>
                            <input
                                type="date"
                                wire:model="startDate"
                                class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:ring-green-500 focus:border-green-500"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('linscarbon.dashboard.end_date') }}</label>
                            <input
                                type="date"
                                wire:model="endDate"
                                class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:ring-green-500 focus:border-green-500"
                            >
                        </div>
                        <button
                            wire:click="applyCustomDates"
                            @click="open = false"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition"
                        >
                            {{ __('linscarbon.dashboard.apply') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

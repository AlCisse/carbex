<div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
        {{ __('carbex.settings.navigation_mode') }}
    </h4>

    <div class="space-y-2">
        {{-- Standard Mode --}}
        <label class="flex items-start p-3 rounded-lg border cursor-pointer transition-colors
            {{ $mode === 'standard' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <input type="radio" wire:model.live="mode" wire:click="setMode('standard')" value="standard"
                   class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
            <div class="ml-3">
                <span class="block text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('carbex.settings.nav_standard') }}
                </span>
                <span class="block text-xs text-gray-500 dark:text-gray-400">
                    {{ __('carbex.settings.nav_standard_desc') }}
                </span>
            </div>
        </label>

        {{-- 5 Pillars Mode --}}
        <label class="flex items-start p-3 rounded-lg border cursor-pointer transition-colors
            {{ $mode === 'pillars' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            <input type="radio" wire:model.live="mode" wire:click="setMode('pillars')" value="pillars"
                   class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
            <div class="ml-3">
                <span class="block text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('carbex.settings.nav_pillars') }}
                </span>
                <span class="block text-xs text-gray-500 dark:text-gray-400">
                    {{ __('carbex.settings.nav_pillars_desc') }}
                </span>
                <div class="mt-2 flex flex-wrap gap-1">
                    @foreach(['measure' => 'emerald', 'plan' => 'blue', 'engage' => 'amber', 'report' => 'purple', 'promote' => 'pink'] as $pillar => $color)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-300">
                            {{ __('carbex.pillars.' . $pillar) }}
                        </span>
                    @endforeach
                </div>
            </div>
        </label>
    </div>
</div>

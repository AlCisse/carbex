<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('EU Taxonomy Report') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Article 8 Disclosure - Regulation (EU) 2020/852') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <select wire:model.live="selectedYear" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                    @for($year = now()->year; $year >= now()->year - 4; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
                <a href="{{ route('csrd.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    &larr; {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Turnover') }}</h4>
            <div class="mt-2 flex items-baseline">
                <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($this->turnoverAlignedPercent, 1) }}%</span>
                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ __('aligned') }}</span>
            </div>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ number_format($this->turnoverEligiblePercent, 1) }}% {{ __('eligible') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('CapEx') }}</h4>
            <div class="mt-2 flex items-baseline">
                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($this->capexAlignedPercent, 1) }}%</span>
                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ __('aligned') }}</span>
            </div>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ number_format($this->capexEligiblePercent, 1) }}% {{ __('eligible') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('OpEx') }}</h4>
            <div class="mt-2 flex items-baseline">
                <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($this->opexAlignedPercent, 1) }}%</span>
                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ __('aligned') }}</span>
            </div>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ number_format($this->opexEligiblePercent, 1) }}% {{ __('eligible') }}</div>
        </div>
    </div>

    <!-- Compliance Badges -->
    <div class="mb-6 flex items-center space-x-4">
        @if($this->minimumSafeguardsMet)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ __('Minimum Safeguards Met') }}
            </span>
        @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                {{ __('Minimum Safeguards Not Met') }}
            </span>
        @endif
        @if($this->dnshMet)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ __('DNSH Criteria Met') }}
            </span>
        @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                {{ __('DNSH Incomplete') }}
            </span>
        @endif
    </div>

    <!-- Section Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 overflow-x-auto">
            @foreach(['kpis' => 'KPIs', 'objectives' => 'Environmental Objectives', 'dnsh' => 'DNSH', 'safeguards' => 'Minimum Safeguards', 'activities' => 'Activities'] as $section => $label)
                <button wire:click="setSection('{{ $section }}')" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm {{ $activeSection === $section ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                    {{ __($label) }}
                </button>
            @endforeach
        </nav>
    </div>

    <form wire:submit="save">
        <!-- KPIs Section -->
        @if($activeSection === 'kpis')
            <div class="space-y-6">
                <!-- Turnover -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Turnover KPIs') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Total Turnover') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model.live="turnoverTotal" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EUR</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Eligible Turnover') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model.live="turnoverEligible" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EUR</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Aligned Turnover') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model.live="turnoverAligned" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EUR</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CapEx -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('CapEx KPIs') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Total CapEx') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model.live="capexTotal" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EUR</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Eligible CapEx') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model.live="capexEligible" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EUR</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Aligned CapEx') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model.live="capexAligned" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EUR</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OpEx -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('OpEx KPIs') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Total OpEx') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model.live="opexTotal" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EUR</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Eligible OpEx') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model.live="opexEligible" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EUR</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Aligned OpEx') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model.live="opexAligned" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EUR</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Environmental Objectives Section -->
        @if($activeSection === 'objectives')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Substantial Contribution to Environmental Objectives') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($this->environmentalObjectives as $key => $objective)
                        <label class="flex items-start p-4 rounded-lg border {{ ${'contributes' . ucfirst(str_replace('_', '', $key))} ?? false ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600' }} cursor-pointer">
                            <input type="checkbox" wire:model="contributes{{ ucfirst(str_replace('_', '', ucwords($key, '_'))) }}" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded mt-1">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $objective['name'] }}</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $objective['regulation'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- DNSH Section -->
        @if($activeSection === 'dnsh')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Do No Significant Harm (DNSH) Assessment') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Confirm that activities do no significant harm to the following environmental objectives:') }}</p>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="dnshClimateMitigation" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('Climate change mitigation') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="dnshClimateAdaptation" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('Climate change adaptation') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="dnshWater" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('Sustainable use of water and marine resources') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="dnshCircularEconomy" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('Transition to a circular economy') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="dnshPollution" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('Pollution prevention and control') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="dnshBiodiversity" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('Protection of biodiversity and ecosystems') }}</span>
                    </label>
                </div>
            </div>
        @endif

        <!-- Minimum Safeguards Section -->
        @if($activeSection === 'safeguards')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Minimum Safeguards Compliance') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Confirm compliance with the following international standards:') }}</p>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="oecdGuidelinesCompliant" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('OECD Guidelines for Multinational Enterprises') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="unGuidingPrinciplesCompliant" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('UN Guiding Principles on Business and Human Rights') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="iloConventionsCompliant" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('ILO Core Labour Conventions') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="humanRightsDeclarationCompliant" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('International Bill of Human Rights') }}</span>
                    </label>
                </div>
            </div>
        @endif

        <!-- Activities Section -->
        @if($activeSection === 'activities')
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Taxonomy-Eligible Activities') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($this->commonActivities as $code => $name)
                            <label class="flex items-center p-2 rounded {{ in_array($code, $eligibleActivities) ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                <input type="checkbox" wire:click="toggleActivity('eligible', '{{ $code }}')" {{ in_array($code, $eligibleActivities) ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">{{ $code }}</span> - {{ $name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Taxonomy-Aligned Activities') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($this->commonActivities as $code => $name)
                            <label class="flex items-center p-2 rounded {{ in_array($code, $alignedActivities) ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                <input type="checkbox" wire:click="toggleActivity('aligned', '{{ $code }}')" {{ in_array($code, $alignedActivities) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">{{ $code }}</span> - {{ $name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                {{ __('Save Report') }}
            </button>
            @if($this->report)
                <button type="button" wire:click="verify" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('Mark as Verified') }}
                </button>
            @endif
        </div>
    </form>
</div>

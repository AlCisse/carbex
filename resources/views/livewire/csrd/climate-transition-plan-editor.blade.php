<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Climate Transition Plan') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ESRS E1-1 - Paris-aligned transition planning') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <select wire:model.live="selectedYear" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                    @for($year = now()->year + 1; $year >= now()->year - 3; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
                <a href="{{ route('csrd.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    &larr; {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Status and SBTi Badge -->
    <div class="mb-6 flex items-center space-x-4">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $status === 'approved' || $status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
            {{ $this->statuses[$status] ?? ucfirst($status) }}
        </span>
        @if($this->isSbtiCompliant)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ __('SBTi Compliant') }}
            </span>
        @endif
    </div>

    <!-- Section Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 overflow-x-auto">
            @foreach(['targets' => 'Targets', 'emissions' => 'Base Year', 'levers' => 'Decarbonization', 'finance' => 'Financial', 'governance' => 'Governance', 'risks' => 'Risks'] as $section => $label)
                <button wire:click="setSection('{{ $section }}')" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm {{ $activeSection === $section ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                    {{ __($label) }}
                </button>
            @endforeach
        </nav>
    </div>

    <form wire:submit="save">
        <!-- Targets Section -->
        @if($activeSection === 'targets')
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Temperature Target') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($this->temperatureTargets as $key => $target)
                            <label class="relative flex cursor-pointer rounded-lg border p-4 {{ $temperatureTarget === $key ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                                <input type="radio" wire:model="temperatureTarget" value="{{ $key }}" class="sr-only">
                                <div class="flex flex-col">
                                    <span class="text-lg font-medium text-gray-900 dark:text-white">{{ $target['name'] }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $target['sbti_pathway'] }}</span>
                                    <span class="mt-2 text-sm text-green-600 dark:text-green-400">{{ $target['annual_reduction_rate'] }}% {{ __('annual reduction') }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('SBTi Commitment') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="isSbtiCommitted" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('SBTi Committed') }}</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="isSbtiValidated" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('SBTi Validated') }}</label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Commitment Date') }}</label>
                            <input type="date" wire:model="sbtiCommitmentDate" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Validation Date') }}</label>
                            <input type="date" wire:model="sbtiValidationDate" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Net-Zero Target') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Target Year') }}</label>
                            <input type="number" wire:model="netZeroTargetYear" min="2030" max="2100" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Residual Emissions %') }}</label>
                            <input type="number" wire:model="netZeroResidualEmissionsPercent" min="0" max="100" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('SBTi requires max 10%') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Required Annual Reduction') }}</label>
                            <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{{ $this->requiredAnnualReduction ?? '-' }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Interim Targets -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Interim Targets') }}</h3>
                        <button type="button" wire:click="addInterimTarget" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add Target') }}
                        </button>
                    </div>
                    @forelse($interimTargets as $index => $target)
                        <div class="flex items-center space-x-4 mb-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <input type="number" wire:model="interimTargets.{{ $index }}.year" placeholder="{{ __('Year') }}" class="w-24 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                            <input type="number" wire:model="interimTargets.{{ $index }}.reduction_percent" placeholder="{{ __('Reduction %') }}" class="w-24 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                            <input type="text" wire:model="interimTargets.{{ $index }}.description" placeholder="{{ __('Description') }}" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                            <button type="button" wire:click="removeInterimTarget({{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No interim targets defined') }}</p>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- Base Year Section -->
        @if($activeSection === 'emissions')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Base Year Emissions') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Base Year') }}</label>
                        <input type="number" wire:model="baseYear" min="2015" max="{{ now()->year }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Total Emissions') }}</label>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->baseYearEmissionsTotal, 2) }} tCO2e</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Scope 1 Emissions (tCO2e)') }}</label>
                        <input type="number" wire:model="baseYearEmissionsScope1" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Scope 2 Emissions (tCO2e)') }}</label>
                        <input type="number" wire:model="baseYearEmissionsScope2" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Scope 3 Emissions (tCO2e)') }}</label>
                        <input type="number" wire:model="baseYearEmissionsScope3" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>
            </div>
        @endif

        <!-- Decarbonization Levers Section -->
        @if($activeSection === 'levers')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Decarbonization Levers') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($this->availableLevers as $key => $label)
                        <label class="flex items-center p-3 rounded-lg border {{ in_array($key, $decarbonizationLevers) ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600' }} cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                            <input type="checkbox" wire:click="toggleLever('{{ $key }}')" {{ in_array($key, $decarbonizationLevers) ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Financial Section -->
        @if($activeSection === 'finance')
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Climate Investment') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Planned CapEx (Climate)') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model="plannedCapexClimate" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EUR</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Planned OpEx (Climate)') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model="plannedOpexClimate" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">EUR</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Internal Carbon Price') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Price per tCO2e') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" wire:model="internalCarbonPrice" step="0.01" class="block w-full pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">{{ $carbonPriceCurrency }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Currency') }}</label>
                            <select wire:model="carbonPriceCurrency" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="EUR">EUR</option>
                                <option value="USD">USD</option>
                                <option value="GBP">GBP</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Carbon Credits Policy') }}</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="usesCarbonCredits" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Organization uses carbon credits/offsets') }}</label>
                        </div>
                        @if($usesCarbonCredits)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Carbon Credits Policy') }}</label>
                                <textarea wire:model="carbonCreditsPolicy" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Maximum Credits (% of emissions)') }}</label>
                                <input type="number" wire:model="carbonCreditsMaxPercent" min="0" max="100" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Governance Section -->
        @if($activeSection === 'governance')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Climate Governance') }}</h3>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Board Oversight') }}</label>
                        <textarea wire:model="boardOversightDescription" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="{{ __('Describe how the board oversees climate-related issues...') }}"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Management Accountability') }}</label>
                        <textarea wire:model="managementAccountability" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="{{ __('Describe management roles and accountability...') }}"></textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="linkedToRemuneration" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Climate targets linked to executive remuneration') }}</label>
                    </div>
                    @if($linkedToRemuneration)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Remuneration Description') }}</label>
                            <textarea wire:model="remunerationDescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Risks Section -->
        @if($activeSection === 'risks')
            <div class="space-y-6">
                <!-- Transition Risks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Transition Risks') }}</h3>
                        <button type="button" wire:click="addRisk('transition')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add Risk') }}
                        </button>
                    </div>
                    @forelse($transitionRisks as $index => $risk)
                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" wire:model="transitionRisks.{{ $index }}.name" placeholder="{{ __('Risk Name') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                <select wire:model="transitionRisks.{{ $index }}.impact" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    <option value="low">{{ __('Low Impact') }}</option>
                                    <option value="medium">{{ __('Medium Impact') }}</option>
                                    <option value="high">{{ __('High Impact') }}</option>
                                </select>
                            </div>
                            <textarea wire:model="transitionRisks.{{ $index }}.description" rows="2" placeholder="{{ __('Description') }}" class="mt-2 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"></textarea>
                            <div class="mt-2 flex justify-end">
                                <button type="button" wire:click="removeRisk('transition', {{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-400 text-sm">{{ __('Remove') }}</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No transition risks defined') }}</p>
                    @endforelse
                </div>

                <!-- Physical Risks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Physical Risks') }}</h3>
                        <button type="button" wire:click="addRisk('physical')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add Risk') }}
                        </button>
                    </div>
                    @forelse($physicalRisks as $index => $risk)
                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" wire:model="physicalRisks.{{ $index }}.name" placeholder="{{ __('Risk Name') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                <select wire:model="physicalRisks.{{ $index }}.impact" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    <option value="low">{{ __('Low Impact') }}</option>
                                    <option value="medium">{{ __('Medium Impact') }}</option>
                                    <option value="high">{{ __('High Impact') }}</option>
                                </select>
                            </div>
                            <textarea wire:model="physicalRisks.{{ $index }}.description" rows="2" placeholder="{{ __('Description') }}" class="mt-2 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"></textarea>
                            <div class="mt-2 flex justify-end">
                                <button type="button" wire:click="removeRisk('physical', {{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-400 text-sm">{{ __('Remove') }}</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No physical risks defined') }}</p>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                {{ __('Save Draft') }}
            </button>
            @if($this->plan && $status === 'draft')
                <button type="button" wire:click="approve" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('Approve Plan') }}
                </button>
            @endif
        </div>
    </form>
</div>

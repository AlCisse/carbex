<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Value Chain Due Diligence') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('LkSG / CSDDD Compliance Management') }}</p>
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

    <!-- LkSG Applicability Banner -->
    @if($this->isLksgRequired)
        <div class="mb-6 rounded-lg bg-orange-50 dark:bg-orange-900/20 p-4 border border-orange-200 dark:border-orange-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-orange-800 dark:text-orange-200">{{ __('LkSG Applicable') }}</h3>
                    <p class="mt-1 text-sm text-orange-700 dark:text-orange-300">
                        {{ __('Based on employee count, your organization is subject to the German Supply Chain Due Diligence Act (LkSG).') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Compliance Score Card -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-3xl font-bold {{ $this->complianceScore >= 90 ? 'text-green-600 dark:text-green-400' : ($this->complianceScore >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">{{ number_format($this->complianceScore, 0) }}%</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Compliance Score') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ count($identifiedRisks) }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Risks Identified') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $suppliersAssessed }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Suppliers Assessed') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-3xl font-bold {{ $this->complaintResolutionRate !== null && $this->complaintResolutionRate >= 80 ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                {{ $this->complaintResolutionRate !== null ? number_format($this->complaintResolutionRate, 0) . '%' : '-' }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Resolution Rate') }}</p>
        </div>
    </div>

    <!-- Section Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 overflow-x-auto">
            @foreach(['policies' => 'Policies', 'risks' => 'Risk Assessment', 'prevention' => 'Prevention', 'monitoring' => 'Monitoring', 'grievance' => 'Grievance', 'reporting' => 'Reporting'] as $section => $label)
                <button wire:click="setSection('{{ $section }}')" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm {{ $activeSection === $section ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                    {{ __($label) }}
                </button>
            @endforeach
        </nav>
    </div>

    <form wire:submit="save">
        <!-- Policies Section -->
        @if($activeSection === 'policies')
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('LkSG Status') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="lksgApplicable" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('LkSG Applicable to Organization') }}</label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Compliance Status') }}</label>
                            <select wire:model="lksgStatus" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                                @foreach($this->lksgStatuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Policy Statements') }}</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="hasHumanRightsPolicy" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('Human Rights Policy Statement') }}</label>
                            </div>
                            @if($hasHumanRightsPolicy)
                                <input type="date" wire:model="humanRightsPolicyDate" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                            @endif
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="hasEnvironmentalPolicy" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('Environmental Policy Statement') }}</label>
                            </div>
                            @if($hasEnvironmentalPolicy)
                                <input type="date" wire:model="environmentalPolicyDate" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                            @endif
                        </div>
                        <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <input type="checkbox" wire:model="supplierCodeOfConduct" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('Supplier Code of Conduct') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Risk Assessment Section -->
        @if($activeSection === 'risks')
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Identified Risks') }}</h3>
                        <button type="button" wire:click="addRisk" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add Risk') }}
                        </button>
                    </div>
                    @forelse($identifiedRisks as $index => $risk)
                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                <select wire:model="identifiedRisks.{{ $index }}.category" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    <option value="human_rights">{{ __('Human Rights') }}</option>
                                    <option value="environmental">{{ __('Environmental') }}</option>
                                </select>
                                <input type="text" wire:model="identifiedRisks.{{ $index }}.description" placeholder="{{ __('Risk Description') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                <select wire:model="identifiedRisks.{{ $index }}.priority" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    <option value="low">{{ __('Low Priority') }}</option>
                                    <option value="medium">{{ __('Medium Priority') }}</option>
                                    <option value="high">{{ __('High Priority') }}</option>
                                </select>
                            </div>
                            <div class="flex justify-end">
                                <button type="button" wire:click="removeRisk({{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-400 text-sm">{{ __('Remove') }}</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No risks identified yet') }}</p>
                    @endforelse
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('High-Risk Countries') }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                        @foreach($this->highRiskCountriesDefault as $code => $name)
                            <label class="flex items-center p-2 rounded {{ in_array($code, $highRiskCountries) ? 'bg-orange-50 dark:bg-orange-900/20' : '' }}">
                                <input type="checkbox" wire:click="toggleHighRiskCountry('{{ $code }}')" {{ in_array($code, $highRiskCountries) ? 'checked' : '' }} class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('High-Risk Sectors') }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @foreach($this->highRiskSectorsDefault as $code => $name)
                            <label class="flex items-center p-2 rounded {{ in_array($code, $highRiskSectors) ? 'bg-orange-50 dark:bg-orange-900/20' : '' }}">
                                <input type="checkbox" wire:click="toggleHighRiskSector('{{ $code }}')" {{ in_array($code, $highRiskSectors) ? 'checked' : '' }} class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Prevention Section -->
        @if($activeSection === 'prevention')
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Prevention Measures') }}</h3>
                        <button type="button" wire:click="addPreventionMeasure" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add Measure') }}
                        </button>
                    </div>
                    @forelse($preventionMeasures as $index => $measure)
                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" wire:model="preventionMeasures.{{ $index }}.name" placeholder="{{ __('Measure Name') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                <select wire:model="preventionMeasures.{{ $index }}.status" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    <option value="planned">{{ __('Planned') }}</option>
                                    <option value="in_progress">{{ __('In Progress') }}</option>
                                    <option value="implemented">{{ __('Implemented') }}</option>
                                </select>
                            </div>
                            <textarea wire:model="preventionMeasures.{{ $index }}.description" rows="2" placeholder="{{ __('Description') }}" class="mt-2 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"></textarea>
                            <div class="mt-2 flex justify-end">
                                <button type="button" wire:click="removePreventionMeasure({{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-400 text-sm">{{ __('Remove') }}</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No prevention measures defined') }}</p>
                    @endforelse
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Contractual Assurances') }}</h3>
                        <button type="button" wire:click="addContractualAssurance" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add Assurance') }}
                        </button>
                    </div>
                    @forelse($contractualAssurances as $index => $assurance)
                        <div class="mb-3 flex items-center space-x-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <input type="text" wire:model="contractualAssurances.{{ $index }}.name" placeholder="{{ __('Assurance Name') }}" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                            <button type="button" wire:click="removeContractualAssurance({{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No contractual assurances defined') }}</p>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- Monitoring Section -->
        @if($activeSection === 'monitoring')
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Monitoring Mechanisms') }}</h3>
                        <button type="button" wire:click="addMonitoringMechanism" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Add Mechanism') }}
                        </button>
                    </div>
                    @forelse($monitoringMechanisms as $index => $mechanism)
                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <input type="text" wire:model="monitoringMechanisms.{{ $index }}.name" placeholder="{{ __('Mechanism Name') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                <select wire:model="monitoringMechanisms.{{ $index }}.type" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    <option value="audit">{{ __('Audit') }}</option>
                                    <option value="questionnaire">{{ __('Questionnaire') }}</option>
                                    <option value="certification">{{ __('Certification') }}</option>
                                    <option value="site_visit">{{ __('Site Visit') }}</option>
                                </select>
                                <select wire:model="monitoringMechanisms.{{ $index }}.frequency" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    <option value="continuous">{{ __('Continuous') }}</option>
                                    <option value="quarterly">{{ __('Quarterly') }}</option>
                                    <option value="annual">{{ __('Annual') }}</option>
                                    <option value="biennial">{{ __('Biennial') }}</option>
                                </select>
                            </div>
                            <div class="mt-2 flex justify-end">
                                <button type="button" wire:click="removeMonitoringMechanism({{ $index }})" class="text-red-600 hover:text-red-900 dark:text-red-400 text-sm">{{ __('Remove') }}</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No monitoring mechanisms defined') }}</p>
                    @endforelse
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Supplier Assessment') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Suppliers Assessed') }}</label>
                            <input type="number" wire:model="suppliersAssessed" min="0" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Supplier Audits Conducted') }}</label>
                            <input type="number" wire:model="supplierAuditsConducted" min="0" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Grievance Section -->
        @if($activeSection === 'grievance')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Grievance Mechanism') }}</h3>
                <div class="space-y-6">
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="hasWhistleblowerChannel" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Whistleblower channel available') }}</label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Complaints Received') }}</label>
                            <input type="number" wire:model="complaintsReceived" min="0" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Complaints Resolved') }}</label>
                            <input type="number" wire:model="complaintsResolved" min="0" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Reporting Section -->
        @if($activeSection === 'reporting')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Annual Report') }}</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="annualReportPublished" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Annual LkSG report published') }}</label>
                    </div>
                    @if($annualReportPublished)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Report URL') }}</label>
                            <input type="url" wire:model="reportUrl" placeholder="https://..." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                {{ __('Save') }}
            </button>
            @if($this->record)
                <button type="button" wire:click="markAsReviewed" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('Mark as Reviewed') }}
                </button>
            @endif
        </div>
    </form>
</div>

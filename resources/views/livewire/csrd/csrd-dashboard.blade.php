<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('linscarbon.csrd_dashboard.title') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.subtitle') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Year Selector -->
                <select wire:model.live="selectedYear" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                    @for($year = now()->year + 1; $year >= now()->year - 3; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <!-- Applicability Banner -->
    @if($this->csrdApplicability['applicable'])
        <div class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800 dark:text-green-200">{{ __('linscarbon.csrd_dashboard.applicable') }}</h3>
                    <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                        {{ __('linscarbon.csrd_dashboard.organization_classified') }} <strong>{{ ucfirst($this->csrdApplicability['category']) }}</strong>.
                        {{ __('linscarbon.csrd_dashboard.first_reporting_year') }}: <strong>{{ $this->csrdApplicability['first_reporting_year'] }}</strong>
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="mb-6 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 p-4 border border-yellow-200 dark:border-yellow-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ __('linscarbon.csrd_dashboard.may_not_apply') }}</h3>
                    <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                        {{ __('linscarbon.csrd_dashboard.voluntary_adoption') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Compliance Score Card -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Overall Score -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.overall_compliance') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $this->complianceScore['score'] ?? 0 }}%</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $this->complianceScore['score'] ?? 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- ESRS 2 Progress -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.esrs2_disclosures') }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $this->esrs2Progress['completed'] }}/{{ $this->esrs2Progress['total'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('csrd.esrs2') }}" class="mt-4 inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                {{ __('linscarbon.csrd_dashboard.manage_disclosures') }}
                <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <!-- Transition Plan -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.transition_plan') }}</p>
                    @if($this->transitionPlan)
                        <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->transitionPlan->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                {{ ucfirst($this->transitionPlan->status) }}
                            </span>
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $this->transitionPlan->temperature_target }}</p>
                    @else
                        <p class="mt-2 text-lg font-semibold text-red-600 dark:text-red-400">{{ __('linscarbon.csrd_dashboard.not_created') }}</p>
                    @endif
                </div>
                <div class="h-12 w-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('csrd.transition-plan') }}" class="mt-4 inline-flex items-center text-sm text-purple-600 dark:text-purple-400 hover:underline">
                {{ __('linscarbon.csrd_dashboard.edit_plan') }}
                <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <!-- EU Taxonomy -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.eu_taxonomy') }}</p>
                    @if($this->taxonomyReport)
                        <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ number_format($this->taxonomyReport->turnover_aligned_percent ?? 0, 1) }}% {{ __('linscarbon.csrd_dashboard.aligned') }}
                        </p>
                    @else
                        <p class="mt-2 text-lg font-semibold text-red-600 dark:text-red-400">{{ __('linscarbon.csrd_dashboard.not_reported') }}</p>
                    @endif
                </div>
                <div class="h-12 w-12 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                    <svg class="h-6 w-6 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('csrd.taxonomy') }}" class="mt-4 inline-flex items-center text-sm text-teal-600 dark:text-teal-400 hover:underline">
                {{ __('linscarbon.csrd_dashboard.edit_report') }}
                <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="setTab('overview')" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                {{ __('linscarbon.csrd_dashboard.overview') }}
            </button>
            <button wire:click="setTab('deadlines')" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'deadlines' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                {{ __('linscarbon.csrd_dashboard.deadlines') }}
            </button>
            <button wire:click="setTab('due-diligence')" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'due-diligence' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                {{ __('linscarbon.csrd_dashboard.due_diligence_lksg') }}
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    @if($activeTab === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- ESRS 2 Progress -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('linscarbon.csrd_dashboard.esrs2_general_disclosures') }}</h3>
                <div class="space-y-4">
                    @foreach(['bp' => __('linscarbon.csrd_dashboard.basis_preparation'), 'gov' => __('linscarbon.csrd_dashboard.governance'), 'sbm' => __('linscarbon.csrd_dashboard.strategy_business_model'), 'iro' => __('linscarbon.csrd_dashboard.impacts_risks_opportunities')] as $category => $label)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-400">{{ $label }}</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $this->esrs2Progress['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $this->esrs2Progress['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('csrd.esrs2') }}" class="mt-4 inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    {{ __('linscarbon.csrd_dashboard.view_all_disclosures') }}
                    <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('linscarbon.csrd_dashboard.quick_actions') }}</h3>
                <div class="space-y-3">
                    <a href="{{ route('csrd.esrs2') }}" class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mr-3">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ __('linscarbon.csrd_dashboard.esrs2_disclosures') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.manage_general_disclosures') }}</p>
                        </div>
                    </a>
                    <a href="{{ route('csrd.transition-plan') }}" class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mr-3">
                            <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ __('linscarbon.csrd_dashboard.climate_transition_plan') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.esrs_e1_requirements') }}</p>
                        </div>
                    </a>
                    <a href="{{ route('csrd.taxonomy') }}" class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="h-10 w-10 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center mr-3">
                            <svg class="h-5 w-5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ __('linscarbon.csrd_dashboard.eu_taxonomy_report') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.article8_disclosure') }}</p>
                        </div>
                    </a>
                    <a href="{{ route('csrd.due-diligence') }}" class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="h-10 w-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mr-3">
                            <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ __('linscarbon.csrd_dashboard.value_chain_due_diligence') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.lksg_csddd_compliance') }}</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    @elseif($activeTab === 'deadlines')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('linscarbon.csrd_dashboard.upcoming_deadlines') }}</h3>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($this->upcomingDeadlines as $deadline)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $deadline['name'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $deadline['description'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium {{ $deadline['days_remaining'] < 30 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                    {{ $deadline['date'] }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $deadline['days_remaining'] }} {{ __('linscarbon.csrd_dashboard.days_remaining') }}
                                </p>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        {{ __('linscarbon.csrd_dashboard.no_upcoming_deadlines') }}
                    </li>
                @endforelse
            </ul>
        </div>
    @elseif($activeTab === 'due-diligence')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('linscarbon.csrd_dashboard.due_diligence_status') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.lksg_german_law') }}</p>
                </div>
                @if($this->dueDiligence)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $this->dueDiligence->lksg_status === 'compliant' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                        {{ $this->dueDiligence->status_label }}
                    </span>
                @endif
            </div>

            @if($this->dueDiligence)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->dueDiligence->compliance_score, 0) }}%</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.compliance_score') }}</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($this->dueDiligence->identified_risks ?? []) }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.risks_identified') }}</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->dueDiligence->suppliers_assessed }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.suppliers_assessed') }}</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->dueDiligence->complaints_resolved }}/{{ $this->dueDiligence->complaints_received }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('linscarbon.csrd_dashboard.complaints_resolved') }}</p>
                    </div>
                </div>
            @endif

            <a href="{{ route('csrd.due-diligence') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                {{ $this->dueDiligence ? __('linscarbon.csrd_dashboard.manage_due_diligence') : __('linscarbon.csrd_dashboard.start_assessment') }}
                <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    @endif
</div>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('carbex.compliance.title') }}
            </h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                {{ __('carbex.compliance.subtitle') }}
            </p>
        </div>

        {{-- Year Selector --}}
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('carbex.compliance.reporting_year') }}:
                </label>
                <select wire:model.live="selectedYear"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    @for($y = now()->year + 1; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button wire:click="openTaskModal"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('carbex.compliance.add_task') }}
            </button>
        </div>

        {{-- Tabs --}}
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8">
                @foreach(['overview' => __('carbex.compliance.tabs.overview'), 'csrd' => 'CSRD', 'iso' => 'ISO', 'tasks' => __('carbex.compliance.tabs.tasks')] as $tab => $label)
                    <button wire:click="setTab('{{ $tab }}')"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === $tab ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Overview Tab --}}
        @if($activeTab === 'overview')
            <div class="space-y-6">
                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- CSRD Progress --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">CSRD {{ __('carbex.compliance.progress') }}</h3>
                            <span class="text-2xl font-bold text-emerald-600">{{ $this->csrdStats['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full transition-all" style="width: {{ $this->csrdStats['percentage'] }}%"></div>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ $this->csrdStats['compliant'] }} / {{ $this->csrdStats['total'] }}</span>
                            <span>{{ __('carbex.compliance.disclosures') }}</span>
                        </div>
                    </div>

                    {{-- ISO Progress --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">ISO {{ __('carbex.compliance.certifications') }}</h3>
                            <span class="text-2xl font-bold text-blue-600">{{ $this->isoStats['certified'] }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="flex items-center space-x-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">{{ __('carbex.compliance.certified') }}: {{ $this->isoStats['certified'] }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span class="text-gray-600 dark:text-gray-400">{{ __('carbex.compliance.in_progress') }}: {{ $this->isoStats['in_progress'] }}</span>
                            </div>
                        </div>
                        @if($this->isoStats['expiring_soon'] > 0)
                            <div class="mt-3 text-xs text-amber-600 dark:text-amber-400">
                                {{ $this->isoStats['expiring_soon'] }} {{ __('carbex.compliance.expiring_soon') }}
                            </div>
                        @endif
                    </div>

                    {{-- Overdue Tasks --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('carbex.compliance.overdue_tasks') }}</h3>
                            <span class="text-2xl font-bold {{ $this->overdueTasks->count() > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                {{ $this->overdueTasks->count() }}
                            </span>
                        </div>
                        @if($this->overdueTasks->count() > 0)
                            <ul class="space-y-2">
                                @foreach($this->overdueTasks->take(3) as $task)
                                    <li class="text-xs text-gray-600 dark:text-gray-400 truncate">
                                        <span class="text-red-500">!</span> {{ $task->title }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('carbex.compliance.no_overdue') }}</p>
                        @endif
                    </div>

                    {{-- Upcoming Tasks --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('carbex.compliance.upcoming') }}</h3>
                            <span class="text-2xl font-bold text-amber-600">{{ $this->upcomingTasks->count() }}</span>
                        </div>
                        @if($this->upcomingTasks->count() > 0)
                            <ul class="space-y-2">
                                @foreach($this->upcomingTasks as $task)
                                    <li class="text-xs text-gray-600 dark:text-gray-400 truncate">
                                        {{ $task->due_date?->format('d/m') }} - {{ $task->title }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('carbex.compliance.no_upcoming') }}</p>
                        @endif
                    </div>
                </div>

                {{-- CSRD Categories Overview --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">CSRD {{ __('carbex.compliance.by_category') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($this->csrdCategories as $key => $label)
                            @php
                                $categoryFrameworks = $this->csrdFrameworks->where('category', $key);
                                $categoryCompliant = $this->csrdCompliance->filter(fn($c) => $categoryFrameworks->pluck('id')->contains($c->csrd_framework_id) && $c->status === 'compliant')->count();
                                $categoryTotal = $categoryFrameworks->count();
                                $percentage = $categoryTotal > 0 ? round(($categoryCompliant / $categoryTotal) * 100) : 0;
                                $color = match($key) {
                                    'environment' => 'emerald',
                                    'social' => 'blue',
                                    'governance' => 'purple',
                                    default => 'gray'
                                };
                            @endphp
                            <div class="p-4 rounded-lg bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 border border-{{ $color }}-200 dark:border-{{ $color }}-800">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-{{ $color }}-800 dark:text-{{ $color }}-300">{{ $label }}</span>
                                    <span class="text-sm text-{{ $color }}-600 dark:text-{{ $color }}-400">{{ $percentage }}%</span>
                                </div>
                                <div class="w-full bg-{{ $color }}-200 dark:bg-{{ $color }}-800 rounded-full h-2">
                                    <div class="bg-{{ $color }}-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <p class="mt-2 text-xs text-{{ $color }}-600 dark:text-{{ $color }}-400">
                                    {{ $categoryCompliant }} / {{ $categoryTotal }} {{ __('carbex.compliance.requirements') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- CSRD Tab --}}
        @if($activeTab === 'csrd')
            <div class="space-y-6">
                {{-- Category Filter --}}
                <div class="flex items-center space-x-2">
                    <button wire:click="setCsrdCategory(null)"
                            class="px-3 py-1.5 text-sm rounded-lg {{ !$selectedCsrdCategory ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ __('carbex.common.all') }}
                    </button>
                    @foreach($this->csrdCategories as $key => $label)
                        <button wire:click="setCsrdCategory('{{ $key }}')"
                                class="px-3 py-1.5 text-sm rounded-lg {{ $selectedCsrdCategory === $key ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- Frameworks Table --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.compliance.code') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.compliance.disclosure') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.compliance.category') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.compliance.status') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.compliance.progress') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->csrdFrameworks as $framework)
                                @php
                                    $compliance = $this->csrdCompliance->get($framework->id);
                                    $status = $compliance?->status ?? 'not_started';
                                    $percentage = $compliance?->completion_percentage ?? 0;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-sm text-gray-900 dark:text-white">{{ $framework->code }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $framework->translated_name }}</p>
                                        @if($framework->is_mandatory)
                                            <span class="text-xs text-red-600 dark:text-red-400">{{ __('carbex.compliance.mandatory') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $framework->category_color }}-100 text-{{ $framework->category_color }}-800 dark:bg-{{ $framework->category_color }}-900/30 dark:text-{{ $framework->category_color }}-300">
                                            {{ $framework->category_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select wire:change="updateCsrdStatus('{{ $framework->id }}', $event.target.value)"
                                                class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            <option value="not_started" {{ $status === 'not_started' ? 'selected' : '' }}>{{ __('carbex.compliance.status.not_started') }}</option>
                                            <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>{{ __('carbex.compliance.status.in_progress') }}</option>
                                            <option value="compliant" {{ $status === 'compliant' ? 'selected' : '' }}>{{ __('carbex.compliance.status.compliant') }}</option>
                                            <option value="non_compliant" {{ $status === 'non_compliant' ? 'selected' : '' }}>{{ __('carbex.compliance.status.non_compliant') }}</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $percentage }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('carbex.compliance.no_frameworks') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ISO Tab --}}
        @if($activeTab === 'iso')
            <div class="space-y-6">
                {{-- Category Filter --}}
                <div class="flex items-center space-x-2">
                    <button wire:click="setIsoCategory(null)"
                            class="px-3 py-1.5 text-sm rounded-lg {{ !$selectedIsoCategory ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ __('carbex.common.all') }}
                    </button>
                    @foreach($this->isoCategories as $key => $label)
                        <button wire:click="setIsoCategory('{{ $key }}')"
                                class="px-3 py-1.5 text-sm rounded-lg {{ $selectedIsoCategory === $key ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- Standards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($this->isoStandards as $standard)
                        @php
                            $certification = $this->isoCertifications->get($standard->id);
                            $status = $certification?->status ?? 'not_certified';
                        @endphp
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <span class="font-mono text-lg font-bold text-gray-900 dark:text-white">{{ $standard->code }}</span>
                                    <h3 class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $standard->translated_name }}</h3>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $standard->category_color }}-100 text-{{ $standard->category_color }}-800 dark:bg-{{ $standard->category_color }}-900/30 dark:text-{{ $standard->category_color }}-300">
                                    {{ $standard->category_label }}
                                </span>
                            </div>

                            @if($standard->translated_description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 line-clamp-2">
                                    {{ $standard->translated_description }}
                                </p>
                            @endif

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('carbex.compliance.status') }}</label>
                                    <select wire:change="updateIsoStatus('{{ $standard->id }}', $event.target.value)"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="not_certified" {{ $status === 'not_certified' ? 'selected' : '' }}>{{ __('carbex.compliance.cert_status.not_certified') }}</option>
                                        <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>{{ __('carbex.compliance.cert_status.in_progress') }}</option>
                                        <option value="certified" {{ $status === 'certified' ? 'selected' : '' }}>{{ __('carbex.compliance.cert_status.certified') }}</option>
                                        <option value="expired" {{ $status === 'expired' ? 'selected' : '' }}>{{ __('carbex.compliance.cert_status.expired') }}</option>
                                    </select>
                                </div>

                                @if($certification && $certification->expiry_date)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('carbex.compliance.expires') }}:</span>
                                        <span class="{{ $certification->isExpiringSoon() ? 'text-amber-600' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $certification->expiry_date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                @endif

                                @if($certification && $certification->certifying_body)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('carbex.compliance.certifier') }}:</span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $certification->certifying_body }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">
                            {{ __('carbex.compliance.no_standards') }}
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- Tasks Tab --}}
        @if($activeTab === 'tasks')
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.compliance.task') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.compliance.type') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.compliance.priority') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.compliance.due_date') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.compliance.status') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->complianceTasks as $task)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->title }}</p>
                                        @if($task->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $task->description }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="uppercase text-xs font-medium text-gray-600 dark:text-gray-400">{{ $task->type }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $task->priority_color }}-100 text-{{ $task->priority_color }}-800 dark:bg-{{ $task->priority_color }}-900/30 dark:text-{{ $task->priority_color }}-300">
                                            {{ $task->priority_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($task->due_date)
                                            <span class="{{ $task->isOverdue() ? 'text-red-600' : ($task->isDueSoon() ? 'text-amber-600' : 'text-gray-600 dark:text-gray-400') }} text-sm">
                                                {{ $task->due_date->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800 dark:bg-{{ $task->status_color }}-900/30 dark:text-{{ $task->status_color }}-300">
                                            {{ $task->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-2">
                                            @if($task->status !== 'completed')
                                                <button wire:click="completeTask('{{ $task->id }}')"
                                                        class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400"
                                                        title="{{ __('carbex.compliance.mark_complete') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            @endif
                                            <button wire:click="openTaskModal('{{ $task->id }}')"
                                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                                    title="{{ __('carbex.common.edit') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="deleteTask('{{ $task->id }}')"
                                                    wire:confirm="{{ __('carbex.compliance.confirm_delete_task') }}"
                                                    class="text-red-400 hover:text-red-600 dark:hover:text-red-300"
                                                    title="{{ __('carbex.common.delete') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('carbex.compliance.no_tasks') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- Task Modal --}}
    @if($showTaskModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="closeTaskModal" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveTask">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $editingTaskId ? __('carbex.compliance.edit_task') : __('carbex.compliance.new_task') }}
                            </h3>
                        </div>

                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('carbex.compliance.type') }}</label>
                                <select wire:model="taskForm.type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="csrd">CSRD</option>
                                    <option value="iso">ISO</option>
                                    <option value="internal">{{ __('carbex.compliance.internal') }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('carbex.compliance.title') }}</label>
                                <input type="text" wire:model="taskForm.title"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                       placeholder="{{ __('carbex.compliance.task_title_placeholder') }}">
                                @error('taskForm.title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('carbex.compliance.description') }}</label>
                                <textarea wire:model="taskForm.description" rows="3"
                                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                          placeholder="{{ __('carbex.compliance.task_description_placeholder') }}"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('carbex.compliance.priority') }}</label>
                                    <select wire:model="taskForm.priority" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="low">{{ __('carbex.compliance.priority.low') }}</option>
                                        <option value="medium">{{ __('carbex.compliance.priority.medium') }}</option>
                                        <option value="high">{{ __('carbex.compliance.priority.high') }}</option>
                                        <option value="critical">{{ __('carbex.compliance.priority.critical') }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('carbex.compliance.due_date') }}</label>
                                    <input type="date" wire:model="taskForm.due_date"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3">
                            <button type="button" wire:click="closeTaskModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500">
                                {{ __('carbex.common.cancel') }}
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg">
                                {{ $editingTaskId ? __('carbex.common.save') : __('carbex.common.create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

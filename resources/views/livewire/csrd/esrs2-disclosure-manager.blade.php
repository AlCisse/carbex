<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('ESRS 2 General Disclosures') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('European Sustainability Reporting Standards - Cross-cutting disclosures') }}</p>
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

    <!-- Stats -->
    <div class="mb-6 grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->stats['total'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $this->stats['completed'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Completed') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $this->stats['in_progress'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('In Progress') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $this->stats['draft'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Draft') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-2xl font-bold text-gray-400">{{ $this->stats['not_started'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Not Started') }}</p>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="mb-6 flex flex-wrap gap-2">
        <button wire:click="setCategory(null)" class="px-4 py-2 rounded-md text-sm font-medium {{ !$selectedCategory ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
            {{ __('All') }}
        </button>
        @foreach($this->categories as $key => $label)
            <button wire:click="setCategory('{{ $key }}')" class="px-4 py-2 rounded-md text-sm font-medium {{ $selectedCategory === $key ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Disclosures List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($this->disclosureDefinitions as $code => $definition)
                @php
                    $disclosure = $this->disclosures[$code] ?? null;
                    $status = $disclosure?->status ?? 'not_started';
                @endphp
                <li class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $code }}
                                </span>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $definition['name'] }}
                                </h3>
                                @if($definition['mandatory'] ?? true)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        {{ __('Mandatory') }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $this->categories[$definition['category']] ?? '' }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Status Badge -->
                            @switch($status)
                                @case('completed')
                                @case('verified')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        {{ ucfirst($status) }}
                                    </span>
                                    @break
                                @case('in_progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ __('In Progress') }}
                                    </span>
                                    @break
                                @case('draft')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        {{ __('Draft') }}
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                                        {{ __('Not Started') }}
                                    </span>
                            @endswitch

                            <!-- Quick Status Update -->
                            <select wire:change="updateStatus('{{ $code }}', $event.target.value)" class="text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="not_started" {{ $status === 'not_started' ? 'selected' : '' }}>{{ __('Not Started') }}</option>
                                <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                                <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                <option value="verified" {{ $status === 'verified' ? 'selected' : '' }}>{{ __('Verified') }}</option>
                            </select>

                            <!-- Edit Button -->
                            <button wire:click="openEditModal('{{ $code }}')" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Edit Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full sm:p-6">
                    <form wire:submit="save">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $form['disclosure_code'] }} - {{ $form['disclosure_name'] }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $this->categories[$form['category']] ?? '' }}</p>
                        </div>

                        <div class="space-y-4">
                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                                <select wire:model="form.status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="not_started">{{ __('Not Started') }}</option>
                                    <option value="in_progress">{{ __('In Progress') }}</option>
                                    <option value="draft">{{ __('Draft') }}</option>
                                    <option value="completed">{{ __('Completed') }}</option>
                                    <option value="verified">{{ __('Verified') }}</option>
                                </select>
                            </div>

                            <!-- Narrative Disclosure -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Narrative Disclosure') }}</label>
                                <textarea wire:model="form.narrative_disclosure" rows="6" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="{{ __('Enter your disclosure narrative...') }}"></textarea>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Provide detailed narrative information as required by the disclosure.') }}</p>
                            </div>

                            <!-- Review Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Review Notes') }}</label>
                                <textarea wire:model="form.review_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="{{ __('Internal notes for review...') }}"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

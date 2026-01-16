<div>
    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">{{ __('carbex.actions.title') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('carbex.actions.subtitle') }}</p>
        </div>
        <button wire:click="openCreateModal" type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('carbex.actions.new') }}
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center space-x-4">
            <button wire:click="setFilter('all')" class="px-3 py-1.5 text-sm font-medium rounded-md {{ $filter === 'all' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100' }}">
                {{ __('carbex.actions.filter_all') }} ({{ $statusCounts['all'] }})
            </button>
            <button wire:click="setFilter('todo')" class="px-3 py-1.5 text-sm font-medium rounded-md {{ $filter === 'todo' ? 'bg-yellow-100 text-yellow-700' : 'text-gray-600 hover:bg-gray-100' }}">
                {{ __('carbex.actions.status.todo') }} ({{ $statusCounts['todo'] }})
            </button>
            <button wire:click="setFilter('in_progress')" class="px-3 py-1.5 text-sm font-medium rounded-md {{ $filter === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                {{ __('carbex.actions.status.in_progress') }} ({{ $statusCounts['in_progress'] }})
            </button>
            <button wire:click="setFilter('completed')" class="px-3 py-1.5 text-sm font-medium rounded-md {{ $filter === 'completed' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100' }}">
                {{ __('carbex.actions.status.completed') }} ({{ $statusCounts['completed'] }})
            </button>
        </div>
    </div>

    <!-- Actions List -->
    @if($actions->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @foreach($actions as $action)
                    <li class="p-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3">
                                    <!-- Status checkbox -->
                                    @if($action->isCompleted())
                                        <button wire:click="updateStatus('{{ $action->id }}', 'in_progress')" class="flex-shrink-0 h-5 w-5 rounded-full bg-green-500 flex items-center justify-center" title="{{ __('carbex.actions.reopen') }}">
                                            <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @elseif($action->isInProgress())
                                        <button wire:click="updateStatus('{{ $action->id }}', 'completed')" class="flex-shrink-0 h-5 w-5 rounded-full border-2 border-blue-500 bg-blue-100" title="{{ __('carbex.actions.complete') }}"></button>
                                    @else
                                        <button wire:click="updateStatus('{{ $action->id }}', 'in_progress')" class="flex-shrink-0 h-5 w-5 rounded-full border-2 border-gray-300" title="{{ __('carbex.actions.start') }}"></button>
                                    @endif

                                    <!-- Title -->
                                    <h3 class="text-sm font-medium text-gray-900 {{ $action->isCompleted() ? 'line-through text-gray-500' : '' }}">
                                        {{ $action->title }}
                                    </h3>

                                    <!-- Status badge -->
                                    @if($action->isTodo())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ __('carbex.actions.status.todo') }}
                                        </span>
                                    @elseif($action->isInProgress())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ __('carbex.actions.status.in_progress') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            {{ __('carbex.actions.status.completed') }}
                                        </span>
                                    @endif

                                    <!-- Overdue badge -->
                                    @if($action->isOverdue())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            {{ __('carbex.actions.overdue') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Description -->
                                @if($action->description)
                                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ Str::limit(strip_tags($action->description), 150) }}</p>
                                @endif

                                <!-- Meta info -->
                                <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                    @if($action->due_date)
                                        <span class="flex items-center {{ $action->isOverdue() ? 'text-red-600' : '' }}">
                                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $action->due_date->format('d/m/Y') }}
                                        </span>
                                    @endif

                                    @if($action->co2_reduction_percent)
                                        <span class="flex items-center text-green-600">
                                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                            </svg>
                                            -{{ $action->co2_reduction_percent }}% CO2
                                        </span>
                                    @endif

                                    @if($action->estimated_cost)
                                        <span class="flex items-center">
                                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ number_format($action->estimated_cost, 0, ',', ' ') }} €
                                        </span>
                                    @endif

                                    <!-- Difficulty -->
                                    <span class="flex items-center">
                                        @if($action->difficulty === 'easy')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700">
                                                {{ __('carbex.actions.difficulty.easy') }}
                                            </span>
                                        @elseif($action->difficulty === 'medium')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-50 text-yellow-700">
                                                {{ __('carbex.actions.difficulty.medium') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700">
                                                {{ __('carbex.actions.difficulty.hard') }}
                                            </span>
                                        @endif
                                    </span>

                                    @if($action->category)
                                        <span class="flex items-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                {{ $action->category->code }} - {{ $action->category->name }}
                                            </span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2 ml-4">
                                <button wire:click="openEditModal('{{ $action->id }}')" class="text-indigo-600 hover:text-indigo-900" title="{{ __('carbex.common.edit') }}">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="deleteAction('{{ $action->id }}')" wire:confirm="{{ __('carbex.actions.confirm_delete') }}" class="text-red-600 hover:text-red-900" title="{{ __('carbex.common.delete') }}">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <!-- Empty state -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('carbex.actions.empty') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('carbex.actions.empty_description') }}</p>
            <div class="mt-6">
                <button wire:click="openCreateModal" type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('carbex.actions.create_first') }}
                </button>
            </div>
        </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40" wire:click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="relative z-50 inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                <form wire:submit="save">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $editingId ? __('carbex.actions.edit') : __('carbex.actions.new') }}
                        </h3>

                        <div class="mt-4 space-y-4">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">
                                    {{ __('carbex.actions.form.title') }} <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="title" type="text" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="{{ __('carbex.actions.form.title_placeholder') }}">
                                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    {{ __('carbex.actions.form.description') }}
                                </label>
                                <textarea wire:model="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="{{ __('carbex.actions.form.description_placeholder') }}"></textarea>
                                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Due Date -->
                                <div>
                                    <label for="dueDate" class="block text-sm font-medium text-gray-700">
                                        {{ __('carbex.actions.form.due_date') }}
                                    </label>
                                    <input wire:model="dueDate" type="date" id="dueDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                    @error('dueDate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Category -->
                                <div>
                                    <label for="categoryId" class="block text-sm font-medium text-gray-700">
                                        {{ __('carbex.actions.form.category') }}
                                    </label>
                                    <select wire:model="categoryId" id="categoryId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                        <option value="">{{ __('carbex.actions.form.category_none') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->code }} - {{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('categoryId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">
                                        {{ __('carbex.actions.form.status') }}
                                    </label>
                                    <select wire:model="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                        <option value="todo">{{ __('carbex.actions.status.todo') }}</option>
                                        <option value="in_progress">{{ __('carbex.actions.status.in_progress') }}</option>
                                        <option value="completed">{{ __('carbex.actions.status.completed') }}</option>
                                    </select>
                                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Estimated Cost -->
                                <div>
                                    <label for="estimatedCost" class="block text-sm font-medium text-gray-700">
                                        {{ __('carbex.actions.form.estimated_cost') }}
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input wire:model="estimatedCost" type="number" step="0.01" id="estimatedCost" class="block w-full pr-12 rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="5000">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">€</span>
                                        </div>
                                    </div>
                                    @error('estimatedCost') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- CO2 Reduction Percent -->
                            <div>
                                <label for="co2ReductionPercent" class="block text-sm font-medium text-gray-700">
                                    {{ __('carbex.actions.form.co2_reduction') }} <span class="text-green-600 font-semibold">{{ $co2ReductionPercent }}%</span>
                                </label>
                                <input wire:model.live="co2ReductionPercent" type="range" id="co2ReductionPercent" min="0" max="100" class="mt-2 w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-green-600">
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>0%</span>
                                    <span>50%</span>
                                    <span>100%</span>
                                </div>
                                @error('co2ReductionPercent') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Difficulty -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('carbex.actions.form.difficulty') }}
                                </label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input wire:model="difficulty" type="radio" value="easy" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">{{ __('carbex.actions.difficulty.easy') }}</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="difficulty" type="radio" value="medium" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">{{ __('carbex.actions.difficulty.medium') }}</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input wire:model="difficulty" type="radio" value="hard" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">{{ __('carbex.actions.difficulty.hard') }}</span>
                                    </label>
                                </div>
                                @error('difficulty') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm">
                            {{ __('carbex.common.save') }}
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            {{ __('carbex.common.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

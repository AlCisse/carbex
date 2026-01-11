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

    <!-- Trajectory Chart (T069) -->
    <div class="mb-6">
        <livewire:transition-plan.trajectory-chart />
    </div>

    <!-- SBTi Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">{{ __('carbex.targets.sbti_title') }}</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>{{ __('carbex.targets.sbti_description') }}</p>
                    <ul class="mt-2 list-disc list-inside space-y-1">
                        <li><strong>{{ __('carbex.targets.sbti_scope12_rate') }}</strong> {{ __('carbex.targets.sbti_scope12_label') }}</li>
                        <li><strong>{{ __('carbex.targets.sbti_scope3_rate') }}</strong> {{ __('carbex.targets.sbti_scope3_label') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Trajectory Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">{{ __('carbex.targets.title') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('carbex.targets.subtitle') }}</p>
        </div>
        <button wire:click="openCreateModal" type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('carbex.targets.new') }}
        </button>
    </div>

    <!-- Targets List -->
    @if($targets->count() > 0)
        <div class="space-y-4">
            @foreach($targets as $target)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ $target->baseline_year }} - {{ $target->target_year }}
                                    </h3>
                                    @if($target->is_sbti_aligned)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            {{ __('carbex.targets.sbti_aligned') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ __('carbex.targets.sbti_not_aligned') }}
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('carbex.targets.horizon', ['years' => $target->years_to_target]) }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button wire:click="openEditModal('{{ $target->id }}')" class="text-indigo-600 hover:text-indigo-900" title="{{ __('carbex.common.edit') }}">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="deleteTarget('{{ $target->id }}')" wire:confirm="{{ __('carbex.targets.confirm_delete') }}" class="text-red-600 hover:text-red-900" title="{{ __('carbex.common.delete') }}">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Scope Reductions -->
                        <div class="mt-6 grid grid-cols-3 gap-6">
                            <!-- Scope 1 -->
                            <div class="bg-orange-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-orange-800">Scope 1</span>
                                    @if($target->isScope1SbtiCompliant())
                                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <p class="mt-2 text-2xl font-semibold text-orange-900">-{{ $target->scope_1_reduction }}%</p>
                                <p class="mt-1 text-xs text-orange-700">{{ $target->scope_1_annual_rate }}%/{{ __('carbex.targets.per_year') }}</p>
                            </div>

                            <!-- Scope 2 -->
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-yellow-800">Scope 2</span>
                                    @if($target->isScope2SbtiCompliant())
                                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <p class="mt-2 text-2xl font-semibold text-yellow-900">-{{ $target->scope_2_reduction }}%</p>
                                <p class="mt-1 text-xs text-yellow-700">{{ $target->scope_2_annual_rate }}%/{{ __('carbex.targets.per_year') }}</p>
                            </div>

                            <!-- Scope 3 -->
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-blue-800">Scope 3</span>
                                    @if($target->isScope3SbtiCompliant())
                                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <p class="mt-2 text-2xl font-semibold text-blue-900">-{{ $target->scope_3_reduction }}%</p>
                                <p class="mt-1 text-xs text-blue-700">{{ $target->scope_3_annual_rate }}%/{{ __('carbex.targets.per_year') }}</p>
                            </div>
                        </div>

                        @if($target->notes)
                            <div class="mt-4 text-sm text-gray-500">
                                <p>{{ $target->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('carbex.targets.empty') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('carbex.targets.empty_description') }}</p>
            <div class="mt-6">
                <button wire:click="openCreateModal" type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('carbex.targets.create_first') }}
                </button>
            </div>
        </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full sm:p-6">
                <form wire:submit="save">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $editingId ? __('carbex.targets.edit') : __('carbex.targets.new') }}
                        </h3>

                        <div class="mt-4 space-y-4">
                            <!-- Years -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="baselineYear" class="block text-sm font-medium text-gray-700">
                                        {{ __('carbex.targets.baseline_year') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="baselineYear" id="baselineYear" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                        @for($y = 2020; $y <= 2035; $y++)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                    @error('baselineYear') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="targetYear" class="block text-sm font-medium text-gray-700">
                                        {{ __('carbex.targets.target_year') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="targetYear" id="targetYear" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                        @for($y = 2025; $y <= 2050; $y++)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                    @error('targetYear') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- SBTi Button -->
                            <div class="flex justify-end">
                                <button type="button" wire:click="applySbtiDefaults" class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100">
                                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('carbex.targets.apply_sbti') }}
                                </button>
                            </div>

                            <!-- Scope 1 -->
                            <div>
                                <label for="scope1Reduction" class="block text-sm font-medium text-gray-700">
                                    {{ __('carbex.targets.scope1_reduction') }}
                                    <span class="text-orange-600 font-semibold">{{ $scope1Reduction }}%</span>
                                    <span class="text-xs text-gray-500">({{ $sbtiCompliance['scope1_annual'] ?? 0 }}%/{{ __('carbex.targets.per_year') }})</span>
                                    @if($sbtiCompliance['scope1'] ?? false)
                                        <span class="ml-2 text-green-600">✓ SBTi</span>
                                    @endif
                                </label>
                                <input wire:model.live="scope1Reduction" type="range" id="scope1Reduction" min="0" max="100" step="1" class="mt-2 w-full h-2 bg-orange-200 rounded-lg appearance-none cursor-pointer accent-orange-600">
                                @error('scope1Reduction') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Scope 2 -->
                            <div>
                                <label for="scope2Reduction" class="block text-sm font-medium text-gray-700">
                                    {{ __('carbex.targets.scope2_reduction') }}
                                    <span class="text-yellow-600 font-semibold">{{ $scope2Reduction }}%</span>
                                    <span class="text-xs text-gray-500">({{ $sbtiCompliance['scope2_annual'] ?? 0 }}%/{{ __('carbex.targets.per_year') }})</span>
                                    @if($sbtiCompliance['scope2'] ?? false)
                                        <span class="ml-2 text-green-600">✓ SBTi</span>
                                    @endif
                                </label>
                                <input wire:model.live="scope2Reduction" type="range" id="scope2Reduction" min="0" max="100" step="1" class="mt-2 w-full h-2 bg-yellow-200 rounded-lg appearance-none cursor-pointer accent-yellow-600">
                                @error('scope2Reduction') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Scope 3 -->
                            <div>
                                <label for="scope3Reduction" class="block text-sm font-medium text-gray-700">
                                    {{ __('carbex.targets.scope3_reduction') }}
                                    <span class="text-blue-600 font-semibold">{{ $scope3Reduction }}%</span>
                                    <span class="text-xs text-gray-500">({{ $sbtiCompliance['scope3_annual'] ?? 0 }}%/{{ __('carbex.targets.per_year') }})</span>
                                    @if($sbtiCompliance['scope3'] ?? false)
                                        <span class="ml-2 text-green-600">✓ SBTi</span>
                                    @endif
                                </label>
                                <input wire:model.live="scope3Reduction" type="range" id="scope3Reduction" min="0" max="100" step="1" class="mt-2 w-full h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                @error('scope3Reduction') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">
                                    {{ __('carbex.targets.notes') }}
                                </label>
                                <textarea wire:model="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="{{ __('carbex.targets.notes_placeholder') }}"></textarea>
                                @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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

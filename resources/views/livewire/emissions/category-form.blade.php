<div>
    <!-- Category Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $this->category }} {{ $this->categoryName }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.emissions.scope_label', ['scope' => $scope]) }}</p>
            </div>
            <div class="flex items-center space-x-3">
                @if(count($sources) > 0)
                    <div class="text-right mr-4">
                        <p class="text-sm text-gray-500">{{ __('carbex.emissions.total') }}</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($this->totalEmissions, 2, ',', ' ') }} <span class="text-sm font-normal text-gray-500">{{ __('carbex.emissions.unit') }}</span></p>
                    </div>
                @endif
                {{-- AI Emission Helper --}}
                <livewire:a-i.emission-helper
                    :scope="$scope"
                    :category-code="$category"
                    :category-name="$this->categoryName"
                />
                <button wire:click="markAsCompleted" type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('carbex.emissions.mark_completed') }}
                </button>
            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
            {{ session('message') }}
        </div>
    @endif

    <!-- Emission Sources -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('carbex.emissions.sources_title') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.emissions.sources_subtitle') }}</p>
            </div>
            @if(count($sources) > 0)
                <button wire:click="openAddSourceForm" type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700" dusk="add-emission-button">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('carbex.emissions.add_source') }}
                </button>
            @endif
        </div>

        @if(count($sources) === 0 && !$showSourceForm)
            <!-- Empty state -->
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('carbex.emissions.no_sources') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.emissions.no_sources_hint') }}</p>
                <div class="mt-6">
                    <button wire:click="openAddSourceForm" type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700" dusk="add-emission-button">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('carbex.emissions.add_source') }}
                    </button>
                </div>
                <p class="mt-2 text-xs text-gray-400">{{ __('carbex.emissions.factors.subtitle', ['count' => '20 000']) }}</p>
            </div>
        @else
            <!-- Sources List -->
            @if(count($sources) > 0)
                <div class="divide-y divide-gray-200" dusk="emissions-list">
                    @foreach($sources as $index => $source)
                        <div class="p-6 hover:bg-gray-50 transition-colors" dusk="emission-row-{{ $index + 1 }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-sm font-medium text-gray-900 truncate">{{ $source['name'] }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $source['factor_name'] }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                                        <span>
                                            <span class="font-medium text-gray-700">{{ number_format($source['quantity'], 2, ',', ' ') }}</span>
                                            {{ $source['unit'] }}
                                        </span>
                                        <span class="text-gray-300">|</span>
                                        <span>
                                            {{ number_format($source['factor_kg_co2e'], 4, ',', ' ') }} {{ __('carbex.emissions.factors.kg_co2e_per') }}{{ $source['unit'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-6 flex items-center gap-4">
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ number_format($source['emissions_kg'] / 1000, 3, ',', ' ') }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ __('carbex.emissions.unit') }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button wire:click="editSource('{{ $source['id'] }}')" type="button" class="p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button wire:click="deleteSource('{{ $source['id'] }}')" wire:confirm="{{ __('carbex.emissions.confirm_delete') }}" type="button" class="p-2 text-gray-400 hover:text-red-600 rounded-md hover:bg-red-50" dusk="delete-emission-button">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Add/Edit Source Form -->
            @if($showSourceForm)
                <div class="p-6 bg-gray-50 border-t border-gray-200" dusk="emission-form-modal">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">
                        {{ $editingSourceId ? __('carbex.emissions.edit_source') : __('carbex.emissions.new_source') }}
                    </h3>
                    <form wire:submit="saveSource" class="space-y-4">
                        <!-- Source Name -->
                        <div>
                            <label for="sourceName" class="block text-sm font-medium text-gray-700">
                                {{ __('carbex.emissions.source_name') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                wire:model="sourceName"
                                type="text"
                                id="sourceName"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm @error('sourceName') border-red-300 @enderror"
                                placeholder="{{ __('carbex.emissions.source_name_placeholder') }}"
                            >
                            @error('sourceName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Emission Factor -->
                            <div dusk="category-selector">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('carbex.emissions.factor') }} <span class="text-red-500">*</span>
                                </label>
                                @if($selectedFactor)
                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-300 rounded-md" dusk="category-options">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $selectedFactor['name'] }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ number_format($selectedFactor['factor_kg_co2e'], 4, ',', ' ') }} {{ __('carbex.emissions.factors.kg_co2e_per') }}{{ $selectedFactor['unit'] }}
                                            </p>
                                        </div>
                                        <button wire:click="openFactorSelector" type="button" class="ml-3 text-sm text-green-600 hover:text-green-700 font-medium">
                                            {{ __('carbex.edit') }}
                                        </button>
                                    </div>
                                @else
                                    <button wire:click="openFactorSelector" type="button" class="w-full flex items-center justify-center px-4 py-3 border-2 border-dashed border-gray-300 rounded-md text-sm font-medium text-gray-600 hover:border-green-500 hover:text-green-600 transition-colors">
                                        <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        {{ __('carbex.emissions.select_factor') }}
                                    </button>
                                @endif
                                @error('selectedFactor')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label for="sourceQuantity" class="block text-sm font-medium text-gray-700">
                                    {{ __('carbex.emissions.quantity') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input
                                        wire:model="sourceQuantity"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        id="sourceQuantity"
                                        class="block w-full pr-16 border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 sm:text-sm @error('sourceQuantity') border-red-300 @enderror"
                                        placeholder="0.00"
                                    >
                                    @if($selectedFactor)
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">{{ $selectedFactor['unit'] }}</span>
                                        </div>
                                    @endif
                                </div>
                                @error('sourceQuantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Calculated Emissions Preview -->
                        @if($selectedFactor && $sourceQuantity)
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg" dusk="emission-preview">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-green-700">{{ __('carbex.emissions.calculated_emissions') }}</span>
                                    <span class="text-lg font-bold text-green-800">
                                        {{ number_format(((float) $sourceQuantity * (float) $selectedFactor['factor_kg_co2e']) / 1000, 3, ',', ' ') }} {{ __('carbex.emissions.unit') }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button wire:click="cancelSourceForm" type="button" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                {{ __('carbex.cancel') }}
                            </button>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                {{ $editingSourceId ? __('carbex.save') : __('carbex.emissions.add_source') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endif
    </div>

    <!-- Factor Selector Modal -->
    <livewire:emissions.factor-selector />
</div>

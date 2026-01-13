<div>
    <!-- Factor Selector Modal -->
    <div
        x-data="{ show: @entangle('isOpen') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-10"
                @click="$wire.close()"
            ></div>

            <!-- Modal panel -->
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative z-20 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
            >
                <!-- Header -->
                <div class="bg-white px-6 pt-5 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                {{ __('carbex.emissions.factors.title') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('carbex.emissions.factors.subtitle', ['count' => number_format($totalCount, 0, ',', ' ')]) }}
                            </p>
                        </div>
                        <button
                            wire:click="close"
                            type="button"
                            class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500"
                        >
                            <span class="sr-only">{{ __('carbex.cancel') }}</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Source Tabs -->
                    <div class="mt-4 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                            @foreach ($tabs as $key => $label)
                                <button
                                    wire:click="setTab('{{ $key }}')"
                                    type="button"
                                    class="whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === $key ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                >
                                    {{ __($label) }}
                                    @if(isset($sourceCounts[$key]))
                                        <span class="ml-2 py-0.5 px-2 rounded-full text-xs {{ $activeTab === $key ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                                            {{ number_format($sourceCounts[$key], 0, ',', ' ') }}
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </nav>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <!-- Semantic Search Toggle & Status -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <!-- Semantic Search Toggle -->
                            <button
                                wire:click="toggleSemanticSearch"
                                type="button"
                                class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 {{ $useSemanticSearch ? 'bg-green-600' : 'bg-gray-200' }}"
                            >
                                <span class="sr-only">{{ __('carbex.emissions.factors.toggle_semantic') }}</span>
                                <span class="inline-block w-4 h-4 transform transition-transform bg-white rounded-full {{ $useSemanticSearch ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                            <span class="text-sm text-gray-600">
                                {{ __('carbex.emissions.factors.semantic_search') }}
                            </span>
                        </div>
                        <!-- Search Mode Indicator -->
                        @if($search && strlen($search) >= 3)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $searchMode === 'semantic' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                @if($searchMode === 'semantic')
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                    {{ __('carbex.emissions.factors.mode_semantic') }}
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3M17 16l4-4m0 0l-4-4m4 4H14" />
                                    </svg>
                                    {{ __('carbex.emissions.factors.mode_text') }}
                                @endif
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Search -->
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    @if($useSemanticSearch)
                                        <svg class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <input
                                    wire:model.live.debounce.300ms="search"
                                    type="text"
                                    class="block w-full pl-10 pr-3 py-2 border rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 sm:text-sm {{ $useSemanticSearch ? 'border-purple-300 focus:ring-purple-500 focus:border-purple-500' : 'border-gray-300 focus:ring-green-500 focus:border-green-500' }}"
                                    placeholder="{{ $useSemanticSearch ? __('carbex.emissions.factors.semantic_placeholder') : __('carbex.emissions.factors.search_placeholder') }}"
                                >
                            </div>
                        </div>

                        <!-- Country Filter -->
                        <div class="w-full sm:w-40">
                            <select
                                wire:model.live="country"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md"
                            >
                                @foreach ($countries as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Unit Filter -->
                        <div class="w-full sm:w-40">
                            <select
                                wire:model.live="unit"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md"
                            >
                                @foreach ($units as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reset Filters -->
                        <button
                            wire:click="resetFilters"
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ __('carbex.emissions.factors.reset') }}
                        </button>
                    </div>
                </div>

                <!-- Factors List -->
                <div class="max-h-96 overflow-y-auto">
                    @if($factors->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach ($factors as $factor)
                                <li
                                    wire:click="selectFactor('{{ $factor->id }}')"
                                    class="px-6 py-4 hover:bg-gray-50 cursor-pointer transition-colors"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $factor->translated_name }}
                                            </p>
                                            @if($factor->description)
                                                <p class="mt-1 text-xs text-gray-500 truncate">
                                                    {{ Str::limit($factor->description, 100) }}
                                                </p>
                                            @endif
                                            <div class="mt-2 flex items-center gap-3 text-xs text-gray-500">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ strtoupper($factor->source) }}
                                                </span>
                                                @if($factor->country)
                                                    <span>{{ $factor->country }}</span>
                                                @endif
                                                <span>{{ $factor->unit }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-shrink-0 text-right">
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ number_format($factor->factor_kg_co2e, 4, ',', ' ') }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ __('carbex.emissions.factors.kg_co2e_per') }} {{ $factor->unit }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('carbex.emissions.factors.no_results') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('carbex.emissions.factors.no_results_hint') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($factors->hasPages())
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                        <p class="text-sm text-gray-700">
                            {{ __('carbex.emissions.factors.showing') }}
                            <span class="font-medium">{{ $factors->firstItem() }}</span>
                            {{ __('carbex.emissions.factors.to') }}
                            <span class="font-medium">{{ $factors->lastItem() }}</span>
                            {{ __('carbex.emissions.factors.of') }}
                            <span class="font-medium">{{ number_format($factors->total(), 0, ',', ' ') }}</span>
                            {{ __('carbex.emissions.factors.results') }}
                        </p>
                        <div>
                            {{ $factors->links() }}
                        </div>
                    </div>
                @endif

                <!-- Footer -->
                <div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between">
                    <button
                        wire:click="openCustomFactorModal"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('carbex.emissions.factors.create_custom') }}
                    </button>
                    <button
                        wire:click="close"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                        {{ __('carbex.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Factor Modal (T040) -->
    <div
        x-data="{ showCustom: @entangle('showCustomFactorModal') }"
        x-show="showCustom"
        x-cloak
        class="fixed inset-0 z-[60] overflow-y-auto"
        aria-labelledby="custom-factor-title"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div
                x-show="showCustom"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-10"
                @click="$wire.closeCustomFactorModal()"
            ></div>

            <!-- Centering spacer -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div
                x-show="showCustom"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative z-20 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            >
                <form wire:submit="createCustomFactor">
                    <!-- Header -->
                    <div class="bg-white px-6 pt-5 pb-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900" id="custom-factor-title">
                                {{ __('carbex.emissions.factors.custom.title') }}
                            </h3>
                            <button
                                wire:click="closeCustomFactorModal"
                                type="button"
                                class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500"
                            >
                                <span class="sr-only">{{ __('carbex.cancel') }}</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('carbex.emissions.factors.custom.subtitle') }}
                        </p>
                    </div>

                    <!-- Form Body -->
                    <div class="px-6 py-4 space-y-4">
                        <!-- Name -->
                        <div>
                            <label for="customName" class="block text-sm font-medium text-gray-700">
                                {{ __('carbex.emissions.factors.custom.name') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                wire:model="customName"
                                type="text"
                                id="customName"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm @error('customName') border-red-300 @enderror"
                                placeholder="{{ __('carbex.emissions.factors.custom.name_placeholder') }}"
                            >
                            @error('customName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="customDescription" class="block text-sm font-medium text-gray-700">
                                {{ __('carbex.emissions.factors.custom.description') }}
                            </label>
                            <textarea
                                wire:model="customDescription"
                                id="customDescription"
                                rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                placeholder="{{ __('carbex.emissions.factors.custom.description_placeholder') }}"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Unit -->
                            <div>
                                <label for="customUnit" class="block text-sm font-medium text-gray-700">
                                    {{ __('carbex.emissions.factors.custom.unit') }} <span class="text-red-500">*</span>
                                </label>
                                <select
                                    wire:model="customUnit"
                                    id="customUnit"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md @error('customUnit') border-red-300 @enderror"
                                >
                                    <option value="kWh">kWh</option>
                                    <option value="MWh">MWh</option>
                                    <option value="L">{{ __('carbex.emissions.factors.units.liter') }}</option>
                                    <option value="m3">m³</option>
                                    <option value="kg">kg</option>
                                    <option value="t">{{ __('carbex.emissions.factors.units.tonne') }}</option>
                                    <option value="km">km</option>
                                    <option value="tkm">tonne.km</option>
                                    <option value="EUR">Euro</option>
                                    <option value="USD">Dollar</option>
                                </select>
                                @error('customUnit')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Factor Value -->
                            <div>
                                <label for="customFactorValue" class="block text-sm font-medium text-gray-700">
                                    {{ __('carbex.emissions.factors.custom.value') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input
                                        wire:model="customFactorValue"
                                        type="number"
                                        step="0.0001"
                                        min="0"
                                        id="customFactorValue"
                                        class="block w-full pr-20 border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 sm:text-sm @error('customFactorValue') border-red-300 @enderror"
                                        placeholder="0.0000"
                                    >
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">kgCO2e</span>
                                    </div>
                                </div>
                                @error('customFactorValue')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="rounded-md bg-blue-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        {{ __('carbex.emissions.factors.custom.info') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                        <button
                            wire:click="closeCustomFactorModal"
                            type="button"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                        >
                            {{ __('carbex.cancel') }}
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('carbex.emissions.factors.custom.create') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

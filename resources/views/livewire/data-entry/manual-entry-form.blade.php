<div class="max-w-4xl mx-auto">
    {{-- Success Message --}}
    @if($showSuccess)
        <x-alert type="success" class="mb-6" dismissible wire:click="$set('showSuccess', false)">
            {{ __('linscarbon.data_entry.success') }}
        </x-alert>
    @endif

    <x-card>
        <x-slot name="header">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ __('linscarbon.data_entry.title') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('linscarbon.data_entry.subtitle') }}
            </p>
        </x-slot>

        <form wire:submit="save" class="space-y-6">
            {{-- Entry Type Selection --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    {{ __('linscarbon.data_entry.activity_type') }}
                </label>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @foreach([
                        'energy' => ['icon' => 'bolt', 'label' => __('linscarbon.data_entry.energy')],
                        'travel' => ['icon' => 'paper-airplane', 'label' => __('linscarbon.data_entry.travel')],
                        'purchase' => ['icon' => 'shopping-cart', 'label' => __('linscarbon.data_entry.purchases')],
                        'waste' => ['icon' => 'trash', 'label' => __('linscarbon.data_entry.waste')],
                        'freight' => ['icon' => 'truck', 'label' => __('linscarbon.data_entry.freight')],
                    ] as $type => $config)
                        <button
                            type="button"
                            wire:click="$set('entryType', '{{ $type }}')"
                            class="flex flex-col items-center p-4 border-2 rounded-lg transition
                                {{ $entryType === $type
                                    ? 'border-green-500 bg-green-50 dark:bg-green-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-green-300' }}"
                        >
                            <x-dynamic-component
                                :component="'heroicon-o-' . $config['icon']"
                                class="w-6 h-6 {{ $entryType === $type ? 'text-green-600' : 'text-gray-400' }}"
                            />
                            <span class="mt-2 text-sm font-medium {{ $entryType === $type ? 'text-green-700 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $config['label'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Site Selection --}}
                <div>
                    <label for="site" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('linscarbon.data_entry.site') }} <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="site"
                        wire:model="siteId"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                    >
                        <option value="">{{ __('linscarbon.data_entry.select_site') }}</option>
                        @foreach($this->sites as $site)
                            <option value="{{ $site->id }}">
                                {{ $site->name }}
                                @if($site->city) ({{ $site->city }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('siteId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Date --}}
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('linscarbon.common.date') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        id="date"
                        wire:model="date"
                        max="{{ now()->toDateString() }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                    >
                    @error('date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Category Selection --}}
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('linscarbon.data_entry.emission_category') }} <span class="text-red-500">*</span>
                </label>
                <select
                    id="category"
                    wire:model.live="categoryId"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                >
                    <option value="">{{ __('linscarbon.data_entry.select_category') }}</option>
                    @php $currentScope = null; @endphp
                    @foreach($this->categories as $category)
                        @if($currentScope !== $category->scope)
                            @if($currentScope !== null)</optgroup>@endif
                            <optgroup label="Scope {{ $category->scope }}">
                            @php $currentScope = $category->scope; @endphp
                        @endif
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                    @if($currentScope !== null)</optgroup>@endif
                </select>
                @error('categoryId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                @if($this->selectedCategory)
                    <p class="mt-2 text-sm text-gray-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            {{ match($this->selectedCategory->scope) {
                                1 => 'bg-green-100 text-green-800',
                                2 => 'bg-blue-100 text-blue-800',
                                3 => 'bg-purple-100 text-purple-800',
                                default => 'bg-gray-100 text-gray-800',
                            } }}">
                            Scope {{ $this->selectedCategory->scope }}
                        </span>
                        <span class="ml-2">{{ $this->selectedCategory->code }}</span>
                    </p>
                @endif
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('linscarbon.common.description') }} <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="description"
                    wire:model="description"
                    rows="2"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                    placeholder="{{ __('linscarbon.data_entry.description_placeholder') }}"
                ></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Quantity and Unit --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('linscarbon.common.quantity') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="quantity"
                        wire:model.live="quantity"
                        step="0.001"
                        min="0"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                        placeholder="0.00"
                    >
                    @error('quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('linscarbon.common.unit') }} <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="unit"
                        wire:model="unit"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                    >
                        @foreach($this->availableUnits as $availableUnit)
                            <option value="{{ $availableUnit }}">{{ $availableUnit }}</option>
                        @endforeach
                    </select>
                    @error('unit') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Additional Fields for Travel --}}
            @if($entryType === 'travel')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div>
                        <label for="origin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('linscarbon.data_entry.origin') }}
                        </label>
                        <input
                            type="text"
                            id="origin"
                            wire:model="origin"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-green-500 focus:border-green-500"
                            placeholder="{{ __('linscarbon.data_entry.origin_placeholder') }}"
                        >
                    </div>
                    <div>
                        <label for="destination" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('linscarbon.data_entry.destination') }}
                        </label>
                        <input
                            type="text"
                            id="destination"
                            wire:model="destination"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-green-500 focus:border-green-500"
                            placeholder="{{ __('linscarbon.data_entry.destination_placeholder') }}"
                        >
                    </div>
                    <div>
                        <label for="travelClass" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('linscarbon.data_entry.travel_class') }}
                        </label>
                        <select
                            id="travelClass"
                            wire:model="travelClass"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-green-500 focus:border-green-500"
                        >
                            <option value="">{{ __('linscarbon.data_entry.standard') }}</option>
                            <option value="economy">{{ __('linscarbon.data_entry.economy') }}</option>
                            <option value="business">{{ __('linscarbon.data_entry.business') }}</option>
                            <option value="first">{{ __('linscarbon.data_entry.first_class') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="passengers" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('linscarbon.data_entry.passengers') }}
                        </label>
                        <input
                            type="number"
                            id="passengers"
                            wire:model="passengers"
                            min="1"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-green-500 focus:border-green-500"
                        >
                    </div>
                </div>
            @endif

            {{-- Additional Fields for Energy --}}
            @if($entryType === 'energy')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div>
                        <label for="fuelType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('linscarbon.data_entry.fuel_type') }}
                        </label>
                        <select
                            id="fuelType"
                            wire:model="fuelType"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-green-500 focus:border-green-500"
                        >
                            <option value="">{{ __('linscarbon.data_entry.not_specified') }}</option>
                            <option value="electricity_grid">{{ __('linscarbon.data_entry.grid_electricity') }}</option>
                            <option value="electricity_renewable">{{ __('linscarbon.data_entry.renewable_electricity') }}</option>
                            <option value="natural_gas">{{ __('linscarbon.data_entry.natural_gas') }}</option>
                            <option value="diesel">{{ __('linscarbon.data_entry.diesel') }}</option>
                            <option value="petrol">{{ __('linscarbon.data_entry.petrol') }}</option>
                            <option value="lpg">{{ __('linscarbon.data_entry.lpg') }}</option>
                            <option value="heating_oil">{{ __('linscarbon.data_entry.heating_oil') }}</option>
                        </select>
                    </div>
                </div>
            @endif

            {{-- Optional Amount --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('linscarbon.common.amount_optional') }}
                    </label>
                    <div class="flex">
                        <input
                            type="number"
                            id="amount"
                            wire:model="amount"
                            step="0.01"
                            min="0"
                            class="flex-1 rounded-l-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                            placeholder="0.00"
                        >
                        <select
                            wire:model="currency"
                            class="rounded-r-lg border-l-0 border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                        >
                            <option value="EUR">EUR</option>
                            <option value="USD">USD</option>
                            <option value="GBP">GBP</option>
                            <option value="CHF">CHF</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Calculate Button --}}
            <div class="flex items-center gap-4">
                <button
                    type="button"
                    wire:click="calculate"
                    wire:loading.attr="disabled"
                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition"
                >
                    <span wire:loading.remove wire:target="calculate">{{ __('linscarbon.data_entry.calculate') }}</span>
                    <span wire:loading wire:target="calculate">{{ __('linscarbon.common.calculating') }}</span>
                </button>

                @error('calculation')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Calculation Result --}}
            @if($calculationResult)
                <div class="p-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <h4 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-4">
                        {{ __('linscarbon.data_entry.calculation_result') }}
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <span class="block text-sm text-green-600 dark:text-green-400">{{ __('linscarbon.data_entry.co2e_kg') }}</span>
                            <span class="text-2xl font-bold text-green-800 dark:text-green-200">
                                {{ number_format($calculationResult['co2e_kg'], 2) }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-sm text-green-600 dark:text-green-400">{{ __('linscarbon.data_entry.co2e_tonnes') }}</span>
                            <span class="text-2xl font-bold text-green-800 dark:text-green-200">
                                {{ number_format($calculationResult['co2e_tonnes'], 4) }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-sm text-green-600 dark:text-green-400">{{ __('linscarbon.data_entry.scope') }}</span>
                            <span class="text-xl font-bold text-green-800 dark:text-green-200">
                                {{ $calculationResult['scope'] }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-sm text-green-600 dark:text-green-400">{{ __('linscarbon.data_entry.methodology') }}</span>
                            <span class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ ucfirst(str_replace('-', ' ', $calculationResult['methodology'])) }}
                            </span>
                        </div>
                    </div>

                    @if(isset($calculationResult['factor_used']))
                        <div class="mt-4 pt-4 border-t border-green-200 dark:border-green-800">
                            <p class="text-sm text-green-700 dark:text-green-300">
                                <strong>{{ __('linscarbon.data_entry.emission_factor') }}</strong>
                                {{ $calculationResult['factor_used']['value'] ?? 'N/A' }}
                                {{ $calculationResult['factor_used']['unit'] ?? '' }}
                                ({{ $calculationResult['factor_used']['source'] ?? 'Default' }})
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Submit Button --}}
            <div class="flex justify-end gap-4 pt-6 border-t dark:border-gray-700">
                <button
                    type="button"
                    wire:click="resetForm"
                    class="px-6 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition"
                >
                    {{ __('linscarbon.common.reset') }}
                </button>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition disabled:opacity-50"
                    {{ !$calculationResult ? 'disabled' : '' }}
                >
                    <span wire:loading.remove wire:target="save">{{ __('linscarbon.data_entry.save_activity') }}</span>
                    <span wire:loading wire:target="save">{{ __('linscarbon.common.saving') }}</span>
                </button>
            </div>
        </form>
    </x-card>
</div>

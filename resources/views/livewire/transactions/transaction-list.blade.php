<div>
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <button wire:click="$set('filter', 'all')"
            class="p-4 rounded-lg text-left transition {{ $filter === 'all' ? 'bg-green-50 dark:bg-green-900/20 border-2 border-green-500' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-green-300' }}">
            <span class="block text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->stats['total']) }}</span>
            <span class="text-sm text-gray-500">{{ __('carbex.transactions.total_transactions') }}</span>
        </button>
        <button wire:click="$set('filter', 'pending')"
            class="p-4 rounded-lg text-left transition {{ $filter === 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-500' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-yellow-300' }}">
            <span class="block text-2xl font-bold text-yellow-600">{{ number_format($this->stats['pending']) }}</span>
            <span class="text-sm text-gray-500">{{ __('carbex.transactions.pending_categorization') }}</span>
        </button>
        <button wire:click="$set('filter', 'low_confidence')"
            class="p-4 rounded-lg text-left transition {{ $filter === 'low_confidence' ? 'bg-orange-50 dark:bg-orange-900/20 border-2 border-orange-500' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-orange-300' }}">
            <span class="block text-2xl font-bold text-orange-600">{{ number_format($this->stats['low_confidence']) }}</span>
            <span class="text-sm text-gray-500">{{ __('carbex.transactions.needs_review') }}</span>
        </button>
        <button wire:click="$set('filter', 'validated')"
            class="p-4 rounded-lg text-left transition {{ $filter === 'validated' ? 'bg-green-50 dark:bg-green-900/20 border-2 border-green-500' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-green-300' }}">
            <span class="block text-2xl font-bold text-green-600">{{ number_format($this->stats['validated']) }}</span>
            <span class="text-sm text-gray-500">{{ __('carbex.transactions.validated') }}</span>
        </button>
    </div>

    <x-card>
        {{-- Filters --}}
        <div class="mb-6 space-y-4">
            <div class="flex flex-wrap gap-4">
                {{-- Search --}}
                <div class="flex-1 min-w-64">
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('carbex.transactions.search_placeholder') }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                    >
                </div>

                {{-- Category Filter --}}
                <select
                    wire:model.live="categoryId"
                    class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                >
                    <option value="">{{ __('carbex.transactions.all_categories') }}</option>
                    @php $currentScope = null; @endphp
                    @foreach($this->categories as $category)
                        @if($currentScope !== $category->scope)
                            @if($currentScope !== null)</optgroup>@endif
                            <optgroup label="Scope {{ $category->scope }}">
                            @php $currentScope = $category->scope; @endphp
                        @endif
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                    @if($currentScope !== null)</optgroup>@endif
                </select>

                {{-- Scope Filter --}}
                <select
                    wire:model.live="scope"
                    class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                >
                    <option value="">{{ __('carbex.transactions.all_scopes') }}</option>
                    <option value="1">Scope 1</option>
                    <option value="2">Scope 2</option>
                    <option value="3">Scope 3</option>
                </select>

                {{-- Date Range --}}
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                >
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                >

                {{-- Reset --}}
                <button
                    wire:click="resetFilters"
                    class="px-4 py-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                >
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            {{-- Bulk Actions --}}
            @if(count($selected) > 0)
                <div class="flex items-center gap-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <span class="text-sm font-medium text-green-700 dark:text-green-400">
                        {{ count($selected) }} {{ __('carbex.transactions.selected') }}
                    </span>
                    <button
                        wire:click="bulkValidate"
                        class="px-3 py-1 text-sm bg-green-600 hover:bg-green-700 text-white rounded transition"
                    >
                        {{ __('carbex.transactions.validate_all') }}
                    </button>
                    <div class="relative" x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            class="px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition"
                        >
                            {{ __('carbex.transactions.categorize_as') }}
                        </button>
                        <div
                            x-show="open"
                            @click.outside="open = false"
                            class="absolute left-0 mt-1 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700 z-50 max-h-64 overflow-y-auto"
                            style="display: none;"
                        >
                            @foreach($this->categories as $category)
                                <button
                                    wire:click="bulkCategorize('{{ $category->id }}')"
                                    @click="open = false"
                                    class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    <span class="inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full mr-2
                                            {{ match($category->scope) { 1 => 'bg-green-500', 2 => 'bg-blue-500', 3 => 'bg-purple-500', default => 'bg-gray-500' } }}">
                                        </span>
                                        {{ $category->name }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Transactions Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 w-10">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                            >
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:text-gray-700"
                            wire:click="sortBy('date')">
                            {{ __('carbex.transactions.date') }}
                            @if($sortField === 'date')
                                <x-heroicon-s-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} class="w-4 h-4 inline" />
                            @endif
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            {{ __('carbex.transactions.description') }}
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            {{ __('carbex.transactions.category') }}
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase cursor-pointer hover:text-gray-700"
                            wire:click="sortBy('amount')">
                            {{ __('carbex.transactions.amount') }}
                            @if($sortField === 'amount')
                                <x-heroicon-s-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} class="w-4 h-4 inline" />
                            @endif
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                            {{ __('carbex.transactions.emissions') }}
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                            {{ __('carbex.transactions.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->transactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $transaction->is_excluded ? 'opacity-50' : '' }}">
                            <td class="px-4 py-3">
                                <input
                                    type="checkbox"
                                    wire:model.live="selected"
                                    value="{{ $transaction->id }}"
                                    class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                >
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900 dark:text-white truncate max-w-xs">
                                    {{ $transaction->clean_description ?? $transaction->description }}
                                </div>
                                @if($transaction->merchant_name)
                                    <div class="text-xs text-gray-500">{{ $transaction->merchant_name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($editingId === $transaction->id)
                                    <div class="flex items-center gap-2">
                                        <select
                                            wire:model="editCategoryId"
                                            class="text-sm rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500"
                                        >
                                            <option value="">{{ __('carbex.transactions.select') }}</option>
                                            @foreach($this->categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <button wire:click="saveCategory('{{ $transaction->id }}')" class="text-green-600 hover:text-green-700">
                                            <x-heroicon-s-check class="w-5 h-5" />
                                        </button>
                                        <button wire:click="cancelEdit" class="text-gray-400 hover:text-gray-600">
                                            <x-heroicon-s-x-mark class="w-5 h-5" />
                                        </button>
                                    </div>
                                @else
                                    @if($transaction->category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium cursor-pointer hover:opacity-80
                                            {{ match($transaction->category->scope) {
                                                1 => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                2 => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                                3 => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                                            } }}"
                                            wire:click="startEdit('{{ $transaction->id }}')"
                                        >
                                            {{ $transaction->category->name }}
                                            @if($transaction->confidence && $transaction->confidence < 0.8)
                                                <x-heroicon-s-exclamation-triangle class="w-3 h-3 ml-1 text-yellow-500" title="{{ __('carbex.transactions.low_confidence') }}" />
                                            @endif
                                        </span>
                                    @else
                                        <button
                                            wire:click="startEdit('{{ $transaction->id }}')"
                                            class="text-sm text-gray-400 hover:text-gray-600"
                                        >
                                            {{ __('carbex.transactions.add_category') }}
                                        </button>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm {{ $transaction->amount < 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($transaction->amount, 2, ',', ' ') }} {{ $transaction->currency }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-900 dark:text-white">
                                @if($transaction->emissionRecord)
                                    {{ number_format($transaction->emissionRecord->co2e_kg, 1, ',', ' ') }} kg
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if(!$transaction->validated_at && $transaction->category_id)
                                        <button
                                            wire:click="validateTransaction('{{ $transaction->id }}')"
                                            class="p-1 text-green-600 hover:text-green-700"
                                            title="{{ __('carbex.transactions.validate') }}"
                                        >
                                            <x-heroicon-o-check-circle class="w-5 h-5" />
                                        </button>
                                    @endif

                                    @if($transaction->validated_at)
                                        <span class="p-1 text-green-500" title="{{ __('carbex.transactions.validated') }}">
                                            <x-heroicon-s-check-badge class="w-5 h-5" />
                                        </span>
                                    @endif

                                    @if($transaction->category_id && $transaction->merchant_name)
                                        <button
                                            wire:click="createMerchantRule('{{ $transaction->id }}')"
                                            class="p-1 text-blue-600 hover:text-blue-700"
                                            title="{{ __('carbex.transactions.create_rule') }}"
                                        >
                                            <x-heroicon-o-bookmark class="w-5 h-5" />
                                        </button>
                                    @endif

                                    @if(!$transaction->is_excluded)
                                        <button
                                            wire:click="excludeTransaction('{{ $transaction->id }}')"
                                            wire:confirm="{{ __('carbex.transactions.exclude_confirm') }}"
                                            class="p-1 text-gray-400 hover:text-red-600"
                                            title="{{ __('carbex.transactions.exclude') }}"
                                        >
                                            <x-heroicon-o-eye-slash class="w-5 h-5" />
                                        </button>
                                    @else
                                        <button
                                            wire:click="includeTransaction('{{ $transaction->id }}')"
                                            class="p-1 text-gray-400 hover:text-green-600"
                                            title="{{ __('carbex.transactions.include') }}"
                                        >
                                            <x-heroicon-o-eye class="w-5 h-5" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                <x-heroicon-o-document-magnifying-glass class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                                <p>{{ __('carbex.transactions.no_transactions') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $this->transactions->links() }}
        </div>
    </x-card>
</div>

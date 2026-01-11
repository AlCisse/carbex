<div>
    {{-- Import Header --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('carbex.sites.import.title') }}
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('carbex.sites.import.description') }}
        </p>
    </div>

    @if(!$showResults)
        {{-- Step 1: Upload --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm mb-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-medium text-gray-900 dark:text-white">
                    1. {{ __('carbex.sites.import.upload_file') }}
                </h4>
                <button wire:click="downloadTemplate" type="button"
                        class="text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    {{ __('carbex.sites.import.download_template') }}
                </button>
            </div>

            <div class="space-y-4">
                {{-- File Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('carbex.sites.import.csv_file') }}
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                @if($csvFile)
                                    <svg class="w-8 h-8 mb-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $csvFile->getClientOriginalName() }}</p>
                                @else
                                    <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-semibold">{{ __('carbex.sites.import.click_to_upload') }}</span>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">CSV (max 5MB)</p>
                                @endif
                            </div>
                            <input type="file" wire:model="csvFile" class="hidden" accept=".csv,.txt" />
                        </label>
                    </div>
                    @error('csvFile') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Header Option --}}
                <div class="flex items-center">
                    <input type="checkbox" wire:model.live="hasHeader" id="hasHeader"
                           class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                    <label for="hasHeader" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ __('carbex.sites.import.has_header') }}
                    </label>
                </div>
            </div>
        </div>

        {{-- Step 2: Column Mapping --}}
        @if($showMapping && count($previewData) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm mb-6">
                <h4 class="font-medium text-gray-900 dark:text-white mb-4">
                    2. {{ __('carbex.sites.import.map_columns') }}
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    @foreach(['name' => __('carbex.sites.name'), 'code' => __('carbex.sites.code'), 'type' => __('carbex.sites.type'), 'address_line_1' => __('carbex.sites.address'), 'city' => __('carbex.sites.city'), 'postal_code' => __('carbex.sites.postal_code'), 'country' => __('carbex.sites.country'), 'floor_area_m2' => __('carbex.sites.floor_area'), 'employee_count' => __('carbex.sites.employees'), 'energy_rating' => __('carbex.sites.energy_rating'), 'construction_year' => __('carbex.sites.construction_year'), 'heating_type' => __('carbex.sites.heating_type')] as $field => $label)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                {{ $label }}
                                @if($field === 'name')
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <select wire:change="setColumnMapping('{{ $field }}', $event.target.value)"
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">-- {{ __('carbex.sites.import.skip') }} --</option>
                                @foreach($previewData[0] ?? [] as $index => $value)
                                    <option value="{{ $index }}" {{ ($columnMapping[$field] ?? null) === $index ? 'selected' : '' }}>
                                        {{ __('carbex.sites.import.column') }} {{ $index + 1 }}: {{ Str::limit($value, 20) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                {{-- Preview Table --}}
                <div class="overflow-x-auto">
                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('carbex.sites.import.preview') }} ({{ count($previewData) }} {{ __('carbex.sites.import.rows') }})
                    </h5>
                    <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                @foreach($previewData[0] ?? [] as $index => $value)
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                                        {{ __('carbex.sites.import.col') }} {{ $index + 1 }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach(array_slice($previewData, 0, 5) as $row)
                                <tr>
                                    @foreach($row as $cell)
                                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300 truncate max-w-xs">
                                            {{ $cell }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @error('mapping') <p class="mt-4 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Import Button --}}
            <div class="flex items-center justify-end space-x-3">
                <button wire:click="resetImport" type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                    {{ __('carbex.common.cancel') }}
                </button>
                <button wire:click="import" type="button"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg disabled:opacity-50 flex items-center">
                    <span wire:loading.remove wire:target="import">
                        {{ __('carbex.sites.import.import_button') }}
                    </span>
                    <span wire:loading wire:target="import" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('carbex.sites.import.importing') }}
                    </span>
                </button>
            </div>
        @endif
    @else
        {{-- Results --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
            <div class="text-center mb-6">
                @if($importedCount > 0)
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full mb-4">
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('carbex.sites.import.success_title') }}
                    </h4>
                @else
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full mb-4">
                        <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('carbex.sites.import.no_imports') }}
                    </h4>
                @endif
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $importedCount }}</p>
                    <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ __('carbex.sites.import.imported') }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $skippedCount }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('carbex.sites.import.skipped') }}</p>
                </div>
            </div>

            {{-- Errors --}}
            @if(count($importErrors) > 0)
                <div class="mb-6">
                    <h5 class="text-sm font-medium text-red-600 dark:text-red-400 mb-2">
                        {{ __('carbex.sites.import.errors') }} ({{ count($importErrors) }})
                    </h5>
                    <div class="max-h-40 overflow-y-auto">
                        <ul class="text-sm text-red-600 dark:text-red-400 space-y-1">
                            @foreach(array_slice($importErrors, 0, 10) as $error)
                                <li>{{ __('carbex.sites.import.row') }} {{ $error['row'] }}: {{ $error['message'] }}</li>
                            @endforeach
                            @if(count($importErrors) > 10)
                                <li class="text-gray-500">... {{ count($importErrors) - 10 }} {{ __('carbex.sites.import.more_errors') }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center justify-center space-x-3">
                <button wire:click="resetImport" type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                    {{ __('carbex.sites.import.import_more') }}
                </button>
                <a href="{{ route('settings.sites') }}"
                   class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg">
                    {{ __('carbex.sites.import.view_sites') }}
                </a>
            </div>
        </div>
    @endif
</div>

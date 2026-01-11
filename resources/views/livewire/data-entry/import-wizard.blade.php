<div class="max-w-4xl mx-auto">
    {{-- Progress Steps --}}
    <nav aria-label="Progress" class="mb-8">
        <ol class="flex items-center justify-center space-x-5">
            @foreach([1 => __('carbex.import.upload'), 2 => __('carbex.import.map_columns'), 3 => __('carbex.import.validate'), 4 => __('carbex.import.import')] as $stepNum => $stepName)
                <li class="flex items-center">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium
                        {{ $step >= $stepNum ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                        @if($step > $stepNum)
                            <x-heroicon-s-check class="w-5 h-5" />
                        @else
                            {{ $stepNum }}
                        @endif
                    </span>
                    <span class="ml-2 text-sm {{ $step >= $stepNum ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}">
                        {{ $stepName }}
                    </span>
                    @if($stepNum < 4)
                        <x-heroicon-s-chevron-right class="w-5 h-5 ml-5 text-gray-400" />
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    {{-- Error Message --}}
    @if($errorMessage)
        <x-alert type="error" class="mb-6">
            {{ $errorMessage }}
        </x-alert>
    @endif

    {{-- Step 1: Upload File --}}
    @if($step === 1)
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">{{ __('carbex.import.upload_data_file') }}</h3>
                <p class="text-sm text-gray-500">{{ __('carbex.import.import_desc') }}</p>
            </x-slot>

            <div class="space-y-6">
                {{-- Import Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        {{ __('carbex.import.import_type') }}
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach([
                            'transactions' => ['icon' => 'credit-card', 'label' => __('carbex.import.bank_transactions'), 'desc' => __('carbex.import.csv_export')],
                            'activities' => ['icon' => 'clipboard-document-list', 'label' => __('carbex.import.activities'), 'desc' => __('carbex.import.activities_desc')],
                            'fec' => ['icon' => 'document-text', 'label' => __('carbex.import.fec_france'), 'desc' => __('carbex.import.fec_desc')],
                        ] as $type => $config)
                            <button
                                type="button"
                                wire:click="$set('importType', '{{ $type }}')"
                                class="flex flex-col p-4 border-2 rounded-lg text-left transition
                                    {{ $importType === $type
                                        ? 'border-green-500 bg-green-50 dark:bg-green-900/20'
                                        : 'border-gray-200 dark:border-gray-700 hover:border-green-300' }}"
                            >
                                <x-dynamic-component
                                    :component="'heroicon-o-' . $config['icon']"
                                    class="w-8 h-8 {{ $importType === $type ? 'text-green-600' : 'text-gray-400' }} mb-2"
                                />
                                <span class="font-medium {{ $importType === $type ? 'text-green-700 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                                    {{ $config['label'] }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $config['desc'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Site Selection --}}
                <div>
                    <label for="site" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('carbex.import.target_site') }}
                    </label>
                    <select
                        id="site"
                        wire:model="siteId"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500"
                    >
                        <option value="">{{ __('carbex.import.select_site') }}</option>
                        @foreach($this->sites as $site)
                            <option value="{{ $site->id }}">{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- File Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('carbex.import.data_file') }}
                    </label>
                    <div
                        x-data="{ dragging: false }"
                        x-on:dragover.prevent="dragging = true"
                        x-on:dragleave.prevent="dragging = false"
                        x-on:drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                        class="border-2 border-dashed rounded-lg p-8 text-center transition"
                        :class="dragging ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-700'"
                    >
                        <input
                            type="file"
                            x-ref="fileInput"
                            wire:model="file"
                            accept=".csv,.txt,.xlsx,.xls"
                            class="hidden"
                            id="file-upload"
                        >
                        <label for="file-upload" class="cursor-pointer">
                            <x-heroicon-o-cloud-arrow-up class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ __('carbex.import.drag_drop') }}
                                <span class="text-green-600 hover:text-green-700 font-medium">{{ __('carbex.import.browse') }}</span>
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                {{ __('carbex.import.file_types') }}
                            </p>
                        </label>

                        @if($file)
                            <div class="mt-4 p-3 bg-gray-100 dark:bg-gray-800 rounded-lg inline-flex items-center">
                                <x-heroicon-o-document class="w-5 h-5 text-gray-500 mr-2" />
                                <span class="text-sm font-medium">{{ $file->getClientOriginalName() }}</span>
                                <span class="text-sm text-gray-500 ml-2">({{ number_format($file->getSize() / 1024, 1) }} KB)</span>
                            </div>
                        @endif

                        <div wire:loading wire:target="file" class="mt-4">
                            <x-heroicon-o-arrow-path class="w-6 h-6 mx-auto text-green-600 animate-spin" />
                            <p class="text-sm text-gray-500">{{ __('carbex.import.uploading') }}</p>
                        </div>
                    </div>
                    @error('file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Analyze Button --}}
                <div class="flex justify-end">
                    <button
                        type="button"
                        wire:click="analyzeFile"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition disabled:opacity-50"
                        {{ !$file || !$siteId ? 'disabled' : '' }}
                    >
                        <span wire:loading.remove wire:target="analyzeFile">{{ __('carbex.import.analyze_file') }}</span>
                        <span wire:loading wire:target="analyzeFile">{{ __('carbex.import.analyzing') }}</span>
                    </button>
                </div>
            </div>
        </x-card>
    @endif

    {{-- Step 2: Map Columns --}}
    @if($step === 2)
        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('carbex.import.map_columns_title') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('carbex.import.map_columns_desc') }}</p>
                    </div>
                    <button wire:click="previousStep" class="text-sm text-gray-500 hover:text-gray-700">
                        &larr; {{ __('carbex.back') }}
                    </button>
                </div>
            </x-slot>

            <div class="space-y-6">
                {{-- File Info --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>{{ __('carbex.import.detected') }}</strong>
                        {{ $totalRows }} {{ __('carbex.import.rows') }},
                        {{ count($headers) }} {{ __('carbex.import.columns') }}
                    </p>
                </div>

                {{-- Column Mapping --}}
                <div class="space-y-4">
                    @foreach($this->requiredColumns as $field => $config)
                        <div class="flex items-center gap-4">
                            <div class="w-1/3">
                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                    {{ $config['label'] }}
                                    @if($config['required'])
                                        <span class="text-red-500">*</span>
                                    @endif
                                </span>
                                <span class="block text-xs text-gray-500">{{ $config['type'] }}</span>
                            </div>
                            <div class="w-2/3">
                                <select
                                    wire:model="columnMapping.{{ $field }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-green-500 focus:border-green-500 text-sm"
                                >
                                    <option value="">{{ __('carbex.import.select_column') }}</option>
                                    @foreach($headers as $header)
                                        <option value="{{ $header }}">{{ $header }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Sample Data Preview --}}
                @if(count($sampleRows) > 0)
                    <div>
                        <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('carbex.import.sample_data') }}</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        @foreach($headers as $header)
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                {{ $header }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach(array_slice($sampleRows, 0, 3) as $row)
                                        <tr>
                                            @foreach($row as $cell)
                                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400 truncate max-w-xs">
                                                    {{ $cell }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Continue Button --}}
                <div class="flex justify-end gap-4 pt-4 border-t dark:border-gray-700">
                    <button
                        type="button"
                        wire:click="validateMapping"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition"
                    >
                        <span wire:loading.remove wire:target="validateMapping">{{ __('carbex.import.validate_mapping') }}</span>
                        <span wire:loading wire:target="validateMapping">{{ __('carbex.import.validating') }}</span>
                    </button>
                </div>
            </div>
        </x-card>
    @endif

    {{-- Step 3: Validate --}}
    @if($step === 3)
        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('carbex.import.validation_results') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('carbex.import.review_before_import') }}</p>
                    </div>
                    <button wire:click="previousStep" class="text-sm text-gray-500 hover:text-gray-700">
                        &larr; {{ __('carbex.back') }}
                    </button>
                </div>
            </x-slot>

            <div class="space-y-6">
                {{-- Summary --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg text-center">
                        <span class="block text-2xl font-bold text-gray-900 dark:text-white">{{ $totalRows }}</span>
                        <span class="text-sm text-gray-500">{{ __('carbex.import.total_rows') }}</span>
                    </div>
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center">
                        <span class="block text-2xl font-bold text-green-600">{{ $validRowCount }}</span>
                        <span class="text-sm text-green-600">{{ __('carbex.import.valid') }}</span>
                    </div>
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg text-center">
                        <span class="block text-2xl font-bold text-red-600">{{ $invalidRowCount }}</span>
                        <span class="text-sm text-red-600">{{ __('carbex.import.invalid') }}</span>
                    </div>
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-center">
                        <span class="block text-2xl font-bold text-yellow-600">{{ count($validationWarnings) }}</span>
                        <span class="text-sm text-yellow-600">{{ __('carbex.import.warnings') }}</span>
                    </div>
                </div>

                {{-- Errors --}}
                @if(count($validationErrors) > 0)
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <h4 class="font-medium text-red-800 dark:text-red-200 mb-2">
                            <x-heroicon-s-exclamation-triangle class="w-5 h-5 inline mr-1" />
                            {{ __('carbex.import.validation_errors') }}
                        </h4>
                        <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                            @foreach(array_slice($validationErrors, 0, 10) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                            @if(count($validationErrors) > 10)
                                <li class="font-medium">{{ __('carbex.import.and_more', ['count' => count($validationErrors) - 10]) }}</li>
                            @endif
                        </ul>
                    </div>
                @endif

                {{-- Warnings --}}
                @if(count($validationWarnings) > 0)
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <h4 class="font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                            <x-heroicon-s-exclamation-circle class="w-5 h-5 inline mr-1" />
                            {{ __('carbex.import.warnings') }}
                        </h4>
                        <ul class="list-disc list-inside text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                            @foreach(array_slice($validationWarnings, 0, 5) as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Import Button --}}
                <div class="flex justify-end gap-4 pt-4 border-t dark:border-gray-700">
                    <button
                        type="button"
                        wire:click="resetWizard"
                        class="px-6 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition"
                    >
                        {{ __('carbex.cancel') }}
                    </button>
                    <button
                        type="button"
                        wire:click="startImport"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition disabled:opacity-50"
                        {{ $invalidRowCount > 0 && $validRowCount === 0 ? 'disabled' : '' }}
                    >
                        <span wire:loading.remove wire:target="startImport">
                            {{ __('carbex.import.import_rows', ['count' => $validRowCount]) }}
                        </span>
                        <span wire:loading wire:target="startImport">{{ __('carbex.import.starting') }}</span>
                    </button>
                </div>
            </div>
        </x-card>
    @endif

    {{-- Step 4: Import Complete --}}
    @if($step === 4)
        <x-card class="text-center py-12">
            @if($importStarted)
                <div class="text-green-600 mb-4">
                    <x-heroicon-s-check-circle class="w-20 h-20 mx-auto" />
                </div>
                <h3 class="text-2xl font-bold mb-2">{{ __('carbex.import.import_started') }}</h3>
                <p class="text-gray-500 mb-6">
                    {{ __('carbex.import.processing_background') }}
                </p>

                <div class="flex justify-center space-x-4">
                    <button wire:click="resetWizard" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">
                        {{ __('carbex.import.import_another') }}
                    </button>
                    <a href="{{ route('dashboard') }}" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                        {{ __('carbex.import.go_to_dashboard') }}
                    </a>
                </div>
            @endif
        </x-card>
    @endif
</div>

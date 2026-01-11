<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('carbex.documents.title') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.documents.subtitle') }}</p>
            </div>
            <button
                wire:click="$toggle('showUploadForm')"
                type="button"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
            >
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                {{ __('carbex.documents.upload') }}
            </button>
        </div>
    </div>

    <!-- Messages -->
    @if($errorMessage)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 flex items-center justify-between">
            <span>{{ $errorMessage }}</span>
            <button wire:click="$set('errorMessage', null)" class="text-red-500 hover:text-red-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if($successMessage)
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 flex items-center justify-between">
            <span>{{ $successMessage }}</span>
            <button wire:click="$set('successMessage', null)" class="text-green-500 hover:text-green-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Upload Form -->
    @if($showUploadForm)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('carbex.documents.new_upload') }}</h3>

            <form wire:submit="upload" class="space-y-4">
                <!-- Drag & Drop Zone -->
                <div
                    x-data="{ dragging: false }"
                    x-on:dragover.prevent="dragging = true"
                    x-on:dragleave.prevent="dragging = false"
                    x-on:drop.prevent="
                        dragging = false;
                        const file = $event.dataTransfer.files[0];
                        if (file) {
                            $wire.upload('file', file);
                        }
                    "
                    class="relative"
                >
                    <label
                        :class="dragging ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-green-500'"
                        class="flex justify-center w-full h-48 px-4 transition border-2 border-dashed rounded-lg appearance-none cursor-pointer focus:outline-none"
                    >
                        <span class="flex flex-col items-center justify-center space-y-2">
                            @if($file)
                                <svg class="w-12 h-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium text-gray-700">{{ $file->getClientOriginalName() }}</span>
                                <span class="text-sm text-gray-500">{{ number_format($file->getSize() / 1024 / 1024, 2) }} MB</span>
                            @else
                                <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span class="font-medium text-gray-700">{{ __('carbex.documents.drop_files') }}</span>
                                <span class="text-sm text-gray-500">{{ __('carbex.documents.or_click') }}</span>
                                <span class="text-xs text-gray-400">PDF, Images, Excel, CSV (max 10MB)</span>
                            @endif
                        </span>
                        <input
                            wire:model="file"
                            type="file"
                            class="hidden"
                            accept=".pdf,.jpg,.jpeg,.png,.webp,.heic,.xlsx,.xls,.csv"
                        >
                    </label>

                    <!-- Upload Progress -->
                    <div wire:loading wire:target="file" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 rounded-lg">
                        <div class="flex flex-col items-center">
                            <svg class="animate-spin h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="mt-2 text-sm text-gray-600">{{ __('carbex.documents.uploading') }}...</span>
                        </div>
                    </div>
                </div>

                @error('file')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Document Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('carbex.documents.type') }}
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @foreach($this->documentTypes as $type => $label)
                            <label
                                class="relative flex items-center justify-center p-3 border rounded-lg cursor-pointer transition-colors
                                    {{ $documentType === $type ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 hover:border-green-300' }}"
                            >
                                <input
                                    wire:model="documentType"
                                    type="radio"
                                    name="documentType"
                                    value="{{ $type }}"
                                    class="sr-only"
                                >
                                <span class="text-sm font-medium text-center">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3">
                    <button
                        wire:click="$set('showUploadForm', false)"
                        type="button"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                    >
                        {{ __('carbex.cancel') }}
                    </button>
                    <button
                        type="submit"
                        @disabled(!$file)
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="upload">{{ __('carbex.documents.process') }}</span>
                        <span wire:loading wire:target="upload">{{ __('carbex.documents.processing') }}...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Documents List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('carbex.documents.list') }}</h3>
        </div>

        @if(empty($documents))
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('carbex.documents.no_documents') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('carbex.documents.no_documents_hint') }}</p>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($documents as $document)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <!-- Document Info -->
                            <div class="flex items-center min-w-0 flex-1">
                                <!-- Icon -->
                                <div class="flex-shrink-0 mr-4">
                                    @if(str_starts_with($document['mime_type'], 'image/'))
                                        <svg class="h-10 w-10 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @elseif($document['mime_type'] === 'application/pdf')
                                        <svg class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    @else
                                        <svg class="h-10 w-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    @endif
                                </div>

                                <!-- Details -->
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $document['filename'] }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $document['status_color'] }}-100 text-{{ $document['status_color'] }}-800">
                                            {{ $document['status_label'] }}
                                        </span>
                                        @if($document['is_validated'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ __('carbex.documents.validated') }}
                                            </span>
                                        @endif
                                        @if($document['emission_created'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ __('carbex.documents.emission_linked') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1 flex items-center gap-4 text-xs text-gray-500">
                                        <span>{{ $document['type_label'] }}</span>
                                        <span>{{ $document['file_size'] }}</span>
                                        <span>{{ $document['created_at'] }}</span>
                                        @if($document['confidence'])
                                            <span class="flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                                </svg>
                                                {{ $document['confidence'] }}% {{ __('carbex.documents.confidence') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 ml-4">
                                @if($document['status'] === 'processing')
                                    <span class="flex items-center text-sm text-blue-600">
                                        <svg class="animate-spin mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ __('carbex.documents.processing') }}...
                                    </span>
                                @else
                                    <button
                                        wire:click="selectDocument('{{ $document['id'] }}')"
                                        type="button"
                                        class="p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100"
                                        title="{{ __('carbex.documents.view') }}"
                                    >
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    @if(in_array($document['status'], ['completed', 'needs_review']) && !$document['is_validated'])
                                        <button
                                            wire:click="openValidation('{{ $document['id'] }}')"
                                            type="button"
                                            class="p-2 text-gray-400 hover:text-green-600 rounded-md hover:bg-green-50"
                                            title="{{ __('carbex.documents.validate') }}"
                                        >
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    @endif

                                    @if($document['status'] === 'completed' && $document['is_validated'] && !$document['emission_created'])
                                        <button
                                            wire:click="createEmission('{{ $document['id'] }}')"
                                            type="button"
                                            class="p-2 text-gray-400 hover:text-purple-600 rounded-md hover:bg-purple-50"
                                            title="{{ __('carbex.documents.create_emission') }}"
                                        >
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    @endif

                                    @if($document['can_reprocess'])
                                        <button
                                            wire:click="reprocess('{{ $document['id'] }}')"
                                            type="button"
                                            class="p-2 text-gray-400 hover:text-blue-600 rounded-md hover:bg-blue-50"
                                            title="{{ __('carbex.documents.reprocess') }}"
                                        >
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>
                                    @endif

                                    <button
                                        wire:click="deleteDocument('{{ $document['id'] }}')"
                                        wire:confirm="{{ __('carbex.documents.confirm_delete') }}"
                                        type="button"
                                        class="p-2 text-gray-400 hover:text-red-600 rounded-md hover:bg-red-50"
                                        title="{{ __('carbex.delete') }}"
                                    >
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Extracted Data Preview (when selected) -->
                        @if($selectedDocumentId === $document['id'] && $document['extracted_data'])
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">{{ __('carbex.documents.extracted_data') }}</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    @if(isset($document['extracted_data']['supplier_name']))
                                        <div>
                                            <p class="text-gray-500">{{ __('carbex.documents.supplier') }}</p>
                                            <p class="font-medium text-gray-900">{{ $document['extracted_data']['supplier_name'] }}</p>
                                        </div>
                                    @endif
                                    @if(isset($document['extracted_data']['date']))
                                        <div>
                                            <p class="text-gray-500">{{ __('carbex.documents.date') }}</p>
                                            <p class="font-medium text-gray-900">{{ $document['extracted_data']['date'] }}</p>
                                        </div>
                                    @endif
                                    @if(isset($document['extracted_data']['total_amount']))
                                        <div>
                                            <p class="text-gray-500">{{ __('carbex.documents.amount') }}</p>
                                            <p class="font-medium text-gray-900">{{ number_format($document['extracted_data']['total_amount'], 2, ',', ' ') }} EUR</p>
                                        </div>
                                    @endif
                                    @if(isset($document['extracted_data']['suggested_category']))
                                        <div>
                                            <p class="text-gray-500">{{ __('carbex.documents.category') }}</p>
                                            <p class="font-medium text-gray-900">{{ $document['extracted_data']['suggested_category'] }}</p>
                                        </div>
                                    @endif
                                </div>

                                @if(isset($document['extracted_data']['line_items']) && count($document['extracted_data']['line_items']) > 0)
                                    <div class="mt-4">
                                        <p class="text-gray-500 mb-2">{{ __('carbex.documents.line_items') }}</p>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('carbex.documents.description') }}</th>
                                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('carbex.documents.quantity') }}</th>
                                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('carbex.documents.unit') }}</th>
                                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('carbex.documents.amount') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    @foreach($document['extracted_data']['line_items'] as $item)
                                                        <tr>
                                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $item['description'] ?? '-' }}</td>
                                                            <td class="px-3 py-2 text-sm text-gray-900 text-right">{{ $item['quantity'] ?? '-' }}</td>
                                                            <td class="px-3 py-2 text-sm text-gray-900 text-right">{{ $item['unit'] ?? '-' }}</td>
                                                            <td class="px-3 py-2 text-sm text-gray-900 text-right">{{ isset($item['amount']) ? number_format($item['amount'], 2, ',', ' ') : '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Validation Modal -->
    @if($showValidationModal && $validatingDocument)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeValidation"></div>

                <!-- Modal -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('carbex.documents.validate_data') }}</h3>
                            <button wire:click="closeValidation" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <p class="text-sm text-gray-500 mb-4">
                            {{ __('carbex.documents.validation_instructions') }}
                        </p>

                        @if($validatingDocument['confidence'])
                            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-yellow-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm text-yellow-700">
                                        {{ __('carbex.documents.confidence_level') }}: {{ $validatingDocument['confidence'] }}%
                                    </span>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            @foreach($correctedData as $key => $value)
                                @if(!is_array($value))
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ __("carbex.documents.fields.{$key}") }}
                                        </label>
                                        <input
                                            wire:model="correctedData.{{ $key }}"
                                            type="text"
                                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                        >
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="validateDocument"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            {{ __('carbex.documents.confirm_validation') }}
                        </button>
                        <button
                            wire:click="closeValidation"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm"
                        >
                            {{ __('carbex.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Auto-refresh for processing documents -->
    @if(collect($documents)->where('status', 'processing')->count() > 0)
        <div wire:poll.5s="loadDocuments"></div>
    @endif
</div>

<div class="space-y-6">
    {{-- Header --}}
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
            {{ __('linscarbon.gdpr.title') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('linscarbon.gdpr.subtitle') }}
        </p>
    </div>

    {{-- Consent Management (Art. 7 DSGVO) --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            {{ __('linscarbon.gdpr.consent_title') }}
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            {{ __('linscarbon.gdpr.consent_description') }}
        </p>

        <div class="space-y-4">
            {{-- Marketing Consent --}}
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">
                        {{ __('linscarbon.gdpr.consent_marketing') }}
                    </h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('linscarbon.gdpr.legal_basis_consent') }}
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model="marketingConsent"
                        wire:change="updateConsent('marketing')"
                        class="sr-only peer"
                    >
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                </label>
            </div>

            {{-- Analytics Consent --}}
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">
                        {{ __('linscarbon.gdpr.consent_analytics') }}
                    </h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('linscarbon.gdpr.legal_basis_legitimate') }}
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model="analyticsConsent"
                        wire:change="updateConsent('analytics')"
                        class="sr-only peer"
                    >
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                </label>
            </div>

            {{-- AI Consent --}}
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">
                        {{ __('linscarbon.gdpr.consent_ai') }}
                    </h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('linscarbon.gdpr.legal_basis_consent') }}
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model="aiConsent"
                        wire:change="updateConsent('ai')"
                        class="sr-only peer"
                    >
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                </label>
            </div>
        </div>
    </div>

    {{-- Data Export (Art. 20 DSGVO) --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('linscarbon.gdpr.export_data') }}
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('linscarbon.gdpr.export_description') }}
                </p>

                <div class="mt-4 flex flex-wrap gap-3">
                    @if($exportPath)
                        <a
                            href="{{ Storage::url($exportPath) }}"
                            download
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            {{ __('linscarbon.gdpr.export_button') }}
                        </a>
                    @else
                        <button
                            wire:click="exportData"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            <span wire:loading.remove wire:target="exportData">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ __('linscarbon.gdpr.export_button') }}
                            </span>
                            <span wire:loading wire:target="exportData">
                                <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('linscarbon.gdpr.export_processing') }}
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Data Rights Summary --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            {{ __('linscarbon.gdpr.access_title') }}
        </h3>

        <div class="grid gap-4 sm:grid-cols-2">
            {{-- Right of Access --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-white text-sm">
                    Art. 15 DSGVO
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('linscarbon.gdpr.access_description') }}
                </p>
            </div>

            {{-- Right to Rectification --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-white text-sm">
                    Art. 16 DSGVO
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('linscarbon.gdpr.rectification_description') }}
                </p>
            </div>

            {{-- Right to Data Portability --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-white text-sm">
                    Art. 20 DSGVO
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('linscarbon.gdpr.portability_description') }}
                </p>
            </div>

            {{-- Right to Object --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-white text-sm">
                    Art. 21 DSGVO / § 37 BDSG
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('linscarbon.gdpr.object_description') }}
                </p>
            </div>
        </div>

        {{-- DPO Contact --}}
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>{{ __('linscarbon.gdpr.dpo_contact') }}:</strong>
                <a href="mailto:{{ __('linscarbon.gdpr.dpo_email') }}" class="underline hover:no-underline">
                    {{ __('linscarbon.gdpr.dpo_email') }}
                </a>
            </p>
        </div>
    </div>

    {{-- Account Deletion (Art. 17 DSGVO, § 35 BDSG) --}}
    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800 p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-medium text-red-800 dark:text-red-200">
                    {{ __('linscarbon.gdpr.delete_account') }}
                </h3>
                <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                    {{ __('linscarbon.gdpr.delete_description') }}
                </p>
                <p class="mt-2 text-xs text-red-600 dark:text-red-400">
                    {{ __('linscarbon.gdpr.delete_warning') }}
                </p>

                <button
                    wire:click="confirmDelete"
                    class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('linscarbon.gdpr.delete_button') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cancelDelete"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                {{ __('linscarbon.gdpr.delete_confirm_title') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('linscarbon.gdpr.delete_confirm_message') }}
                                </p>
                                <input
                                    type="text"
                                    wire:model="deleteConfirmation"
                                    class="mt-3 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                    placeholder="{{ __('linscarbon.gdpr.delete_confirm_word') }}"
                                >
                                @error('deleteConfirmation')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        wire:click="deleteAccount"
                        type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        {{ __('linscarbon.gdpr.delete_button') }}
                    </button>
                    <button
                        wire:click="cancelDelete"
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        {{ __('linscarbon.common.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

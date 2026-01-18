{{-- Cookie Consent Banner - GDPR/RGPD/BDSG/TTDSG Compliant --}}
@if($showBanner)
<div
    x-data="{ showDetails: @entangle('showDetails') }"
    class="fixed bottom-0 inset-x-0 z-50 p-4"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
>
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Main Banner --}}
        <div class="p-6">
            <div class="flex items-start gap-4">
                {{-- Cookie Icon --}}
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('linscarbon.cookies.title') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        {{ __('linscarbon.cookies.description') }}
                    </p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('linscarbon.cookies.legal_notice') }}
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 flex flex-wrap gap-3">
                <button
                    wire:click="acceptAll"
                    class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    {{ __('linscarbon.cookies.accept_all') }}
                </button>

                <button
                    wire:click="acceptEssential"
                    class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors"
                >
                    {{ __('linscarbon.cookies.essential_only') }}
                </button>

                <button
                    wire:click="toggleDetails"
                    class="px-5 py-2.5 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white text-sm font-medium transition-colors flex items-center gap-2"
                >
                    {{ __('linscarbon.cookies.customize') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showDetails }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Details Panel --}}
        <div
            x-show="showDetails"
            x-collapse
            class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50"
        >
            <div class="p-6 space-y-4">
                {{-- Essential Cookies --}}
                <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            {{ __('linscarbon.cookies.essential_title') }}
                        </h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('linscarbon.cookies.essential_desc') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                        <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-full">
                            {{ __('linscarbon.cookies.always_active') }}
                        </span>
                    </div>
                </div>

                {{-- Functional Cookies --}}
                <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            {{ __('linscarbon.cookies.functional_title') }}
                        </h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('linscarbon.cookies.functional_desc') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="functional" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>

                {{-- Analytics Cookies --}}
                <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            {{ __('linscarbon.cookies.analytics_title') }}
                        </h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('linscarbon.cookies.analytics_desc') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="analytics" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>

                {{-- Marketing Cookies --}}
                <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-white">
                            {{ __('linscarbon.cookies.marketing_title') }}
                        </h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('linscarbon.cookies.marketing_desc') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="marketing" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>

                {{-- Save Preferences Button --}}
                <div class="flex justify-end pt-4">
                    <button
                        wire:click="savePreferences"
                        class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        {{ __('linscarbon.cookies.save_preferences') }}
                    </button>
                </div>

                {{-- Legal Links --}}
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400">
                        <a href="{{ route('legal.privacy') }}" class="hover:text-green-600 dark:hover:text-green-400">
                            {{ __('linscarbon.cookies.privacy_policy') }}
                        </a>
                        <a href="{{ route('legal.mentions') }}" class="hover:text-green-600 dark:hover:text-green-400">
                            {{ __('linscarbon.cookies.legal_notice_link') }}
                        </a>
                        <span>GDPR Art. 7 | TTDSG § 25 | BDSG</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

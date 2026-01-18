<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">{{ __('linscarbon.pillars.promote.share') }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ __('linscarbon.promote.showcase_desc') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Marketing Assets Section -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900">{{ __('linscarbon.promote.marketing_assets') }}</h2>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Social Media Kit -->
                <div class="p-6 border border-gray-200 rounded-xl text-center hover:border-emerald-500 transition-colors">
                    <div class="w-12 h-12 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 mb-2">{{ __('linscarbon.promote.social_kit') }}</h3>
                    <p class="text-sm text-gray-500 mb-4">Images optimisées pour LinkedIn, Twitter et Facebook</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                        {{ __('linscarbon.common.coming_soon') }}
                    </span>
                </div>

                <!-- Email Signature -->
                <div class="p-6 border border-gray-200 rounded-xl text-center hover:border-emerald-500 transition-colors">
                    <div class="w-12 h-12 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 mb-2">{{ __('linscarbon.promote.email_signature') }}</h3>
                    <p class="text-sm text-gray-500 mb-4">Ajoutez vos badges à votre signature email</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                        {{ __('linscarbon.common.coming_soon') }}
                    </span>
                </div>

                <!-- Website Widget -->
                <div class="p-6 border border-gray-200 rounded-xl text-center hover:border-emerald-500 transition-colors">
                    <div class="w-12 h-12 mx-auto mb-4 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 mb-2">{{ __('linscarbon.promote.embed_widget') }}</h3>
                    <p class="text-sm text-gray-500 mb-4">Intégrez un widget sur votre site web</p>
                    <a href="{{ route('promote.showcase') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                        {{ __('linscarbon.common.view_details') }}
                    </a>
                </div>
            </div>
        </x-card>

        <!-- Share Your Journey -->
        <x-card>
            <x-slot name="header">
                <h2 class="text-lg font-medium text-gray-900">{{ __('linscarbon.promote.sustainability_journey') }}</h2>
            </x-slot>

            <div class="text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Partagez vos engagements</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">Affichez vos badges et communiquez sur vos efforts de réduction carbone auprès de vos parties prenantes.</p>
                <a href="{{ route('promote.showcase') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    {{ __('linscarbon.promote.view_all_badges') }}
                </a>
            </div>
        </x-card>
    </div>
</x-layouts.app>

<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('carbex.documents.title') }}</h1>
            <span class="ml-3 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                {{ __('carbex.common.ai') }}
            </span>
        </div>
    </x-slot>

    <livewire:a-i.document-uploader />
</x-layouts.app>

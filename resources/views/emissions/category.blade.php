<x-layouts.app :currentScope="$scope" :currentCategory="$category">
    <x-slot name="header">
        <div class="flex items-center text-sm text-gray-500">
            <span>{{ session('current_assessment_year', date('Y')) }}</span>
            <svg class="mx-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span>{{ auth()->user()->organization?->name ?? auth()->user()->name }}</span>
        </div>
    </x-slot>

    <livewire:emissions.category-form :scope="$scope" :category="$category" />
</x-layouts.app>

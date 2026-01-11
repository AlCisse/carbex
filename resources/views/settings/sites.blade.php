<x-layouts.app>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">Sites</h1>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1">
            <x-settings-menu active="sites" />
        </div>
        <div class="lg:col-span-3">
            <livewire:settings.site-management />
        </div>
    </div>
</x-layouts.app>

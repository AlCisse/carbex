<x-layouts.guest>
    <livewire:auth.login-form />

    <x-slot name="footer">
        <p class="text-sm text-gray-600">
            {{ __('carbex.auth.no_account') }}
            <a href="{{ route('register') }}" class="font-semibold text-green-600 hover:text-green-500">
                {{ __('carbex.auth.register_link') }}
            </a>
        </p>
    </x-slot>
</x-layouts.guest>

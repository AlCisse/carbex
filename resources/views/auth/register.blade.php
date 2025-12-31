<x-layouts.guest>
    <livewire:auth.register-form />

    <x-slot name="footer">
        <p class="text-sm text-gray-600">
            {{ __('carbex.auth.already_account') }}
            <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:text-green-500">
                {{ __('carbex.auth.login_link') }}
            </a>
        </p>
    </x-slot>
</x-layouts.guest>

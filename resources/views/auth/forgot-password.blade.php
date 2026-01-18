<x-layouts.guest>
    <livewire:auth.forgot-password-form />

    <x-slot name="footer">
        <p class="text-sm text-gray-600">
            {{ __('linscarbon.auth.remember_password') }}
            <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:text-green-500">
                {{ __('linscarbon.auth.login_link') }}
            </a>
        </p>
    </x-slot>
</x-layouts.guest>

<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="flex justify-end gap-4 mt-6">
            <x-filament::button type="submit">
                {{ __('carbex.filament.save_configuration') }}
            </x-filament::button>
        </div>
    </x-filament-panels::form>

    <x-filament::section class="mt-8">
        <x-slot name="heading">
            {{ __('carbex.filament.api_keys_configuration') }}
        </x-slot>
        <x-slot name="description">
            {{ __('carbex.filament.api_keys_description') }}
        </x-slot>

        <div class="prose dark:prose-invert max-w-none">
            <h4>{{ __('carbex.filament.how_to_configure') }}</h4>
            <ol>
                <li>{{ __('carbex.filament.step_create_secrets') }}</li>
                <li>{{ __('carbex.filament.step_add_keys') }}</li>
                <li>{{ __('carbex.filament.step_auto_mount') }}</li>
            </ol>

            <h4>{{ __('carbex.filament.secrets_files') }}</h4>
            <ul>
                <li><code>anthropic_api_key</code> - {{ __('carbex.filament.anthropic_key') }}</li>
                <li><code>openai_api_key</code> - {{ __('carbex.filament.openai_key') }}</li>
                <li><code>google_api_key</code> - {{ __('carbex.filament.google_key') }}</li>
                <li><code>deepseek_api_key</code> - {{ __('carbex.filament.deepseek_key') }}</li>
            </ul>

            <h4>{{ __('carbex.filament.example_command') }}</h4>
            <pre><code>echo "sk-ant-api..." > docker/secrets/anthropic_api_key
chmod 600 docker/secrets/anthropic_api_key</code></pre>
        </div>
    </x-filament::section>
</x-filament-panels::page>

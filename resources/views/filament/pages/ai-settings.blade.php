<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="flex justify-end gap-4 mt-6">
            <x-filament::button type="submit">
                Sauvegarder la configuration
            </x-filament::button>
        </div>
    </x-filament-panels::form>

    <x-filament::section class="mt-8">
        <x-slot name="heading">
            Configuration des clés API
        </x-slot>
        <x-slot name="description">
            Les clés API sont stockées de manière sécurisée dans Docker Secrets.
        </x-slot>

        <div class="prose dark:prose-invert max-w-none">
            <h4>Comment configurer les clés API :</h4>
            <ol>
                <li>Créez les fichiers secrets dans <code>docker/secrets/</code></li>
                <li>Ajoutez les clés API dans les fichiers correspondants</li>
                <li>Les secrets sont automatiquement montés dans le container</li>
            </ol>

            <h4>Fichiers de secrets :</h4>
            <ul>
                <li><code>anthropic_api_key</code> - Clé API Anthropic (Claude)</li>
                <li><code>openai_api_key</code> - Clé API OpenAI</li>
                <li><code>google_api_key</code> - Clé API Google AI</li>
                <li><code>deepseek_api_key</code> - Clé API DeepSeek</li>
            </ul>

            <h4>Exemple de commande :</h4>
            <pre><code>echo "sk-ant-api..." > docker/secrets/anthropic_api_key
chmod 600 docker/secrets/anthropic_api_key</code></pre>
        </div>
    </x-filament::section>
</x-filament-panels::page>

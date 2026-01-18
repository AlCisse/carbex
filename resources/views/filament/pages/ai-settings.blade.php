<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="flex justify-end gap-4 mt-6">
            <x-filament::button type="submit">
                {{ __('carbex.filament.save_configuration') }}
            </x-filament::button>
        </div>
    </form>

    <!-- Subscription Model Configuration Section -->
    <x-filament::section class="mt-8">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-credit-card style="width: 1rem; height: 1rem;" />
                Modèles par abonnement
            </div>
        </x-slot>
        <x-slot name="description">
            Configurez le modèle IA attribué à chaque type d'abonnement
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Gratuit --}}
            <div class="rounded-xl border p-5 bg-white dark:bg-gray-900">
                <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-gray-400 uppercase">Gratuit</p>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 mb-3">50K tokens · 100 req/mois</p>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:change="saveSubscriptionModel('free', $event.target.value)">
                        @foreach($this->getAllModelsOptions() as $group => $models)
                            <optgroup label="{{ $group }}">
                                @foreach($models as $modelKey => $modelLabel)
                                    <option value="{{ $modelKey }}" {{ $freeModel === $modelKey ? 'selected' : '' }}>{{ $modelLabel }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            {{-- Starter --}}
            <div class="rounded-xl border p-5 bg-white dark:bg-gray-900">
                <p class="text-xs font-semibold tracking-wide text-blue-600 dark:text-blue-400 uppercase">Starter</p>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 mb-3">200K tokens · 500 req/mois</p>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:change="saveSubscriptionModel('starter', $event.target.value)">
                        @foreach($this->getAllModelsOptions() as $group => $models)
                            <optgroup label="{{ $group }}">
                                @foreach($models as $modelKey => $modelLabel)
                                    <option value="{{ $modelKey }}" {{ $starterModel === $modelKey ? 'selected' : '' }}>{{ $modelLabel }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            {{-- Professional --}}
            <div class="rounded-xl border p-5 bg-white dark:bg-gray-900">
                <p class="text-xs font-semibold tracking-wide text-purple-600 dark:text-purple-400 uppercase">Professional</p>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 mb-3">1M tokens · 2500 req/mois</p>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:change="saveSubscriptionModel('professional', $event.target.value)">
                        @foreach($this->getAllModelsOptions() as $group => $models)
                            <optgroup label="{{ $group }}">
                                @foreach($models as $modelKey => $modelLabel)
                                    <option value="{{ $modelKey }}" {{ $professionalModel === $modelKey ? 'selected' : '' }}>{{ $modelLabel }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            {{-- Enterprise --}}
            <div class="rounded-xl border p-5 bg-white dark:bg-gray-900">
                <p class="text-xs font-semibold tracking-wide text-amber-600 dark:text-amber-400 uppercase">Enterprise</p>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 mb-3">Illimité</p>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:change="saveSubscriptionModel('enterprise', $event.target.value)">
                        @foreach($this->getAllModelsOptions() as $group => $models)
                            <optgroup label="{{ $group }}">
                                @foreach($models as $modelKey => $modelLabel)
                                    <option value="{{ $modelKey }}" {{ $enterpriseModel === $modelKey ? 'selected' : '' }}>{{ $modelLabel }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </div>
    </x-filament::section>

    <!-- API Keys Configuration Section -->
    <x-filament::section class="mt-8">
        <x-slot name="heading">
            {{ __('carbex.filament.api_keys_configuration') }}
        </x-slot>
        <x-slot name="description">
            {{ __('carbex.filament.api_keys_description') }}
        </x-slot>

        <div class="space-y-6">
            <!-- Anthropic API Key -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900 flex items-center justify-center">
                            <span class="text-orange-600 dark:text-orange-400 font-bold text-sm">A</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">Anthropic (Claude)</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Claude Sonnet 4, Claude 3.5, etc.</p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <input
                        type="password"
                        wire:model="anthropicApiKey"
                        placeholder="sk-ant-api03-..."
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    >
                    <x-filament::button
                        wire:click="saveApiKey('anthropic')"
                        size="sm"
                    >
                        Enregistrer
                    </x-filament::button>
                    <x-filament::button
                        wire:click="testConnection('anthropic')"
                        size="sm"
                        color="info"
                    >
                        Tester
                    </x-filament::button>
                    @if($anthropicApiKey)
                    <x-filament::button
                        wire:click="removeApiKey('anthropic')"
                        size="sm"
                        color="danger"
                        wire:confirm="Supprimer cette clé API ?"
                    >
                        Supprimer
                    </x-filament::button>
                    @endif
                </div>
            </div>

            <!-- OpenAI API Key -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center">
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold text-sm">O</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">OpenAI (GPT)</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">GPT-4o, GPT-4 Turbo, etc.</p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <input
                        type="password"
                        wire:model="openaiApiKey"
                        placeholder="sk-..."
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    >
                    <x-filament::button
                        wire:click="saveApiKey('openai')"
                        size="sm"
                    >
                        Enregistrer
                    </x-filament::button>
                    <x-filament::button
                        wire:click="testConnection('openai')"
                        size="sm"
                        color="info"
                    >
                        Tester
                    </x-filament::button>
                    @if($openaiApiKey)
                    <x-filament::button
                        wire:click="removeApiKey('openai')"
                        size="sm"
                        color="danger"
                        wire:confirm="Supprimer cette clé API ?"
                    >
                        Supprimer
                    </x-filament::button>
                    @endif
                </div>
            </div>

            <!-- Google API Key -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">G</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">Google (Gemini)</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Gemini 1.5 Pro, Gemini Flash, etc.</p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <input
                        type="password"
                        wire:model="googleApiKey"
                        placeholder="AIza..."
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    >
                    <x-filament::button
                        wire:click="saveApiKey('google')"
                        size="sm"
                    >
                        Enregistrer
                    </x-filament::button>
                    <x-filament::button
                        wire:click="testConnection('google')"
                        size="sm"
                        color="info"
                    >
                        Tester
                    </x-filament::button>
                    @if($googleApiKey)
                    <x-filament::button
                        wire:click="removeApiKey('google')"
                        size="sm"
                        color="danger"
                        wire:confirm="Supprimer cette clé API ?"
                    >
                        Supprimer
                    </x-filament::button>
                    @endif
                </div>
            </div>

            <!-- DeepSeek API Key -->
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                            <span class="text-purple-600 dark:text-purple-400 font-bold text-sm">D</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">DeepSeek</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">DeepSeek Chat, DeepSeek Coder</p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <input
                        type="password"
                        wire:model="deepseekApiKey"
                        placeholder="sk-..."
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    >
                    <x-filament::button
                        wire:click="saveApiKey('deepseek')"
                        size="sm"
                    >
                        Enregistrer
                    </x-filament::button>
                    <x-filament::button
                        wire:click="testConnection('deepseek')"
                        size="sm"
                        color="info"
                    >
                        Tester
                    </x-filament::button>
                    @if($deepseekApiKey)
                    <x-filament::button
                        wire:click="removeApiKey('deepseek')"
                        size="sm"
                        color="danger"
                        wire:confirm="Supprimer cette clé API ?"
                    >
                        Supprimer
                    </x-filament::button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="mt-6 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Stockage securise</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>Les cles API sont chiffrees avec AES-256 avant d'etre stockees en base de donnees. Elles ne sont jamais exposees en clair.</p>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>

    <!-- Help Section -->
    <x-filament::section class="mt-8">
        <x-slot name="heading">
            Obtenir des cles API
        </x-slot>

        <div class="prose dark:prose-invert max-w-none text-sm">
            <ul class="space-y-2">
                <li>
                    <a href="https://console.anthropic.com/" target="_blank" class="text-primary-600 hover:text-primary-500 dark:text-primary-400">
                        Anthropic Console
                    </a>
                    - Obtenez votre cle API Claude
                </li>
                <li>
                    <a href="https://platform.openai.com/api-keys" target="_blank" class="text-primary-600 hover:text-primary-500 dark:text-primary-400">
                        OpenAI Platform
                    </a>
                    - Obtenez votre cle API GPT
                </li>
                <li>
                    <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-primary-600 hover:text-primary-500 dark:text-primary-400">
                        Google AI Studio
                    </a>
                    - Obtenez votre cle API Gemini
                </li>
                <li>
                    <a href="https://platform.deepseek.com/" target="_blank" class="text-primary-600 hover:text-primary-500 dark:text-primary-400">
                        DeepSeek Platform
                    </a>
                    - Obtenez votre cle API DeepSeek
                </li>
            </ul>
        </div>
    </x-filament::section>
</x-filament-panels::page>

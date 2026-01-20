<?php

namespace App\Filament\Pages;

use App\Models\AISetting;
use App\Services\AI\AIManager;
use Filament\Schemas\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Actions\Action;

class AISettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Configuration IA';
    protected static string | \UnitEnum | null $navigationGroup = 'Paramètres';
    protected static ?int $navigationSort = 100;
    protected string $view = 'filament.pages.ai-settings';

    public ?array $data = [];

    // API Keys (for input)
    public string $anthropicApiKey = '';
    public string $openaiApiKey = '';
    public string $googleApiKey = '';
    public string $deepseekApiKey = '';

    // Subscription model assignments
    public string $freeModel = '';
    public string $starterModel = '';
    public string $professionalModel = '';
    public string $enterpriseModel = '';

    // Plan configuration
    public array $subscriptionPlans = [];

    public function mount(): void
    {
        $settings = AISetting::getAllSettings();

        $this->form->fill([
            'default_provider' => $settings['default_provider'] ?? 'anthropic',
            'anthropic_enabled' => $settings['anthropic_enabled'] ?? true,
            'anthropic_model' => $settings['anthropic_model'] ?? 'claude-sonnet-4-20250514',
            'openai_enabled' => $settings['openai_enabled'] ?? false,
            'openai_model' => $settings['openai_model'] ?? 'gpt-4o',
            'google_enabled' => $settings['google_enabled'] ?? false,
            'google_model' => $settings['google_model'] ?? 'gemini-2.0-flash',
            'deepseek_enabled' => $settings['deepseek_enabled'] ?? false,
            'deepseek_model' => $settings['deepseek_model'] ?? 'deepseek-chat',
            'max_tokens' => $settings['max_tokens'] ?? 4096,
            'temperature' => $settings['temperature'] ?? '0.7',
        ]);

        // Load masked API keys
        $this->anthropicApiKey = $this->getMaskedKey('anthropic');
        $this->openaiApiKey = $this->getMaskedKey('openai');
        $this->googleApiKey = $this->getMaskedKey('google');
        $this->deepseekApiKey = $this->getMaskedKey('deepseek');

        // Load subscription model assignments
        $this->freeModel = $settings['free_model'] ?? 'gemini-2.0-flash-lite';
        $this->starterModel = $settings['starter_model'] ?? 'gpt-4o-mini';
        $this->professionalModel = $settings['professional_model'] ?? 'claude-sonnet-4-20250514';
        $this->enterpriseModel = $settings['enterprise_model'] ?? 'claude-sonnet-4-20250514';

        // Build subscription plans data
        $this->subscriptionPlans = $this->buildSubscriptionPlans();
    }

    protected function buildSubscriptionPlans(): array
    {
        return [
            'free' => [
                'name' => 'Gratuit',
                'description' => 'Essai gratuit - Fonctionnalités de base',
                'color' => 'gray',
                'icon' => 'gift',
                'tokens' => '50K tokens/mois',
                'requests' => '100 requêtes/mois',
            ],
            'starter' => [
                'name' => 'Starter',
                'description' => 'Pour les petites équipes',
                'color' => 'blue',
                'icon' => 'rocket',
                'tokens' => '200K tokens/mois',
                'requests' => '500 requêtes/mois',
            ],
            'professional' => [
                'name' => 'Professional',
                'description' => 'Pour les entreprises en croissance',
                'color' => 'purple',
                'icon' => 'briefcase',
                'tokens' => '1M tokens/mois',
                'requests' => '2,500 requêtes/mois',
            ],
            'enterprise' => [
                'name' => 'Enterprise',
                'description' => 'Solution sur mesure',
                'color' => 'amber',
                'icon' => 'building-office',
                'tokens' => 'Illimité',
                'requests' => 'Illimité',
            ],
        ];
    }

    protected function getMaskedKey(string $provider): string
    {
        $key = AISetting::getValue("{$provider}_api_key");
        if (!$key) {
            return '';
        }

        if (strlen($key) <= 8) {
            return str_repeat('*', strlen($key));
        }

        return substr($key, 0, 4) . str_repeat('*', strlen($key) - 8) . substr($key, -4);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Provider par défaut')
                    ->description('Sélectionnez le provider IA utilisé par défaut pour l\'assistant.')
                    ->schema([
                        Select::make('default_provider')
                            ->label('Provider principal')
                            ->options([
                                'anthropic' => 'Anthropic (Claude)',
                                'openai' => 'OpenAI (GPT)',
                                'google' => 'Google (Gemini)',
                                'deepseek' => 'DeepSeek',
                            ])
                            ->required(),
                    ]),

                Section::make('Anthropic (Claude)')
                    ->description('Configuration pour Claude AI')
                    ->collapsible()
                    ->schema([
                        Toggle::make('anthropic_enabled')
                            ->label('Activer')
                            ->live(),
                        Select::make('anthropic_model')
                            ->label(__('linscarbon.settings.ai.model'))
                            ->options([
                                'claude-sonnet-4-20250514' => 'Claude Sonnet 4 ★',
                                'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet',
                                'claude-3-5-haiku-20241022' => 'Claude 3.5 Haiku',
                                'claude-3-haiku-20240307' => 'Claude 3 Haiku',
                                'claude-3-opus-20240229' => 'Claude 3 Opus (Premium)',
                            ])
                            ->visible(fn (Get $get) => $get('anthropic_enabled')),
                        Placeholder::make('anthropic_status')
                            ->label('Statut')
                            ->content(fn () => $this->getProviderStatus('anthropic')),
                    ]),

                Section::make('OpenAI (GPT)')
                    ->description('Configuration pour GPT')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Toggle::make('openai_enabled')
                            ->label('Activer')
                            ->live(),
                        Select::make('openai_model')
                            ->label(__('linscarbon.settings.ai.model'))
                            ->options([
                                'gpt-4.5-preview' => 'GPT-4.5 Preview (Premium)',
                                'gpt-4o' => 'GPT-4o ★',
                                'gpt-4o-mini' => 'GPT-4o Mini',
                                'o3-mini' => 'o3-mini (Reasoning)',
                                'o1' => 'o1 (Reasoning Premium)',
                                'o1-mini' => 'o1-mini (Reasoning)',
                                'gpt-4-turbo' => 'GPT-4 Turbo',
                                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
                            ])
                            ->visible(fn (Get $get) => $get('openai_enabled')),
                        Placeholder::make('openai_status')
                            ->label('Statut')
                            ->content(fn () => $this->getProviderStatus('openai')),
                    ]),

                Section::make('Google (Gemini)')
                    ->description('Configuration pour Gemini')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Toggle::make('google_enabled')
                            ->label('Activer')
                            ->live(),
                        Select::make('google_model')
                            ->label(__('linscarbon.settings.ai.model'))
                            ->options([
                                'gemini-2.0-flash' => 'Gemini 2.0 Flash ★',
                                'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash Lite',
                                'gemini-1.5-pro' => 'Gemini 1.5 Pro',
                                'gemini-1.5-flash' => 'Gemini 1.5 Flash',
                                'gemini-pro' => 'Gemini Pro',
                            ])
                            ->visible(fn (Get $get) => $get('google_enabled')),
                        Placeholder::make('google_status')
                            ->label('Statut')
                            ->content(fn () => $this->getProviderStatus('google')),
                    ]),

                Section::make('DeepSeek')
                    ->description('Configuration pour DeepSeek')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Toggle::make('deepseek_enabled')
                            ->label('Activer')
                            ->live(),
                        Select::make('deepseek_model')
                            ->label(__('linscarbon.settings.ai.model'))
                            ->options([
                                'deepseek-chat' => 'DeepSeek Chat ★',
                                'deepseek-coder' => 'DeepSeek Coder',
                            ])
                            ->visible(fn (Get $get) => $get('deepseek_enabled')),
                        Placeholder::make('deepseek_status')
                            ->label('Statut')
                            ->content(fn () => $this->getProviderStatus('deepseek')),
                    ]),

                Section::make('Paramètres avancés')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('max_tokens')
                            ->label('Tokens maximum')
                            ->numeric()
                            ->minValue(256)
                            ->maxValue(8192)
                            ->helperText('Nombre maximum de tokens par réponse'),
                        TextInput::make('temperature')
                            ->label('Température')
                            ->helperText('0.0 = déterministe, 1.0 = créatif'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_status')) {
                continue; // Skip placeholder fields
            }
            AISetting::setValue($key, $value);
        }

        AISetting::clearCache();

        Notification::make()
            ->title('Configuration IA sauvegardée')
            ->success()
            ->send();
    }

    public function saveApiKey(string $provider): void
    {
        $keyProperty = "{$provider}ApiKey";
        $newKey = $this->$keyProperty;

        // Don't save if it's empty or contains only asterisks (masked)
        if (empty($newKey) || preg_match('/^\*+$/', $newKey) || str_contains($newKey, '****')) {
            Notification::make()
                ->title('Clé API invalide')
                ->body('Veuillez entrer une nouvelle clé API valide.')
                ->danger()
                ->send();
            return;
        }

        // Save the encrypted API key
        AISetting::setValue("{$provider}_api_key", $newKey);

        // Auto-enable provider
        AISetting::setValue("{$provider}_enabled", true);

        AISetting::clearCache();

        // Update masked display
        $this->$keyProperty = $this->getMaskedKey($provider);

        // Refresh form to update status
        $this->mount();

        Notification::make()
            ->title('Clé API sauvegardée')
            ->body('La clé API ' . ucfirst($provider) . ' a été enregistrée de manière sécurisée.')
            ->success()
            ->send();
    }

    public function removeApiKey(string $provider): void
    {
        AISetting::where('key', "{$provider}_api_key")->delete();
        AISetting::clearCache();

        $keyProperty = "{$provider}ApiKey";
        $this->$keyProperty = '';

        Notification::make()
            ->title('Clé API supprimée')
            ->body('La clé API ' . ucfirst($provider) . ' a été supprimée.')
            ->success()
            ->send();
    }

    public function testConnection(string $provider): void
    {
        $manager = app(AIManager::class);
        $providerInstance = $manager->provider($provider);

        if (!$providerInstance || !$providerInstance->isAvailable()) {
            Notification::make()
                ->title('Provider non configuré')
                ->body('Le provider ' . ucfirst($provider) . ' n\'est pas correctement configuré.')
                ->danger()
                ->send();
            return;
        }

        try {
            $response = $providerInstance->prompt('Say "OK" in exactly one word.');

            if ($response) {
                Notification::make()
                    ->title('Connexion réussie')
                    ->body('Le provider ' . ucfirst($provider) . ' fonctionne correctement.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Échec de connexion')
                    ->body('Le provider ' . ucfirst($provider) . ' n\'a pas répondu.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur de connexion')
                ->body('Erreur: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getProviderStatus(string $provider): string
    {
        // Check if API key exists in database
        $dbKey = AISetting::getValue("{$provider}_api_key");
        if ($dbKey) {
            $manager = app(AIManager::class);
            $providerInstance = $manager->provider($provider);

            if ($providerInstance && $providerInstance->isAvailable()) {
                return '✅ Configuré et actif';
            }

            return '⚠️ Clé enregistrée - Vérifiez la configuration';
        }

        // Check Docker secrets
        $secretPath = "/run/secrets/{$provider}_api_key";
        if (file_exists($secretPath)) {
            return '✅ Clé via Docker secrets';
        }

        return '❌ Clé API non configurée';
    }

    protected function getAllModelsOptions(): array
    {
        return [
            'Anthropic (Claude)' => [
                'claude-sonnet-4-20250514' => 'Claude Sonnet 4 ★',
                'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet',
                'claude-3-5-haiku-20241022' => 'Claude 3.5 Haiku',
                'claude-3-haiku-20240307' => 'Claude 3 Haiku',
                'claude-3-opus-20240229' => 'Claude 3 Opus (Premium)',
            ],
            'OpenAI (GPT)' => [
                'gpt-4.5-preview' => 'GPT-4.5 Preview (Premium)',
                'gpt-4o' => 'GPT-4o',
                'gpt-4o-mini' => 'GPT-4o Mini',
                'o3-mini' => 'o3-mini (Reasoning)',
                'o1' => 'o1 (Reasoning Premium)',
                'o1-mini' => 'o1-mini (Reasoning)',
                'gpt-4-turbo' => 'GPT-4 Turbo',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            ],
            'Google (Gemini)' => [
                'gemini-2.0-flash' => 'Gemini 2.0 Flash',
                'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash Lite',
                'gemini-1.5-pro' => 'Gemini 1.5 Pro',
                'gemini-1.5-flash' => 'Gemini 1.5 Flash',
                'gemini-pro' => 'Gemini Pro',
            ],
            'DeepSeek' => [
                'deepseek-chat' => 'DeepSeek Chat',
                'deepseek-coder' => 'DeepSeek Coder',
            ],
        ];
    }

    public function getFlatModelsOptions(): array
    {
        $flat = [];
        foreach ($this->getAllModelsOptions() as $group => $models) {
            foreach ($models as $key => $label) {
                $flat[$key] = $label;
            }
        }
        return $flat;
    }

    public function getModelLabel(string $modelKey): string
    {
        $options = $this->getFlatModelsOptions();
        return $options[$modelKey] ?? $modelKey;
    }

    public function saveSubscriptionModel(string $plan, string $model): void
    {
        $property = "{$plan}Model";
        $this->$property = $model;

        AISetting::setValue("{$plan}_model", $model);
        AISetting::clearCache();

        Notification::make()
            ->title('Modèle mis à jour')
            ->body("Le modèle pour le plan " . ucfirst($plan) . " a été changé.")
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Sauvegarder')
                ->submit('save'),
        ];
    }
}

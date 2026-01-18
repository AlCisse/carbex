<?php

namespace App\Filament\Pages;

use App\Models\AISetting;
use App\Services\AI\AIManager;
use Filament\Forms\Components\Placeholder;
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
            'google_model' => $settings['google_model'] ?? 'gemini-1.5-pro',
            'deepseek_enabled' => $settings['deepseek_enabled'] ?? false,
            'deepseek_model' => $settings['deepseek_model'] ?? 'deepseek-chat',
            'max_tokens' => $settings['max_tokens'] ?? 4096,
            'temperature' => $settings['temperature'] ?? '0.7',
        ]);
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
                            ->helperText('Les clés API sont stockées dans les secrets Docker'),
                        Select::make('anthropic_model')
                            ->label(__('carbex.settings.ai.model'))
                            ->options([
                                'claude-sonnet-4-20250514' => 'Claude Sonnet 4 ★',
                                'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet',
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
                            ->label('Activer'),
                        Select::make('openai_model')
                            ->label(__('carbex.settings.ai.model'))
                            ->options([
                                'gpt-4o' => 'GPT-4o ★',
                                'gpt-4o-mini' => 'GPT-4o Mini',
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
                            ->label('Activer'),
                        Select::make('google_model')
                            ->label(__('carbex.settings.ai.model'))
                            ->options([
                                'gemini-1.5-pro' => 'Gemini 1.5 Pro ★',
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
                            ->label('Activer'),
                        Select::make('deepseek_model')
                            ->label(__('carbex.settings.ai.model'))
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

    protected function getProviderStatus(string $provider): string
    {
        $manager = app(AIManager::class);
        $providerInstance = $manager->provider($provider);

        if (!$providerInstance) {
            return '❌ Provider non trouvé';
        }

        if ($providerInstance->isAvailable()) {
            return '✅ Configuré et actif';
        }

        // Check if API key exists in Docker secrets
        $secretPath = "/run/secrets/{$provider}_api_key";
        if (file_exists($secretPath)) {
            return '⚠️ Clé trouvée mais provider désactivé';
        }

        return '❌ Clé API non configurée';
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

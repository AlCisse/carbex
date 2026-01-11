<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Multi-Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Carbex supporte plusieurs providers IA: Anthropic (Claude), OpenAI,
    | Google (Gemini), et DeepSeek. Configurable via l'admin Filament.
    |
    */

    'default_provider' => env('AI_DEFAULT_PROVIDER', 'anthropic'),

    /*
    |--------------------------------------------------------------------------
    | Providers Configuration
    |--------------------------------------------------------------------------
    |
    | Chaque provider a ses propres modèles et paramètres.
    | Les API keys sont stockées dans Docker secrets.
    |
    */

    'providers' => [
        'anthropic' => [
            'name' => 'Anthropic (Claude)',
            'enabled' => env('AI_ANTHROPIC_ENABLED', true),
            'api_key' => env('ANTHROPIC_API_KEY'),
            'api_url' => 'https://api.anthropic.com/v1/messages',
            'models' => [
                'claude-sonnet-4-20250514' => 'Claude Sonnet 4 (Recommandé)',
                'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet',
                'claude-3-haiku-20240307' => 'Claude 3 Haiku (Économique)',
                'claude-3-opus-20240229' => 'Claude 3 Opus (Premium)',
            ],
            'default_model' => env('AI_ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),
            'max_tokens' => 4096,
        ],

        'openai' => [
            'name' => 'OpenAI (GPT)',
            'enabled' => env('AI_OPENAI_ENABLED', false),
            'api_key' => env('OPENAI_API_KEY'),
            'api_url' => 'https://api.openai.com/v1/chat/completions',
            'models' => [
                'gpt-4o' => 'GPT-4o (Recommandé)',
                'gpt-4o-mini' => 'GPT-4o Mini (Économique)',
                'gpt-4-turbo' => 'GPT-4 Turbo',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo (Économique)',
            ],
            'default_model' => env('AI_OPENAI_MODEL', 'gpt-4o'),
            'max_tokens' => 4096,
        ],

        'google' => [
            'name' => 'Google (Gemini)',
            'enabled' => env('AI_GOOGLE_ENABLED', false),
            'api_key' => env('GOOGLE_AI_API_KEY'),
            'api_url' => 'https://generativelanguage.googleapis.com/v1beta/models',
            'models' => [
                'gemini-1.5-pro' => 'Gemini 1.5 Pro (Recommandé)',
                'gemini-1.5-flash' => 'Gemini 1.5 Flash (Rapide)',
                'gemini-pro' => 'Gemini Pro',
            ],
            'default_model' => env('AI_GOOGLE_MODEL', 'gemini-1.5-pro'),
            'max_tokens' => 4096,
        ],

        'deepseek' => [
            'name' => 'DeepSeek',
            'enabled' => env('AI_DEEPSEEK_ENABLED', false),
            'api_key' => env('DEEPSEEK_API_KEY'),
            'api_url' => 'https://api.deepseek.com/v1/chat/completions',
            'models' => [
                'deepseek-chat' => 'DeepSeek Chat (Recommandé)',
                'deepseek-coder' => 'DeepSeek Coder',
            ],
            'default_model' => env('AI_DEEPSEEK_MODEL', 'deepseek-chat'),
            'max_tokens' => 4096,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Generation Parameters
    |--------------------------------------------------------------------------
    */

    'max_tokens' => env('AI_MAX_TOKENS', 4096),
    'temperature' => env('AI_TEMPERATURE', 0.7),
    'timeout' => env('AI_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Plan Quotas
    |--------------------------------------------------------------------------
    |
    | Quotas par plan d'abonnement
    | - enabled: si l'IA est accessible pour ce plan
    | - daily_limit: requêtes par jour (-1 = illimité)
    | - monthly_limit: requêtes par mois (-1 = illimité)
    |
    */

    'plan_quotas' => [
        // Plan gratuit (pas d'abonnement)
        'free' => [
            'enabled' => false,
            'daily_limit' => 0,
            'monthly_limit' => 0,
        ],
        // Starter - 39€/mois (quota limité)
        'starter' => [
            'enabled' => true,
            'daily_limit' => 50,
            'monthly_limit' => 500,
        ],
        // Business - 400€/an
        'business' => [
            'enabled' => true,
            'daily_limit' => 100,
            'monthly_limit' => 1500,
        ],
        // Professional
        'professional' => [
            'enabled' => true,
            'daily_limit' => -1,
            'monthly_limit' => -1,
        ],
        // Enterprise - 840€/an (illimité)
        'enterprise' => [
            'enabled' => true,
            'daily_limit' => -1,
            'monthly_limit' => -1,
        ],
    ],

    // Legacy rate_limits for backward compatibility
    'rate_limits' => [
        'trial' => 20,
        'premium' => 200,
        'advanced' => 1000,
        'enterprise' => -1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Context Configuration
    |--------------------------------------------------------------------------
    |
    | Types de contexte pour les conversations IA
    |
    */

    'context_types' => [
        'emission_entry' => 'Aide à la saisie des émissions',
        'action_suggestion' => 'Recommandations d\'actions de réduction',
        'factor_explanation' => 'Explication des facteurs d\'émission',
        'report_help' => 'Aide à la génération de rapports',
        'general' => 'Questions générales sur le bilan carbone',
    ],

    /*
    |--------------------------------------------------------------------------
    | System Prompts
    |--------------------------------------------------------------------------
    |
    | Prompts système par défaut pour différents contextes
    |
    */

    'system_prompts' => [
        'default' => "Tu es l'assistant IA de Carbex, plateforme de bilan carbone pour PME françaises.
Tu aides les utilisateurs à comprendre et réduire leur empreinte carbone.
Tu connais parfaitement le GHG Protocol, l'ISO 14064, et la Base Carbone ADEME.
Réponds toujours en français, de manière claire et concise.
Si tu ne connais pas la réponse, dis-le honnêtement.",

        'emission_entry' => "Tu es l'assistant Carbex spécialisé dans la saisie des émissions carbone.
Tu aides l'utilisateur à identifier la bonne catégorie d'émission et le bon facteur d'émission.
Utilise la nomenclature GHG Protocol (Scope 1, 2, 3) et les facteurs ADEME.
Pose des questions clarificatrices si nécessaire.",

        'action_suggestion' => "Tu es l'assistant Carbex spécialisé dans les recommandations de réduction carbone.
Analyse le profil d'émissions de l'utilisateur et propose des actions concrètes.
Pour chaque action, indique:
- L'impact estimé (% de réduction)
- Le coût approximatif (€/€€/€€€)
- La difficulté (facile/moyen/difficile)
- Le délai de mise en œuvre",
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */

    'features' => [
        'chat_widget' => env('AI_CHAT_WIDGET_ENABLED', true),
        'emission_helper' => env('AI_EMISSION_HELPER_ENABLED', true),
        'document_extraction' => env('AI_DOCUMENT_EXTRACTION_ENABLED', false),
        'streaming' => env('AI_STREAMING_ENABLED', true),
    ],
];

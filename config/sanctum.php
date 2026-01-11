<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from the following domains / hosts will receive stateful API
    | authentication cookies. Typically, these should include your local
    | and production domains which access your API via a frontend SPA.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ',' . parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | This array contains the authentication guards that will be checked when
    | Sanctum is trying to authenticate a request. If none of these guards
    | are able to authenticate the request, Sanctum will use the bearer
    | token that's present on an incoming request for authentication.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until an issued token will be
    | considered expired. If this value is null, personal access tokens do
    | not expire. This won't tweak the lifetime of first-party sessions.
    |
    */

    'expiration' => env('SANCTUM_TOKEN_EXPIRATION', 60 * 24 * 7), // 7 days

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Sanctum can prefix new tokens in order to take advantage of numerous
    | temporary security improvements offered by platforms like GitHub
    | that check for leaked tokens using their respective prefixes.
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'carbex_'),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPA with Sanctum you may need to
    | customize some of the middleware Sanctum uses while processing the
    | request. You may change the middleware listed below as required.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Abilities
    |--------------------------------------------------------------------------
    |
    | Define the available token abilities/scopes for the application.
    | These are used for fine-grained access control.
    |
    */

    'abilities' => [
        // Organization abilities
        'organization:read' => 'View organization details',
        'organization:update' => 'Update organization settings',

        // Site abilities
        'sites:read' => 'View sites',
        'sites:create' => 'Create new sites',
        'sites:update' => 'Update sites',
        'sites:delete' => 'Delete sites',

        // User abilities
        'users:read' => 'View team members',
        'users:invite' => 'Invite new team members',
        'users:update' => 'Update team members',
        'users:delete' => 'Remove team members',

        // Banking abilities
        'banking:read' => 'View bank connections and accounts',
        'banking:connect' => 'Connect new bank accounts',
        'banking:sync' => 'Trigger bank synchronization',
        'banking:delete' => 'Disconnect bank accounts',

        // Transaction abilities
        'transactions:read' => 'View transactions',
        'transactions:categorize' => 'Categorize transactions',
        'transactions:validate' => 'Validate transactions',
        'transactions:import' => 'Import transactions from CSV',

        // Emission abilities
        'emissions:read' => 'View emission records',
        'emissions:create' => 'Create manual emission entries',
        'emissions:update' => 'Update emission records',
        'emissions:delete' => 'Delete emission records',

        // Report abilities
        'reports:read' => 'View reports',
        'reports:create' => 'Generate new reports',
        'reports:download' => 'Download reports',
        'reports:delete' => 'Delete reports',

        // Subscription abilities
        'subscription:read' => 'View subscription details',
        'subscription:manage' => 'Manage subscription and billing',

        // Admin abilities
        'admin:full' => 'Full administrative access',
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Abilities Mapping
    |--------------------------------------------------------------------------
    |
    | Map roles to their default abilities.
    |
    */

    'role_abilities' => [
        'owner' => ['*'], // All abilities
        'admin' => [
            'organization:read',
            'organization:update',
            'sites:*',
            'users:*',
            'banking:*',
            'transactions:*',
            'emissions:*',
            'reports:*',
            'subscription:read',
        ],
        'manager' => [
            'organization:read',
            'sites:read',
            'sites:update',
            'users:read',
            'banking:read',
            'banking:sync',
            'transactions:*',
            'emissions:*',
            'reports:read',
            'reports:create',
            'reports:download',
        ],
        'member' => [
            'organization:read',
            'sites:read',
            'banking:read',
            'transactions:read',
            'transactions:categorize',
            'emissions:read',
            'reports:read',
            'reports:download',
        ],
        'viewer' => [
            'organization:read',
            'sites:read',
            'transactions:read',
            'emissions:read',
            'reports:read',
        ],
    ],

];

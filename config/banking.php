<?php

/**
 * Carbex - Open Banking Configuration
 *
 * Configuration for Open Banking providers (PSD2 compliant)
 * - Bridge (France)
 * - Finapi (Germany)
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    */

    'default' => env('BANKING_PROVIDER', 'bridge'),

    /*
    |--------------------------------------------------------------------------
    | Provider by Country
    |--------------------------------------------------------------------------
    */

    'country_providers' => [
        'FR' => 'bridge',
        'DE' => 'finapi',
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers Configuration
    |--------------------------------------------------------------------------
    */

    'providers' => [

        /*
        |----------------------------------------------------------------------
        | Bridge (France)
        |----------------------------------------------------------------------
        | https://docs.bridgeapi.io/
        |----------------------------------------------------------------------
        */
        'bridge' => [
            'name' => 'Bridge by Bankin\'',
            'enabled' => true,
            'countries' => ['FR'],

            // API Configuration
            'base_url' => env('BRIDGE_API_URL', 'https://api.bridgeapi.io'),
            'version' => 'v2',
            'client_id' => env('BRIDGE_CLIENT_ID'),
            'client_secret' => env('BRIDGE_CLIENT_SECRET'),
            'webhook_secret' => env('BRIDGE_WEBHOOK_SECRET'),

            // Environment
            'sandbox' => env('BRIDGE_SANDBOX', true),
            'sandbox_url' => 'https://api.sandbox.bridgeapi.io',

            // Mock mode for development
            'mock' => env('BRIDGE_MOCK', false),

            // OAuth Endpoints
            'endpoints' => [
                'auth' => '/v2/connect/items/add',
                'token' => '/v2/oauth/token',
                'refresh' => '/v2/connect/items/{item_id}/refresh',
                'accounts' => '/v2/accounts',
                'transactions' => '/v2/accounts/{account_id}/transactions',
                'items' => '/v2/items',
                'banks' => '/v2/banks',
                'categories' => '/v2/categories',
            ],

            // Webhook Events
            'webhook_events' => [
                'item.created',
                'item.refreshed',
                'item.refresh_failed',
                'item.deleted',
                'account.created',
                'account.updated',
                'transaction.created',
                'transaction.updated',
            ],

            // Rate Limits
            'rate_limits' => [
                'requests_per_minute' => 60,
                'requests_per_day' => 10000,
            ],

            // Sync Configuration
            'sync' => [
                'interval_hours' => env('CARBEX_SYNC_INTERVAL_HOURS', 1),
                'max_days_history' => 90,
                'batch_size' => 100,
                'retry_attempts' => 3,
                'retry_delay_seconds' => 60,
            ],

            // Supported Banks (top French banks)
            'supported_banks' => [
                'bnp_paribas',
                'credit_agricole',
                'societe_generale',
                'credit_mutuel',
                'la_banque_postale',
                'caisse_epargne',
                'lcl',
                'boursorama',
                'hello_bank',
                'fortuneo',
                'n26',
                'revolut',
                'qonto',
                'shine',
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Finapi (Germany)
        |----------------------------------------------------------------------
        | https://docs.finapi.io/
        |----------------------------------------------------------------------
        */
        'finapi' => [
            'name' => 'finAPI',
            'enabled' => true,
            'countries' => ['DE'],

            // API Configuration
            'base_url' => env('FINAPI_API_URL', 'https://live.finapi.io'),
            'version' => 'v2',
            'client_id' => env('FINAPI_CLIENT_ID'),
            'client_secret' => env('FINAPI_CLIENT_SECRET'),
            'webhook_secret' => env('FINAPI_WEBHOOK_SECRET'),

            // Environment
            'sandbox' => env('FINAPI_SANDBOX', true),
            'sandbox_url' => 'https://sandbox.finapi.io',

            // Mock mode for development
            'mock' => env('FINAPI_MOCK', false),

            // OAuth Endpoints
            'endpoints' => [
                'auth' => '/api/v2/bankConnections/import',
                'token' => '/api/v2/oauth/token',
                'refresh' => '/api/v2/bankConnections/{id}/update',
                'accounts' => '/api/v2/accounts',
                'transactions' => '/api/v2/transactions',
                'connections' => '/api/v2/bankConnections',
                'banks' => '/api/v2/banks',
                'categories' => '/api/v2/categories',
            ],

            // Webhook Events
            'webhook_events' => [
                'BANK_CONNECTION_CREATED',
                'BANK_CONNECTION_UPDATED',
                'BANK_CONNECTION_DELETED',
                'NEW_TRANSACTIONS',
            ],

            // Rate Limits
            'rate_limits' => [
                'requests_per_minute' => 100,
                'requests_per_day' => 50000,
            ],

            // Sync Configuration
            'sync' => [
                'interval_hours' => env('CARBEX_SYNC_INTERVAL_HOURS', 1),
                'max_days_history' => 90,
                'batch_size' => 500,
                'retry_attempts' => 3,
                'retry_delay_seconds' => 60,
            ],

            // Supported Banks (top German banks)
            'supported_banks' => [
                'deutsche_bank',
                'commerzbank',
                'sparkasse',
                'volksbank',
                'ing_diba',
                'dkb',
                'comdirect',
                'hypovereinsbank',
                'postbank',
                'n26',
                'revolut',
                'bunq',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Categories (MCC Code Mapping)
    |--------------------------------------------------------------------------
    */

    'mcc_mapping' => [
        // Transportation
        4011 => 'transport_rail',
        4111 => 'transport_local',
        4112 => 'transport_rail',
        4121 => 'transport_taxi',
        4131 => 'transport_bus',
        4214 => 'logistics_freight',
        4215 => 'logistics_courier',
        4511 => 'travel_airline',
        4512 => 'travel_airline',

        // Hotels & Lodging
        3501 => 'travel_hotel',
        3502 => 'travel_hotel',
        3503 => 'travel_hotel',
        7011 => 'travel_hotel',

        // Gas Stations
        5541 => 'fuel_station',
        5542 => 'fuel_station',
        5983 => 'fuel_station',

        // Utilities
        4900 => 'utilities',

        // Office Supplies
        5044 => 'office_equipment',
        5045 => 'computer_equipment',
        5111 => 'office_supplies',
        5943 => 'office_supplies',

        // IT & Software
        5732 => 'electronics',
        5734 => 'software',
        7372 => 'software_services',
        7379 => 'it_services',

        // Restaurants
        5812 => 'restaurant',
        5813 => 'bar',
        5814 => 'fast_food',

        // Professional Services
        8111 => 'legal_services',
        8931 => 'accounting_services',
        8999 => 'professional_services',
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Encryption
    |--------------------------------------------------------------------------
    */

    'encryption' => [
        'enabled' => true,
        'cipher' => 'aes-256-gcm',
        'key' => env('BANKING_ENCRYPTION_KEY', env('APP_KEY')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */

    'webhooks' => [
        'enabled' => true,
        'path' => '/webhooks/banking',
        'verify_signature' => true,
        'queue' => 'banking-webhooks',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'enabled' => true,
        'ttl' => [
            'banks' => 86400,        // 24 hours
            'accounts' => 300,       // 5 minutes
            'transactions' => 60,    // 1 minute
        ],
        'prefix' => 'carbex_banking_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback: Manual CSV Import
    |--------------------------------------------------------------------------
    */

    'csv_import' => [
        'enabled' => true,
        'max_file_size' => 10 * 1024 * 1024, // 10MB
        'allowed_formats' => ['csv', 'xlsx', 'xls', 'ofx', 'qif'],
        'date_formats' => [
            'FR' => ['d/m/Y', 'd-m-Y', 'Y-m-d'],
            'DE' => ['d.m.Y', 'd-m-Y', 'Y-m-d'],
        ],
        'column_mappings' => [
            'date' => ['date', 'datum', 'date_operation', 'buchungstag'],
            'description' => ['description', 'libelle', 'verwendungszweck', 'label'],
            'amount' => ['amount', 'montant', 'betrag', 'value'],
            'category' => ['category', 'categorie', 'kategorie'],
        ],
    ],

];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Energy Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for French energy data providers:
    | - Enedis: Electricity consumption via DataConnect API
    | - GRDF: Gas consumption via ADICT API
    |
    */

    'providers' => [
        'enedis' => [
            'name' => 'Enedis',
            'type' => 'electricity',
            'country' => 'FR',
            'enabled' => env('ENEDIS_ENABLED', true),
            'sandbox' => env('ENEDIS_SANDBOX', true),
            'mock' => env('ENEDIS_MOCK', true),

            // OAuth2 Configuration
            'client_id' => env('ENEDIS_CLIENT_ID'),
            'client_secret' => env('ENEDIS_CLIENT_SECRET'),

            // API Endpoints
            'auth_url' => env('ENEDIS_AUTH_URL', 'https://gw.prd.api.enedis.fr'),
            'api_url' => env('ENEDIS_API_URL', 'https://gw.prd.api.enedis.fr'),
            'sandbox_auth_url' => 'https://gw.hml.api.enedis.fr',
            'sandbox_api_url' => 'https://gw.hml.api.enedis.fr',

            // Redirect URI for OAuth
            'redirect_uri' => env('ENEDIS_REDIRECT_URI', '/energy/enedis/callback'),

            // Data retention (months)
            'history_months' => 36,

            // Sync frequency (hours)
            'sync_interval' => 24,

            // Rate limits
            'rate_limit' => [
                'requests_per_minute' => 10,
                'requests_per_day' => 1000,
            ],
        ],

        'grdf' => [
            'name' => 'GRDF',
            'type' => 'gas',
            'country' => 'FR',
            'enabled' => env('GRDF_ENABLED', true),
            'sandbox' => env('GRDF_SANDBOX', true),
            'mock' => env('GRDF_MOCK', true),

            // OAuth2 Configuration
            'client_id' => env('GRDF_CLIENT_ID'),
            'client_secret' => env('GRDF_CLIENT_SECRET'),

            // API Endpoints (ADICT)
            'auth_url' => env('GRDF_AUTH_URL', 'https://api.grdf.fr'),
            'api_url' => env('GRDF_API_URL', 'https://api.grdf.fr/adict/v2'),
            'sandbox_auth_url' => 'https://api-sandbox.grdf.fr',
            'sandbox_api_url' => 'https://api-sandbox.grdf.fr/adict/v2',

            // Redirect URI for OAuth
            'redirect_uri' => env('GRDF_REDIRECT_URI', '/energy/grdf/callback'),

            // Data retention (months)
            'history_months' => 36,

            // Sync frequency (hours)
            'sync_interval' => 24,

            // Rate limits
            'rate_limit' => [
                'requests_per_minute' => 5,
                'requests_per_day' => 500,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Emission Factors
    |--------------------------------------------------------------------------
    |
    | Default emission factors for energy consumption (kg CO2e per unit)
    | These are used when specific factors are not available
    |
    */

    'emission_factors' => [
        'electricity' => [
            'FR' => [
                'factor' => 0.0569, // kg CO2e/kWh (ADEME 2023)
                'unit' => 'kWh',
                'source' => 'ADEME Base Empreinte',
                'year' => 2023,
            ],
            'DE' => [
                'factor' => 0.366, // kg CO2e/kWh
                'unit' => 'kWh',
                'source' => 'UBA',
                'year' => 2023,
            ],
            'BE' => [
                'factor' => 0.164, // kg CO2e/kWh
                'unit' => 'kWh',
                'source' => 'VITO',
                'year' => 2023,
            ],
            'NL' => [
                'factor' => 0.328, // kg CO2e/kWh
                'unit' => 'kWh',
                'source' => 'CE Delft',
                'year' => 2023,
            ],
            'AT' => [
                'factor' => 0.114, // kg CO2e/kWh (high hydro share)
                'unit' => 'kWh',
                'source' => 'Umweltbundesamt AT',
                'year' => 2023,
            ],
            'CH' => [
                'factor' => 0.024, // kg CO2e/kWh (hydro + nuclear)
                'unit' => 'kWh',
                'source' => 'BAFU',
                'year' => 2023,
            ],
            'ES' => [
                'factor' => 0.192, // kg CO2e/kWh
                'unit' => 'kWh',
                'source' => 'MITECO',
                'year' => 2023,
            ],
            'IT' => [
                'factor' => 0.275, // kg CO2e/kWh
                'unit' => 'kWh',
                'source' => 'ISPRA',
                'year' => 2023,
            ],
        ],
        'gas' => [
            'FR' => [
                'factor' => 2.04, // kg CO2e/m³ (natural gas)
                'unit' => 'm3',
                'source' => 'ADEME Base Empreinte',
                'year' => 2023,
                // PCI: 10.7 kWh/m³
                'factor_kwh' => 0.187, // kg CO2e/kWh
            ],
            'DE' => [
                'factor' => 2.02, // kg CO2e/m³
                'unit' => 'm3',
                'source' => 'UBA',
                'year' => 2023,
                'factor_kwh' => 0.182,
            ],
            'BE' => [
                'factor' => 2.01, // kg CO2e/m³
                'unit' => 'm3',
                'source' => 'VITO',
                'year' => 2023,
                'factor_kwh' => 0.185,
            ],
            'NL' => [
                'factor' => 1.89, // kg CO2e/m³ (Groningen gas lower CO2)
                'unit' => 'm3',
                'source' => 'CE Delft',
                'year' => 2023,
                'factor_kwh' => 0.178,
            ],
            'AT' => [
                'factor' => 2.00, // kg CO2e/m³
                'unit' => 'm3',
                'source' => 'Umweltbundesamt AT',
                'year' => 2023,
                'factor_kwh' => 0.184,
            ],
            'CH' => [
                'factor' => 2.05, // kg CO2e/m³
                'unit' => 'm3',
                'source' => 'BAFU',
                'year' => 2023,
                'factor_kwh' => 0.189,
            ],
            'ES' => [
                'factor' => 2.03, // kg CO2e/m³
                'unit' => 'm3',
                'source' => 'MITECO',
                'year' => 2023,
                'factor_kwh' => 0.186,
            ],
            'IT' => [
                'factor' => 2.00, // kg CO2e/m³
                'unit' => 'm3',
                'source' => 'ISPRA',
                'year' => 2023,
                'factor_kwh' => 0.184,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Aggregation
    |--------------------------------------------------------------------------
    |
    | How to aggregate energy consumption data
    |
    */

    'aggregation' => [
        // Granularity levels available
        'levels' => ['hourly', 'daily', 'monthly', 'yearly'],

        // Default display granularity
        'default' => 'daily',

        // Store raw data at this granularity
        'storage' => 'hourly',
    ],

    /*
    |--------------------------------------------------------------------------
    | Consent Configuration
    |--------------------------------------------------------------------------
    |
    | User consent is required for accessing energy data
    |
    */

    'consent' => [
        // Consent duration in months
        'duration_months' => 12,

        // Remind before expiry (days)
        'reminder_days' => 30,
    ],

];

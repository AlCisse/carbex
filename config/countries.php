<?php

/**
 * LinsCarbon - Multi-Country Configuration
 *
 * Supported countries: France (FR), Germany (DE), Belgium (BE),
 * Netherlands (NL), Austria (AT), Switzerland (CH), Spain (ES), Italy (IT)
 *
 * Each country has specific VAT rates, date/number formats,
 * regulatory requirements, and emission factor sources.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Country
    |--------------------------------------------------------------------------
    */

    'default' => env('CARBEX_DEFAULT_COUNTRY', 'FR'),

    /*
    |--------------------------------------------------------------------------
    | Supported Countries
    |--------------------------------------------------------------------------
    */

    'supported' => explode(',', env('CARBEX_SUPPORTED_COUNTRIES', 'FR,DE')),

    /*
    |--------------------------------------------------------------------------
    | Country Configurations
    |--------------------------------------------------------------------------
    */

    'countries' => [

        /*
        |----------------------------------------------------------------------
        | France
        |----------------------------------------------------------------------
        */
        'FR' => [
            'name' => 'France',
            'native_name' => 'France',
            'locale' => 'fr_FR',
            'language' => 'fr',
            'timezone' => 'Europe/Paris',
            'currency' => 'EUR',
            'currency_symbol' => '€',
            'currency_position' => 'after', // 100 €

            // VAT Rates
            'vat' => [
                'standard' => 20.0,
                'reduced' => 10.0,
                'super_reduced' => 5.5,
                'zero' => 0.0,
            ],

            // Number Formatting
            'number_format' => [
                'decimal_separator' => ',',
                'thousands_separator' => ' ',
                'decimals' => 2,
            ],

            // Date Formatting
            'date_format' => [
                'short' => 'd/m/Y',
                'long' => 'd F Y',
                'datetime' => 'd/m/Y H:i',
                'carbon' => 'DD/MM/YYYY',
            ],

            // Regulatory Framework
            'regulations' => [
                'carbon_reporting' => 'BEGES', // Bilan d'Émissions de Gaz à Effet de Serre
                'csrd_applicable' => true,
                'mandatory_threshold' => 500, // employees
                'fiscal_year_default' => 'calendar', // calendar or custom
            ],

            // Emission Factor Sources
            'emission_sources' => [
                'primary' => 'ademe',
                'secondary' => 'ecoinvent',
            ],

            // Open Banking Provider
            'banking_provider' => 'bridge',

            // Business Registration
            'business_id' => [
                'name' => 'SIRET',
                'format' => '/^\d{14}$/',
                'example' => '12345678901234',
            ],

            // Contact Information
            'phone_prefix' => '+33',
            'phone_format' => '/^(?:\+33|0)[1-9](?:[0-9]{8})$/',

            // Legal Requirements
            'legal' => [
                'privacy_authority' => 'CNIL',
                'data_retention_years' => 10, // accounting documents
                'invoice_requirements' => [
                    'siret',
                    'vat_number',
                    'legal_form',
                    'capital',
                ],
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Germany
        |----------------------------------------------------------------------
        */
        'DE' => [
            'name' => 'Germany',
            'native_name' => 'Deutschland',
            'locale' => 'de_DE',
            'language' => 'de',
            'timezone' => 'Europe/Berlin',
            'currency' => 'EUR',
            'currency_symbol' => '€',
            'currency_position' => 'after', // 100 €

            // VAT Rates (Mehrwertsteuer)
            'vat' => [
                'standard' => 19.0,
                'reduced' => 7.0,
                'zero' => 0.0,
            ],

            // Number Formatting
            'number_format' => [
                'decimal_separator' => ',',
                'thousands_separator' => '.',
                'decimals' => 2,
            ],

            // Date Formatting
            'date_format' => [
                'short' => 'd.m.Y',
                'long' => 'd. F Y',
                'datetime' => 'd.m.Y H:i',
                'carbon' => 'DD.MM.YYYY',
            ],

            // Regulatory Framework
            'regulations' => [
                'carbon_reporting' => 'DSGVO_CO2', // German CO2 reporting
                'csrd_applicable' => true,
                'mandatory_threshold' => 500, // employees
                'fiscal_year_default' => 'calendar',
            ],

            // Emission Factor Sources
            'emission_sources' => [
                'primary' => 'uba', // Umweltbundesamt
                'secondary' => 'ecoinvent',
            ],

            // Open Banking Provider
            'banking_provider' => 'finapi',

            // Business Registration
            'business_id' => [
                'name' => 'Handelsregisternummer',
                'format' => '/^HRB?\s?\d+$/',
                'example' => 'HRB 12345',
            ],

            // Contact Information
            'phone_prefix' => '+49',
            'phone_format' => '/^(?:\+49|0)[1-9](?:[0-9]{9,10})$/',

            // Legal Requirements
            'legal' => [
                'privacy_authority' => 'BfDI',
                'data_retention_years' => 10, // GoBD requirements
                'invoice_requirements' => [
                    'handelsregister',
                    'vat_number',
                    'legal_form',
                    'geschaeftsfuehrer',
                ],
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Belgium
        |----------------------------------------------------------------------
        */
        'BE' => [
            'name' => 'Belgium',
            'native_name' => 'België / Belgique',
            'locale' => 'fr_BE',
            'language' => 'fr', // Also nl and de
            'timezone' => 'Europe/Brussels',
            'currency' => 'EUR',
            'currency_symbol' => '€',
            'currency_position' => 'after',

            // VAT Rates (BTW/TVA)
            'vat' => [
                'standard' => 21.0,
                'reduced' => 12.0,
                'super_reduced' => 6.0,
                'zero' => 0.0,
            ],

            // Number Formatting
            'number_format' => [
                'decimal_separator' => ',',
                'thousands_separator' => '.',
                'decimals' => 2,
            ],

            // Date Formatting
            'date_format' => [
                'short' => 'd/m/Y',
                'long' => 'd F Y',
                'datetime' => 'd/m/Y H:i',
                'carbon' => 'DD/MM/YYYY',
            ],

            // Regulatory Framework
            'regulations' => [
                'carbon_reporting' => 'PRTR', // Pollutant Release and Transfer Register
                'csrd_applicable' => true,
                'mandatory_threshold' => 500,
                'fiscal_year_default' => 'calendar',
            ],

            // Emission Factor Sources
            'emission_sources' => [
                'primary' => 'vito', // Flemish Institute for Technological Research
                'secondary' => 'ecoinvent',
            ],

            // Open Banking Provider
            'banking_provider' => 'finapi',

            // Business Registration
            'business_id' => [
                'name' => 'BCE/KBO',
                'format' => '/^0[0-9]{9}$/',
                'example' => '0123456789',
            ],

            // Contact Information
            'phone_prefix' => '+32',
            'phone_format' => '/^(?:\+32|0)[1-9](?:[0-9]{7,8})$/',

            // Legal Requirements
            'legal' => [
                'privacy_authority' => 'APD/GBA',
                'data_retention_years' => 7,
                'invoice_requirements' => [
                    'bce_number',
                    'vat_number',
                    'legal_form',
                ],
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Netherlands
        |----------------------------------------------------------------------
        */
        'NL' => [
            'name' => 'Netherlands',
            'native_name' => 'Nederland',
            'locale' => 'nl_NL',
            'language' => 'nl',
            'timezone' => 'Europe/Amsterdam',
            'currency' => 'EUR',
            'currency_symbol' => '€',
            'currency_position' => 'before', // € 100

            // VAT Rates (BTW)
            'vat' => [
                'standard' => 21.0,
                'reduced' => 9.0,
                'zero' => 0.0,
            ],

            // Number Formatting
            'number_format' => [
                'decimal_separator' => ',',
                'thousands_separator' => '.',
                'decimals' => 2,
            ],

            // Date Formatting
            'date_format' => [
                'short' => 'd-m-Y',
                'long' => 'd F Y',
                'datetime' => 'd-m-Y H:i',
                'carbon' => 'DD-MM-YYYY',
            ],

            // Regulatory Framework
            'regulations' => [
                'carbon_reporting' => 'NEa', // Nederlandse Emissieautoriteit
                'csrd_applicable' => true,
                'mandatory_threshold' => 500,
                'fiscal_year_default' => 'calendar',
            ],

            // Emission Factor Sources
            'emission_sources' => [
                'primary' => 'ce_delft', // CE Delft
                'secondary' => 'ecoinvent',
            ],

            // Open Banking Provider
            'banking_provider' => 'finapi',

            // Business Registration
            'business_id' => [
                'name' => 'KvK-nummer',
                'format' => '/^[0-9]{8}$/',
                'example' => '12345678',
            ],

            // Contact Information
            'phone_prefix' => '+31',
            'phone_format' => '/^(?:\+31|0)[1-9](?:[0-9]{8})$/',

            // Legal Requirements
            'legal' => [
                'privacy_authority' => 'AP',
                'data_retention_years' => 7,
                'invoice_requirements' => [
                    'kvk_number',
                    'vat_number',
                    'legal_form',
                ],
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Austria
        |----------------------------------------------------------------------
        */
        'AT' => [
            'name' => 'Austria',
            'native_name' => 'Österreich',
            'locale' => 'de_AT',
            'language' => 'de',
            'timezone' => 'Europe/Vienna',
            'currency' => 'EUR',
            'currency_symbol' => '€',
            'currency_position' => 'before', // € 100

            // VAT Rates (USt)
            'vat' => [
                'standard' => 20.0,
                'reduced' => 13.0,
                'super_reduced' => 10.0,
                'zero' => 0.0,
            ],

            // Number Formatting
            'number_format' => [
                'decimal_separator' => ',',
                'thousands_separator' => '.',
                'decimals' => 2,
            ],

            // Date Formatting
            'date_format' => [
                'short' => 'd.m.Y',
                'long' => 'd. F Y',
                'datetime' => 'd.m.Y H:i',
                'carbon' => 'DD.MM.YYYY',
            ],

            // Regulatory Framework
            'regulations' => [
                'carbon_reporting' => 'EZG', // Emissionszertifikategesetz
                'csrd_applicable' => true,
                'mandatory_threshold' => 500,
                'fiscal_year_default' => 'calendar',
            ],

            // Emission Factor Sources
            'emission_sources' => [
                'primary' => 'umweltbundesamt_at', // Austrian Environment Agency
                'secondary' => 'ecoinvent',
            ],

            // Open Banking Provider
            'banking_provider' => 'finapi',

            // Business Registration
            'business_id' => [
                'name' => 'Firmenbuchnummer',
                'format' => '/^FN\s?\d+[a-z]$/i',
                'example' => 'FN 123456a',
            ],

            // Contact Information
            'phone_prefix' => '+43',
            'phone_format' => '/^(?:\+43|0)[1-9](?:[0-9]{6,10})$/',

            // Legal Requirements
            'legal' => [
                'privacy_authority' => 'DSB',
                'data_retention_years' => 7,
                'invoice_requirements' => [
                    'firmenbuch',
                    'vat_number',
                    'legal_form',
                ],
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Switzerland
        |----------------------------------------------------------------------
        */
        'CH' => [
            'name' => 'Switzerland',
            'native_name' => 'Schweiz / Suisse',
            'locale' => 'de_CH',
            'language' => 'de', // Also fr, it, rm
            'timezone' => 'Europe/Zurich',
            'currency' => 'CHF',
            'currency_symbol' => 'CHF',
            'currency_position' => 'before', // CHF 100

            // VAT Rates (MWST/TVA)
            'vat' => [
                'standard' => 8.1,
                'reduced' => 2.6,
                'special' => 3.8, // Accommodation
                'zero' => 0.0,
            ],

            // Number Formatting
            'number_format' => [
                'decimal_separator' => '.',
                'thousands_separator' => "'",
                'decimals' => 2,
            ],

            // Date Formatting
            'date_format' => [
                'short' => 'd.m.Y',
                'long' => 'd. F Y',
                'datetime' => 'd.m.Y H:i',
                'carbon' => 'DD.MM.YYYY',
            ],

            // Regulatory Framework
            'regulations' => [
                'carbon_reporting' => 'CO2_Verordnung', // Swiss CO2 Ordinance
                'csrd_applicable' => false, // Not EU member
                'mandatory_threshold' => 500,
                'fiscal_year_default' => 'calendar',
            ],

            // Emission Factor Sources
            'emission_sources' => [
                'primary' => 'bafu', // Bundesamt für Umwelt
                'secondary' => 'ecoinvent',
            ],

            // Open Banking Provider
            'banking_provider' => 'finapi',

            // Business Registration
            'business_id' => [
                'name' => 'UID',
                'format' => '/^CHE-\d{3}\.\d{3}\.\d{3}$/',
                'example' => 'CHE-123.456.789',
            ],

            // Contact Information
            'phone_prefix' => '+41',
            'phone_format' => '/^(?:\+41|0)[1-9](?:[0-9]{8})$/',

            // Legal Requirements
            'legal' => [
                'privacy_authority' => 'FDPIC',
                'data_retention_years' => 10,
                'invoice_requirements' => [
                    'uid',
                    'vat_number',
                    'legal_form',
                ],
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Spain
        |----------------------------------------------------------------------
        */
        'ES' => [
            'name' => 'Spain',
            'native_name' => 'España',
            'locale' => 'es_ES',
            'language' => 'es',
            'timezone' => 'Europe/Madrid',
            'currency' => 'EUR',
            'currency_symbol' => '€',
            'currency_position' => 'after', // 100 €

            // VAT Rates (IVA)
            'vat' => [
                'standard' => 21.0,
                'reduced' => 10.0,
                'super_reduced' => 4.0,
                'zero' => 0.0,
            ],

            // Number Formatting
            'number_format' => [
                'decimal_separator' => ',',
                'thousands_separator' => '.',
                'decimals' => 2,
            ],

            // Date Formatting
            'date_format' => [
                'short' => 'd/m/Y',
                'long' => 'd \\de F \\de Y',
                'datetime' => 'd/m/Y H:i',
                'carbon' => 'DD/MM/YYYY',
            ],

            // Regulatory Framework
            'regulations' => [
                'carbon_reporting' => 'PRTR_ES', // Registro PRTR España
                'csrd_applicable' => true,
                'mandatory_threshold' => 500,
                'fiscal_year_default' => 'calendar',
            ],

            // Emission Factor Sources
            'emission_sources' => [
                'primary' => 'miteco', // Ministerio para la Transición Ecológica
                'secondary' => 'ecoinvent',
            ],

            // Open Banking Provider
            'banking_provider' => 'finapi',

            // Business Registration
            'business_id' => [
                'name' => 'CIF',
                'format' => '/^[A-Z][0-9]{7}[A-Z0-9]$/',
                'example' => 'B12345678',
            ],

            // Contact Information
            'phone_prefix' => '+34',
            'phone_format' => '/^(?:\+34)?[6-9][0-9]{8}$/',

            // Legal Requirements
            'legal' => [
                'privacy_authority' => 'AEPD',
                'data_retention_years' => 6,
                'invoice_requirements' => [
                    'cif',
                    'vat_number',
                    'legal_form',
                    'registro_mercantil',
                ],
            ],
        ],

        /*
        |----------------------------------------------------------------------
        | Italy
        |----------------------------------------------------------------------
        */
        'IT' => [
            'name' => 'Italy',
            'native_name' => 'Italia',
            'locale' => 'it_IT',
            'language' => 'it',
            'timezone' => 'Europe/Rome',
            'currency' => 'EUR',
            'currency_symbol' => '€',
            'currency_position' => 'after', // 100 €

            // VAT Rates (IVA)
            'vat' => [
                'standard' => 22.0,
                'reduced' => 10.0,
                'super_reduced' => 5.0,
                'minimum' => 4.0,
                'zero' => 0.0,
            ],

            // Number Formatting
            'number_format' => [
                'decimal_separator' => ',',
                'thousands_separator' => '.',
                'decimals' => 2,
            ],

            // Date Formatting
            'date_format' => [
                'short' => 'd/m/Y',
                'long' => 'd F Y',
                'datetime' => 'd/m/Y H:i',
                'carbon' => 'DD/MM/YYYY',
            ],

            // Regulatory Framework
            'regulations' => [
                'carbon_reporting' => 'ISPRA', // Istituto Superiore per la Protezione e la Ricerca Ambientale
                'csrd_applicable' => true,
                'mandatory_threshold' => 500,
                'fiscal_year_default' => 'calendar',
            ],

            // Emission Factor Sources
            'emission_sources' => [
                'primary' => 'ispra', // ISPRA
                'secondary' => 'ecoinvent',
            ],

            // Open Banking Provider
            'banking_provider' => 'finapi',

            // Business Registration
            'business_id' => [
                'name' => 'Partita IVA',
                'format' => '/^IT[0-9]{11}$/',
                'example' => 'IT12345678901',
            ],

            // Contact Information
            'phone_prefix' => '+39',
            'phone_format' => '/^(?:\+39)?[0-9]{6,12}$/',

            // Legal Requirements
            'legal' => [
                'privacy_authority' => 'Garante',
                'data_retention_years' => 10,
                'invoice_requirements' => [
                    'partita_iva',
                    'codice_fiscale',
                    'legal_form',
                    'rea',
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | SME Size Categories (EU Definition)
    |--------------------------------------------------------------------------
    */

    'sme_categories' => [
        'micro' => [
            'max_employees' => 10,
            'max_turnover' => 2_000_000, // EUR
            'max_balance' => 2_000_000,
        ],
        'small' => [
            'max_employees' => 50,
            'max_turnover' => 10_000_000,
            'max_balance' => 10_000_000,
        ],
        'medium' => [
            'max_employees' => 250,
            'max_turnover' => 50_000_000,
            'max_balance' => 43_000_000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Industry Sectors (NACE Rev. 2)
    |--------------------------------------------------------------------------
    */

    'sectors' => [
        'A' => 'agriculture',
        'B' => 'mining',
        'C' => 'manufacturing',
        'D' => 'electricity_gas',
        'E' => 'water_waste',
        'F' => 'construction',
        'G' => 'wholesale_retail',
        'H' => 'transport_storage',
        'I' => 'accommodation_food',
        'J' => 'information_communication',
        'K' => 'financial_insurance',
        'L' => 'real_estate',
        'M' => 'professional_services',
        'N' => 'administrative_services',
        'O' => 'public_administration',
        'P' => 'education',
        'Q' => 'health_social',
        'R' => 'arts_entertainment',
        'S' => 'other_services',
    ],

];

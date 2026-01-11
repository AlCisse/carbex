<?php

/**
 * Carbex - Emission Factors Configuration
 *
 * Configuration for emission factor sources, categories,
 * and calculation parameters for carbon footprint calculation.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Emission Factor Sources
    |--------------------------------------------------------------------------
    */

    'sources' => [

        /*
        |----------------------------------------------------------------------
        | ADEME - France (Base Carbone)
        |----------------------------------------------------------------------
        */
        'ademe' => [
            'name' => 'ADEME Base Carbone',
            'country' => 'FR',
            'url' => 'https://base-empreinte.ademe.fr/',
            'api_url' => env('CARBEX_ADEME_API_URL', 'https://data.ademe.fr/data-fair/api/v1/datasets'),
            'api_key' => env('CARBEX_ADEME_API_KEY'),
            'dataset_id' => 'base-carbone(r)',
            'version' => '23.0',
            'last_update' => '2024-01-01',
            'license' => 'Open License 2.0',
            'priority' => 1,
            'enabled' => true,
        ],

        /*
        |----------------------------------------------------------------------
        | UBA - Germany (Umweltbundesamt)
        |----------------------------------------------------------------------
        */
        'uba' => [
            'name' => 'Umweltbundesamt CO2 Rechner',
            'country' => 'DE',
            'url' => 'https://www.umweltbundesamt.de/themen/klima-energie/treibhausgas-emissionen',
            'api_url' => null, // Manual import required
            'version' => '2024',
            'last_update' => '2024-01-01',
            'license' => 'Public Domain',
            'priority' => 1,
            'enabled' => true,
        ],

        /*
        |----------------------------------------------------------------------
        | Ecoinvent (International)
        |----------------------------------------------------------------------
        */
        'ecoinvent' => [
            'name' => 'Ecoinvent',
            'country' => 'INTL',
            'url' => 'https://ecoinvent.org/',
            'api_url' => null, // Requires license
            'version' => '3.10',
            'last_update' => '2024-01-01',
            'license' => 'Commercial',
            'priority' => 2,
            'enabled' => false, // Requires subscription
        ],

        /*
        |----------------------------------------------------------------------
        | GHG Protocol (Fallback)
        |----------------------------------------------------------------------
        */
        'ghg_protocol' => [
            'name' => 'GHG Protocol Emission Factors',
            'country' => 'INTL',
            'url' => 'https://ghgprotocol.org/calculation-tools',
            'api_url' => null,
            'version' => '2024',
            'last_update' => '2024-01-01',
            'license' => 'Open',
            'priority' => 3,
            'enabled' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | GHG Protocol Scopes
    |--------------------------------------------------------------------------
    */

    'scopes' => [
        1 => [
            'name' => 'scope_1',
            'label' => 'Direct Emissions',
            'description' => 'Direct GHG emissions from sources owned or controlled by the company',
            'categories' => [
                'stationary_combustion',
                'mobile_combustion',
                'fugitive_emissions',
                'process_emissions',
            ],
        ],
        2 => [
            'name' => 'scope_2',
            'label' => 'Indirect Emissions - Energy',
            'description' => 'Indirect GHG emissions from purchased electricity, steam, heating, and cooling',
            'categories' => [
                'purchased_electricity',
                'purchased_heat',
                'purchased_steam',
                'purchased_cooling',
            ],
            'methods' => [
                'location_based' => 'Uses grid average emission factor',
                'market_based' => 'Uses supplier-specific or residual mix factor',
            ],
        ],
        3 => [
            'name' => 'scope_3',
            'label' => 'Other Indirect Emissions',
            'description' => 'All other indirect emissions in the value chain',
            'categories' => [
                // Upstream
                'purchased_goods_services',      // Cat 1
                'capital_goods',                  // Cat 2
                'fuel_energy_activities',         // Cat 3
                'upstream_transport',             // Cat 4
                'waste_operations',               // Cat 5
                'business_travel',                // Cat 6
                'employee_commuting',             // Cat 7
                'upstream_leased_assets',         // Cat 8
                // Downstream
                'downstream_transport',           // Cat 9
                'processing_sold_products',       // Cat 10
                'use_sold_products',              // Cat 11
                'end_of_life_treatment',          // Cat 12
                'downstream_leased_assets',       // Cat 13
                'franchises',                     // Cat 14
                'investments',                    // Cat 15
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Emission Categories (Mapped to MCC Codes)
    |--------------------------------------------------------------------------
    */

    'categories' => [

        // Scope 1 - Direct Emissions
        'fuel_gasoline' => [
            'scope' => 1,
            'ghg_category' => 'mobile_combustion',
            'unit' => 'liter',
            'mcc_codes' => [5541, 5542, 5983], // Gas stations
            'keywords' => ['essence', 'gasoline', 'benzin', 'fuel', 'carburant'],
        ],

        'fuel_diesel' => [
            'scope' => 1,
            'ghg_category' => 'mobile_combustion',
            'unit' => 'liter',
            'mcc_codes' => [5541, 5542, 5983],
            'keywords' => ['diesel', 'gazole', 'gasoil'],
        ],

        'natural_gas' => [
            'scope' => 1,
            'ghg_category' => 'stationary_combustion',
            'unit' => 'kWh',
            'mcc_codes' => [4900],
            'keywords' => ['gaz naturel', 'natural gas', 'erdgas', 'engie', 'grdf'],
        ],

        // Scope 2 - Energy
        'electricity' => [
            'scope' => 2,
            'ghg_category' => 'purchased_electricity',
            'unit' => 'kWh',
            'mcc_codes' => [4900],
            'keywords' => ['électricité', 'electricity', 'strom', 'edf', 'enedis'],
        ],

        'district_heating' => [
            'scope' => 2,
            'ghg_category' => 'purchased_heat',
            'unit' => 'kWh',
            'mcc_codes' => [4900],
            'keywords' => ['chauffage urbain', 'district heating', 'fernwärme'],
        ],

        // Scope 3 - Indirect
        'business_travel_air' => [
            'scope' => 3,
            'ghg_category' => 'business_travel',
            'unit' => 'km',
            'mcc_codes' => [3000, 3001, 3002, 3003, 4511, 4512],
            'keywords' => ['air france', 'lufthansa', 'flight', 'vol', 'flug', 'airline'],
        ],

        'business_travel_rail' => [
            'scope' => 3,
            'ghg_category' => 'business_travel',
            'unit' => 'km',
            'mcc_codes' => [4011, 4112],
            'keywords' => ['sncf', 'deutsche bahn', 'train', 'tgv', 'ice', 'rail'],
        ],

        'business_travel_hotel' => [
            'scope' => 3,
            'ghg_category' => 'business_travel',
            'unit' => 'night',
            'mcc_codes' => [3501, 3502, 3503, 3504, 3505, 7011],
            'keywords' => ['hotel', 'hôtel', 'ibis', 'novotel', 'marriott', 'hilton'],
        ],

        'purchased_goods_office' => [
            'scope' => 3,
            'ghg_category' => 'purchased_goods_services',
            'unit' => 'EUR',
            'mcc_codes' => [5111, 5943, 5044],
            'keywords' => ['office depot', 'staples', 'fournitures', 'bürobedarf'],
        ],

        'purchased_goods_it' => [
            'scope' => 3,
            'ghg_category' => 'purchased_goods_services',
            'unit' => 'EUR',
            'mcc_codes' => [5045, 5732, 5734],
            'keywords' => ['apple', 'dell', 'hp', 'lenovo', 'computer', 'ordinateur'],
        ],

        'cloud_services' => [
            'scope' => 3,
            'ghg_category' => 'purchased_goods_services',
            'unit' => 'EUR',
            'mcc_codes' => [5734, 7372, 7379],
            'keywords' => ['aws', 'azure', 'google cloud', 'scaleway', 'ovh', 'saas'],
        ],

        'waste_general' => [
            'scope' => 3,
            'ghg_category' => 'waste_operations',
            'unit' => 'kg',
            'mcc_codes' => [4214, 7349],
            'keywords' => ['déchets', 'waste', 'abfall', 'recycling'],
        ],

        'delivery_courier' => [
            'scope' => 3,
            'ghg_category' => 'upstream_transport',
            'unit' => 'shipment',
            'mcc_codes' => [4215, 4225],
            'keywords' => ['ups', 'fedex', 'dhl', 'chronopost', 'colissimo', 'hermes'],
        ],

        'restaurant_meals' => [
            'scope' => 3,
            'ghg_category' => 'purchased_goods_services',
            'unit' => 'meal',
            'mcc_codes' => [5812, 5813, 5814],
            'keywords' => ['restaurant', 'café', 'bistro', 'cantine'],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Unit Conversions
    |--------------------------------------------------------------------------
    */

    'units' => [
        'energy' => [
            'kWh' => 1,
            'MWh' => 1000,
            'GJ' => 277.778,
            'MJ' => 0.277778,
        ],
        'volume' => [
            'liter' => 1,
            'm3' => 1000,
            'gallon_us' => 3.78541,
            'gallon_uk' => 4.54609,
        ],
        'mass' => [
            'kg' => 1,
            'tonne' => 1000,
            'lb' => 0.453592,
        ],
        'distance' => [
            'km' => 1,
            'mile' => 1.60934,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CO2 Equivalent Factors (GWP 100 years - AR6)
    |--------------------------------------------------------------------------
    */

    'gwp' => [
        'CO2' => 1,
        'CH4' => 27.9,      // Methane (fossil)
        'CH4_bio' => 27.2,  // Methane (biogenic)
        'N2O' => 273,       // Nitrous oxide
        'SF6' => 25200,     // Sulfur hexafluoride
        'HFC-134a' => 1526,
        'HFC-32' => 771,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Emission Factors (kgCO2e per unit)
    |--------------------------------------------------------------------------
    | Used as fallback when no specific factor is found
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'FR' => [
            'electricity_kwh' => 0.0569,      // France (nuclear heavy)
            'natural_gas_kwh' => 0.227,
            'diesel_liter' => 3.16,
            'gasoline_liter' => 2.80,
            'flight_short_km' => 0.258,       // <1000km
            'flight_medium_km' => 0.187,      // 1000-3500km
            'flight_long_km' => 0.152,        // >3500km
            'train_km' => 0.00373,            // TGV
            'hotel_night' => 19.5,
            'meal_average' => 2.5,
        ],
        'DE' => [
            'electricity_kwh' => 0.420,       // Germany (coal heavy)
            'natural_gas_kwh' => 0.227,
            'diesel_liter' => 3.16,
            'gasoline_liter' => 2.80,
            'flight_short_km' => 0.258,
            'flight_medium_km' => 0.187,
            'flight_long_km' => 0.152,
            'train_km' => 0.032,              // DB average
            'hotel_night' => 22.0,
            'meal_average' => 2.8,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Confidence Thresholds
    |--------------------------------------------------------------------------
    */

    'confidence' => [
        'high' => 0.90,      // Automatic validation
        'medium' => 0.70,    // Suggested, needs review
        'low' => 0.50,       // Needs manual validation
        'minimum' => 0.30,   // Below this, use fallback
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'enabled' => true,
        'ttl' => 86400,        // 24 hours
        'prefix' => 'carbex_ef_',
    ],

];

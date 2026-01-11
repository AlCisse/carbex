<?php

/**
 * MCC (Merchant Category Code) to Emission Category Mapping
 *
 * This configuration maps standard Merchant Category Codes used in bank transactions
 * to Carbex emission categories for automatic transaction categorization.
 *
 * Each MCC is mapped to:
 * - category: The emission category (matches emission_factors table)
 * - subcategory: Optional subcategory for more precise classification
 * - scope: GHG Protocol scope (1, 2, or 3)
 * - confidence: Confidence level of the mapping (high, medium, low)
 * - keywords: Additional keywords to help with merchant name matching
 *
 * @see https://www.citibank.com/tts/solutions/commercial-cards/assets/docs/govt/Merchant-Category-Codes.pdf
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Transportation (MCC 4000-4999)
    |--------------------------------------------------------------------------
    */

    // Airlines
    '3000-3350' => [
        'category' => 'business_travel',
        'subcategory' => 'air_travel',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['airline', 'airways', 'aviation'],
    ],
    '4511' => [
        'category' => 'business_travel',
        'subcategory' => 'air_travel',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['airline', 'airways', 'flight'],
    ],

    // Railways
    '4011' => [
        'category' => 'business_travel',
        'subcategory' => 'rail_travel',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['rail', 'train', 'sncf', 'eurostar'],
    ],
    '4112' => [
        'category' => 'business_travel',
        'subcategory' => 'rail_travel',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['passenger railway'],
    ],

    // Taxis & Rideshare
    '4121' => [
        'category' => 'business_travel',
        'subcategory' => 'taxi',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['taxi', 'uber', 'lyft', 'bolt', 'kapten'],
    ],

    // Bus Lines
    '4131' => [
        'category' => 'business_travel',
        'subcategory' => 'bus_travel',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['bus', 'coach', 'flixbus'],
    ],

    // Vehicle Rental
    '7512' => [
        'category' => 'business_travel',
        'subcategory' => 'car_rental',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['rental', 'hertz', 'avis', 'europcar', 'enterprise'],
    ],
    '7513' => [
        'category' => 'business_travel',
        'subcategory' => 'truck_rental',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['truck rental', 'van rental'],
    ],

    // Parking
    '7523' => [
        'category' => 'company_vehicles',
        'subcategory' => 'parking',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['parking', 'garage'],
    ],

    // Toll
    '4784' => [
        'category' => 'company_vehicles',
        'subcategory' => 'tolls',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['toll', 'peage', 'autoroute'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fuel & Energy (MCC 5500-5599)
    |--------------------------------------------------------------------------
    */

    // Service Stations
    '5541' => [
        'category' => 'company_vehicles',
        'subcategory' => 'fuel',
        'scope' => 1,
        'confidence' => 'high',
        'keywords' => ['gas', 'petrol', 'fuel', 'shell', 'total', 'bp', 'esso'],
    ],
    '5542' => [
        'category' => 'company_vehicles',
        'subcategory' => 'fuel',
        'scope' => 1,
        'confidence' => 'high',
        'keywords' => ['fuel', 'automated fuel'],
    ],

    // Electric Vehicle Charging
    '5552' => [
        'category' => 'company_vehicles',
        'subcategory' => 'ev_charging',
        'scope' => 2,
        'confidence' => 'high',
        'keywords' => ['charging', 'ev', 'tesla', 'ionity', 'chargepoint'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Utilities (MCC 4800-4899)
    |--------------------------------------------------------------------------
    */

    // Electric Companies
    '4900' => [
        'category' => 'electricity',
        'subcategory' => 'grid_electricity',
        'scope' => 2,
        'confidence' => 'high',
        'keywords' => ['electric', 'edf', 'engie', 'power', 'energy'],
    ],

    // Gas Companies
    '4814' => [
        'category' => 'heating',
        'subcategory' => 'natural_gas',
        'scope' => 1,
        'confidence' => 'high',
        'keywords' => ['gas', 'gaz', 'natural gas', 'grdf'],
    ],

    // Water
    '4816' => [
        'category' => 'water',
        'subcategory' => 'municipal_water',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['water', 'eau', 'veolia'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Hotels & Lodging (MCC 3500-3999, 7011)
    |--------------------------------------------------------------------------
    */

    '3501-3999' => [
        'category' => 'business_travel',
        'subcategory' => 'hotel',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['hotel', 'inn', 'lodge', 'resort', 'marriott', 'hilton', 'accor'],
    ],
    '7011' => [
        'category' => 'business_travel',
        'subcategory' => 'hotel',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['hotel', 'motel', 'lodging'],
    ],
    '7012' => [
        'category' => 'business_travel',
        'subcategory' => 'timeshare',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['timeshare', 'vacation'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Office Supplies & Equipment (MCC 5000-5099)
    |--------------------------------------------------------------------------
    */

    '5111' => [
        'category' => 'purchased_goods',
        'subcategory' => 'stationery',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['stationery', 'paper', 'office supplies'],
    ],
    '5943' => [
        'category' => 'purchased_goods',
        'subcategory' => 'office_supplies',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['office', 'staples', 'bureau'],
    ],
    '5044' => [
        'category' => 'purchased_goods',
        'subcategory' => 'office_equipment',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['copier', 'printer', 'office equipment'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Computer & Electronics (MCC 5045, 5732, 5734)
    |--------------------------------------------------------------------------
    */

    '5045' => [
        'category' => 'purchased_goods',
        'subcategory' => 'computer_equipment',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['computer', 'hardware', 'dell', 'hp', 'lenovo'],
    ],
    '5732' => [
        'category' => 'purchased_goods',
        'subcategory' => 'electronics',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['electronics', 'fnac', 'darty', 'boulanger'],
    ],
    '5734' => [
        'category' => 'purchased_goods',
        'subcategory' => 'software',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['software', 'microsoft', 'adobe'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Telecommunications (MCC 4812-4816)
    |--------------------------------------------------------------------------
    */

    '4812' => [
        'category' => 'purchased_services',
        'subcategory' => 'telecom',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['telecom', 'mobile', 'orange', 'sfr', 'bouygues'],
    ],
    '4813' => [
        'category' => 'purchased_services',
        'subcategory' => 'telecom',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['telecom', 'internet', 'phone'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Professional Services (MCC 7300-7399)
    |--------------------------------------------------------------------------
    */

    '7311' => [
        'category' => 'purchased_services',
        'subcategory' => 'advertising',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['advertising', 'marketing', 'agency'],
    ],
    '7333' => [
        'category' => 'purchased_services',
        'subcategory' => 'commercial_photography',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['photography', 'photo', 'studio'],
    ],
    '7338' => [
        'category' => 'purchased_services',
        'subcategory' => 'printing',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['printing', 'copy', 'reproduction'],
    ],
    '7361' => [
        'category' => 'purchased_services',
        'subcategory' => 'employment_agency',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['employment', 'staffing', 'recruitment'],
    ],
    '7372' => [
        'category' => 'purchased_services',
        'subcategory' => 'software_services',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['software', 'saas', 'cloud'],
    ],
    '7379' => [
        'category' => 'purchased_services',
        'subcategory' => 'computer_services',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['it services', 'consulting', 'technology'],
    ],
    '7392' => [
        'category' => 'purchased_services',
        'subcategory' => 'consulting',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['consulting', 'conseil', 'management'],
    ],
    '7393' => [
        'category' => 'purchased_services',
        'subcategory' => 'security_services',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['security', 'detective', 'protection'],
    ],
    '7394' => [
        'category' => 'purchased_services',
        'subcategory' => 'equipment_rental',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['rental', 'leasing', 'equipment'],
    ],
    '8111' => [
        'category' => 'purchased_services',
        'subcategory' => 'legal_services',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['legal', 'lawyer', 'avocat', 'attorney'],
    ],
    '8931' => [
        'category' => 'purchased_services',
        'subcategory' => 'accounting',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['accounting', 'comptable', 'audit'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Restaurants & Catering (MCC 5811-5814)
    |--------------------------------------------------------------------------
    */

    '5811' => [
        'category' => 'food_catering',
        'subcategory' => 'catering',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['catering', 'traiteur'],
    ],
    '5812' => [
        'category' => 'food_catering',
        'subcategory' => 'restaurant',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['restaurant', 'dining', 'bistro'],
    ],
    '5813' => [
        'category' => 'food_catering',
        'subcategory' => 'bar',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['bar', 'pub', 'drinking'],
    ],
    '5814' => [
        'category' => 'food_catering',
        'subcategory' => 'fast_food',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['fast food', 'quick service', 'mcdonald', 'burger'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipping & Freight (MCC 4214-4215)
    |--------------------------------------------------------------------------
    */

    '4214' => [
        'category' => 'freight',
        'subcategory' => 'trucking',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['trucking', 'transport', 'moving', 'delivery'],
    ],
    '4215' => [
        'category' => 'freight',
        'subcategory' => 'courier',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['courier', 'dhl', 'fedex', 'ups', 'chronopost', 'colissimo'],
    ],
    '4225' => [
        'category' => 'freight',
        'subcategory' => 'warehousing',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['warehouse', 'storage', 'logistics'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Building & Construction (MCC 1520-1799)
    |--------------------------------------------------------------------------
    */

    '1520' => [
        'category' => 'capital_goods',
        'subcategory' => 'construction',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['contractor', 'construction', 'building'],
    ],
    '1711' => [
        'category' => 'capital_goods',
        'subcategory' => 'hvac',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['heating', 'cooling', 'plumbing', 'hvac'],
    ],
    '1731' => [
        'category' => 'capital_goods',
        'subcategory' => 'electrical',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['electrical', 'electrician', 'wiring'],
    ],
    '1750' => [
        'category' => 'capital_goods',
        'subcategory' => 'carpentry',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['carpentry', 'woodwork', 'menuiserie'],
    ],
    '1761' => [
        'category' => 'capital_goods',
        'subcategory' => 'roofing',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['roofing', 'couvreur'],
    ],
    '1799' => [
        'category' => 'capital_goods',
        'subcategory' => 'specialty_trade',
        'scope' => 3,
        'confidence' => 'medium',
        'keywords' => ['specialty contractor', 'trade'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Waste Management (MCC 4950)
    |--------------------------------------------------------------------------
    */

    '4950' => [
        'category' => 'waste',
        'subcategory' => 'waste_management',
        'scope' => 3,
        'confidence' => 'high',
        'keywords' => ['waste', 'disposal', 'recycling', 'dechets', 'veolia'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cloud & Hosting Services
    |--------------------------------------------------------------------------
    */

    '7375' => [
        'category' => 'purchased_services',
        'subcategory' => 'data_processing',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => ['data processing', 'hosting', 'cloud', 'aws', 'azure', 'gcp'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Categories (Fallback)
    |--------------------------------------------------------------------------
    */

    'default_services' => [
        'category' => 'purchased_services',
        'subcategory' => 'other_services',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => [],
    ],

    'default_goods' => [
        'category' => 'purchased_goods',
        'subcategory' => 'other_goods',
        'scope' => 3,
        'confidence' => 'low',
        'keywords' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Emission Factors by Subcategory (kgCO2e per EUR)
    |--------------------------------------------------------------------------
    | These are spend-based emission factors when transaction amounts are known
    | but specific quantities are not. Values based on ADEME Base Carbone.
    */

    'emission_factors' => [
        // Transport (kgCO2e per EUR spent)
        'air_travel' => 0.257,           // Average air travel
        'rail_travel' => 0.025,          // Train
        'taxi' => 0.120,                 // Taxi/rideshare
        'bus_travel' => 0.045,           // Bus/coach
        'car_rental' => 0.150,           // Car rental
        'hotel' => 0.032,                // Hotel stays

        // Fuel (kgCO2e per EUR)
        'fuel' => 1.850,                 // Diesel/petrol
        'ev_charging' => 0.048,          // Electric charging

        // Energy
        'grid_electricity' => 0.057,     // kgCO2e per kWh (France mix)
        'natural_gas' => 0.227,          // kgCO2e per kWh

        // Goods
        'computer_equipment' => 0.285,
        'office_supplies' => 0.180,
        'electronics' => 0.350,

        // Services
        'consulting' => 0.045,
        'telecom' => 0.028,
        'software_services' => 0.022,

        // Freight
        'trucking' => 0.165,
        'courier' => 0.220,

        // Other
        'catering' => 0.095,
        'restaurant' => 0.085,
        'waste_management' => 0.350,
    ],

    /*
    |--------------------------------------------------------------------------
    | MCC Range Mappings
    |--------------------------------------------------------------------------
    | For MCC codes that span ranges (e.g., 3000-3350 for airlines)
    */

    'ranges' => [
        ['start' => 3000, 'end' => 3350, 'category' => 'business_travel', 'subcategory' => 'air_travel', 'scope' => 3],
        ['start' => 3351, 'end' => 3500, 'category' => 'business_travel', 'subcategory' => 'car_rental', 'scope' => 3],
        ['start' => 3501, 'end' => 3999, 'category' => 'business_travel', 'subcategory' => 'hotel', 'scope' => 3],
    ],
];

<?php

namespace App\Services\AI;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

/**
 * MCC Code Lookup Service
 *
 * Maps Merchant Category Codes (MCC) to emission categories.
 * MCC codes are 4-digit ISO 18245 codes used in payment processing.
 */
class MccCodeLookup
{
    /**
     * MCC code to category mapping.
     * Format: MCC code => category_code
     */
    private const MCC_MAPPING = [
        // Airlines (Scope 3 - Business Travel)
        '3000' => 'business_travel', // Airlines
        '3001' => 'business_travel', // Air France
        '3002' => 'business_travel', // American Airlines
        '3003' => 'business_travel', // British Airways
        '3004' => 'business_travel', // Delta
        '3005' => 'business_travel', // Japan Air Lines
        '3006' => 'business_travel', // Lufthansa
        '3007' => 'business_travel', // Northwest
        '3008' => 'business_travel', // KLM
        '3009' => 'business_travel', // United
        '3010' => 'business_travel', // Aeromexico
        '3011' => 'business_travel', // Alaska Airlines
        '3012' => 'business_travel', // US Airways
        '4511' => 'business_travel', // Airlines, Air Carriers

        // Rail (Scope 3 - Business Travel)
        '4011' => 'business_travel', // Railroads
        '4112' => 'business_travel', // Passenger Railways

        // Taxi/Limousine (Scope 3 - Business Travel)
        '4121' => 'business_travel', // Taxicabs and Limousines
        '4131' => 'business_travel', // Bus Lines

        // Car Rental (Scope 3 - Business Travel)
        '3351' => 'business_travel', // Car Rental (Hertz, Avis, etc.)
        '3352' => 'business_travel', // National
        '3353' => 'business_travel', // Budget
        '3354' => 'business_travel', // Enterprise
        '7512' => 'business_travel', // Car Rental Agencies

        // Hotels (Scope 3 - Business Travel)
        '3500' => 'business_travel', // Hotels/Motels
        '3501' => 'business_travel', // Hilton
        '3502' => 'business_travel', // Sheraton
        '3503' => 'business_travel', // Best Western
        '3504' => 'business_travel', // Holiday Inn
        '3505' => 'business_travel', // Marriott
        '3506' => 'business_travel', // Hyatt
        '3507' => 'business_travel', // Days Inn
        '3508' => 'business_travel', // Ramada
        '3509' => 'business_travel', // Howard Johnson
        '3510' => 'business_travel', // Radisson
        '3511' => 'business_travel', // Novotel
        '3512' => 'business_travel', // Accor
        '7011' => 'business_travel', // Lodging

        // Fuel/Gas Stations (Scope 1 - Direct)
        '5541' => 'fuel', // Service Stations
        '5542' => 'fuel', // Fuel Dispensers
        '5172' => 'fuel', // Petroleum Products

        // Electric Utilities (Scope 2)
        '4900' => 'electricity', // Utilities

        // Purchased Goods & Services (Scope 3)
        '5411' => 'purchased_goods', // Grocery Stores
        '5499' => 'purchased_goods', // Misc Food Stores
        '5812' => 'purchased_goods', // Eating Places, Restaurants
        '5814' => 'purchased_goods', // Fast Food Restaurants
        '5311' => 'purchased_goods', // Department Stores
        '5411' => 'purchased_goods', // Grocery Stores
        '5732' => 'purchased_goods', // Electronics Stores
        '5734' => 'purchased_goods', // Computer Software Stores
        '5735' => 'purchased_goods', // Record Shops
        '5943' => 'purchased_goods', // Stationery Stores
        '5944' => 'purchased_goods', // Jewelry Stores
        '5945' => 'purchased_goods', // Hobby/Toy/Game Shops
        '5946' => 'purchased_goods', // Camera & Photographic Supplies
        '5947' => 'purchased_goods', // Gift, Card, Novelty Stores
        '5999' => 'purchased_goods', // Miscellaneous Retail

        // IT & Cloud Services (Scope 3)
        '7372' => 'purchased_goods', // Computer Programming
        '7379' => 'purchased_goods', // Computer Related Services
        '4814' => 'purchased_goods', // Telecom Services
        '4816' => 'purchased_goods', // Computer Network Services

        // Professional Services (Scope 3)
        '8111' => 'purchased_goods', // Legal Services
        '8931' => 'purchased_goods', // Accounting Services
        '8999' => 'purchased_goods', // Professional Services

        // Shipping/Freight (Scope 3 - Upstream Transport)
        '4214' => 'upstream_transport', // Motor Freight Carriers
        '4215' => 'upstream_transport', // Courier Services
        '4225' => 'upstream_transport', // Warehousing

        // Waste (Scope 3)
        '4789' => 'waste', // Transportation Services

        // Excluded Categories
        '6010' => 'excluded', // Financial Institutions
        '6011' => 'excluded', // ATM
        '6012' => 'excluded', // Financial Institutions
        '6050' => 'excluded', // Quasi Cash
        '6051' => 'excluded', // Wire Transfer
        '9211' => 'excluded', // Courts
        '9222' => 'excluded', // Fines
        '9311' => 'excluded', // Tax Payments
        '9399' => 'excluded', // Government Services
        '9402' => 'excluded', // Postal Services
    ];

    /**
     * MCC code ranges for category groups.
     */
    private const MCC_RANGES = [
        // Airlines: 3000-3299
        ['start' => 3000, 'end' => 3299, 'category' => 'business_travel'],
        // Car Rental: 3351-3441
        ['start' => 3351, 'end' => 3441, 'category' => 'business_travel'],
        // Hotels: 3500-3799
        ['start' => 3500, 'end' => 3799, 'category' => 'business_travel'],
        // Airlines: 4511
        ['start' => 4511, 'end' => 4511, 'category' => 'business_travel'],
        // Fuel: 5541-5542
        ['start' => 5541, 'end' => 5542, 'category' => 'fuel'],
    ];

    /**
     * Lookup category by MCC code.
     */
    public function lookup(string $mccCode): ?Category
    {
        $mccCode = trim($mccCode);

        // Skip invalid codes
        if (! preg_match('/^\d{4}$/', $mccCode)) {
            return null;
        }

        $categoryCode = $this->getCategoryCode($mccCode);

        if (! $categoryCode) {
            return null;
        }

        return Cache::remember("category_by_code_{$categoryCode}", 3600, function () use ($categoryCode) {
            return Category::where('code', $categoryCode)->first();
        });
    }

    /**
     * Get category code for an MCC code.
     */
    public function getCategoryCode(string $mccCode): ?string
    {
        // First check direct mapping
        if (isset(self::MCC_MAPPING[$mccCode])) {
            return self::MCC_MAPPING[$mccCode];
        }

        // Check ranges
        $mccInt = (int) $mccCode;

        foreach (self::MCC_RANGES as $range) {
            if ($mccInt >= $range['start'] && $mccInt <= $range['end']) {
                return $range['category'];
            }
        }

        return null;
    }

    /**
     * Get MCC code description.
     */
    public function getDescription(string $mccCode): ?string
    {
        $descriptions = $this->getDescriptions();

        return $descriptions[$mccCode] ?? null;
    }

    /**
     * Get all MCC descriptions.
     */
    public function getDescriptions(): array
    {
        return Cache::remember('mcc_descriptions', 86400, function () {
            return [
                '3000' => 'United Airlines',
                '3001' => 'American Airlines',
                '3002' => 'Pan American',
                '3003' => 'Eurofly',
                '3004' => 'Dragon Air',
                '3005' => 'British Airways',
                '3006' => 'Japan Airlines',
                '3007' => 'Air France',
                '3008' => 'Lufthansa',
                '3009' => 'Air Canada',
                '3010' => 'KLM',
                '4011' => 'Railroads',
                '4112' => 'Passenger Railways',
                '4121' => 'Taxicabs/Limousines',
                '4131' => 'Bus Lines',
                '4511' => 'Airlines, Air Carriers',
                '4812' => 'Telecommunication Equipment',
                '4814' => 'Telecommunication Services',
                '4816' => 'Computer Network Services',
                '4900' => 'Utilities - Electric, Gas, Water',
                '5172' => 'Petroleum and Petroleum Products',
                '5311' => 'Department Stores',
                '5411' => 'Grocery Stores, Supermarkets',
                '5541' => 'Service Stations',
                '5542' => 'Fuel Dispensers',
                '5812' => 'Eating Places, Restaurants',
                '5814' => 'Fast Food Restaurants',
                '5943' => 'Stationery, Office Supplies',
                '6010' => 'Financial Institutions',
                '7011' => 'Hotels, Motels, Resorts',
                '7372' => 'Computer Programming',
                '7512' => 'Car Rental Agencies',
            ];
        });
    }

    /**
     * Get categories that match MCC codes.
     *
     * @return array<string, array<string>>
     */
    public function getMccByCategory(): array
    {
        $result = [];

        foreach (self::MCC_MAPPING as $mcc => $category) {
            if (! isset($result[$category])) {
                $result[$category] = [];
            }
            $result[$category][] = $mcc;
        }

        return $result;
    }

    /**
     * Suggest MCC code based on merchant name.
     */
    public function suggestMcc(string $merchantName): ?string
    {
        $patterns = [
            '/\b(AIR FRANCE|LUFTHANSA|EASYJET|RYANAIR|AIRLINE)\b/i' => '4511',
            '/\b(SNCF|DEUTSCHE BAHN|DB BAHN|TGV|ICE)\b/i' => '4112',
            '/\b(UBER|BOLT|TAXI|LYFT)\b/i' => '4121',
            '/\b(SHELL|TOTAL|ESSO|BP|ARAL)\b/i' => '5541',
            '/\b(EDF|ENGIE|VATTENFALL|RWE)\b/i' => '4900',
            '/\b(HOTEL|MARRIOTT|HILTON|ACCOR|IBIS)\b/i' => '7011',
            '/\b(HERTZ|AVIS|EUROPCAR|SIXT)\b/i' => '7512',
            '/\b(AWS|GOOGLE CLOUD|AZURE|GITHUB)\b/i' => '7372',
            '/\b(RESTAURANT|CAFE|BISTRO)\b/i' => '5812',
            '/\b(MCDONALDS|BURGER KING|KFC|SUBWAY)\b/i' => '5814',
        ];

        foreach ($patterns as $pattern => $mcc) {
            if (preg_match($pattern, $merchantName)) {
                return $mcc;
            }
        }

        return null;
    }
}

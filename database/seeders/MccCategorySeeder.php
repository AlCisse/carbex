<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MccCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seed emission categories with MCC code mappings.
     */
    public function run(): void
    {
        $categories = $this->getCategories();

        foreach ($categories as $categoryData) {
            $this->createCategory($categoryData);
        }

        $this->command->info('Created ' . count($categories) . ' emission categories with MCC mappings.');
    }

    /**
     * Create a category with optional children.
     */
    private function createCategory(array $data, ?string $parentId = null): void
    {
        $children = $data['children'] ?? [];
        unset($data['children']);

        $category = Category::create([
            'id' => Str::uuid(),
            'parent_id' => $parentId,
            ...$data,
        ]);

        foreach ($children as $childData) {
            $this->createCategory($childData, $category->id);
        }
    }

    /**
     * Get all categories data.
     */
    private function getCategories(): array
    {
        return [
            // Scope 1 - Direct Emissions
            [
                'code' => 'SCOPE1',
                'name' => 'Direct Emissions',
                'description' => 'Direct GHG emissions from owned or controlled sources',
                'scope' => 1,
                'ghg_category' => 'scope1',
                'icon' => 'fire',
                'color' => '#EF4444',
                'sort_order' => 1,
                'is_active' => true,
                'translations' => [
                    'fr' => ['name' => 'Emissions directes'],
                    'de' => ['name' => 'Direkte Emissionen'],
                ],
                'children' => [
                    [
                        'code' => 'fuel',
                        'name' => 'Fuel Combustion',
                        'description' => 'Emissions from burning fuel in owned vehicles and equipment',
                        'scope' => 1,
                        'ghg_category' => 'scope1',
                        'mcc_codes' => ['5541', '5542', '5983'], // Service stations, fuel
                        'keywords' => ['fuel', 'gas', 'diesel', 'petrol', 'carburant', 'essence', 'benzin'],
                        'default_unit' => 'liters',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Combustion de carburant'],
                            'de' => ['name' => 'Kraftstoffverbrennung'],
                        ],
                    ],
                    [
                        'code' => 'heating',
                        'name' => 'Facility Heating',
                        'description' => 'Emissions from natural gas and heating oil',
                        'scope' => 1,
                        'ghg_category' => 'scope1',
                        'mcc_codes' => ['4900'], // Utilities
                        'keywords' => ['gas', 'heating', 'chauffage', 'heizung', 'natural gas'],
                        'default_unit' => 'kWh',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Chauffage des locaux'],
                            'de' => ['name' => 'Gebaudeheizung'],
                        ],
                    ],
                ],
            ],

            // Scope 2 - Indirect Energy Emissions
            [
                'code' => 'SCOPE2',
                'name' => 'Indirect Energy Emissions',
                'description' => 'Indirect GHG emissions from purchased energy',
                'scope' => 2,
                'ghg_category' => 'scope2',
                'icon' => 'bolt',
                'color' => '#F59E0B',
                'sort_order' => 2,
                'is_active' => true,
                'translations' => [
                    'fr' => ['name' => 'Emissions indirectes liees a l\'energie'],
                    'de' => ['name' => 'Indirekte energiebezogene Emissionen'],
                ],
                'children' => [
                    [
                        'code' => 'electricity',
                        'name' => 'Purchased Electricity',
                        'description' => 'Emissions from purchased electricity',
                        'scope' => 2,
                        'ghg_category' => 'scope2',
                        'mcc_codes' => ['4900'], // Utilities
                        'keywords' => ['electricity', 'electric', 'electricite', 'strom', 'EDF', 'engie'],
                        'default_unit' => 'kWh',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Electricite achetee'],
                            'de' => ['name' => 'Eingekaufter Strom'],
                        ],
                    ],
                    [
                        'code' => 'district_heating',
                        'name' => 'District Heating/Cooling',
                        'description' => 'Emissions from purchased heating and cooling',
                        'scope' => 2,
                        'ghg_category' => 'scope2',
                        'keywords' => ['heat', 'cooling', 'district', 'reseau', 'fernwarme'],
                        'default_unit' => 'kWh',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Reseau de chaleur/froid'],
                            'de' => ['name' => 'Fernwarme/Fernkalte'],
                        ],
                    ],
                ],
            ],

            // Scope 3 - Other Indirect Emissions
            [
                'code' => 'SCOPE3',
                'name' => 'Other Indirect Emissions',
                'description' => 'All other indirect GHG emissions in value chain',
                'scope' => 3,
                'ghg_category' => 'scope3',
                'icon' => 'globe',
                'color' => '#3B82F6',
                'sort_order' => 3,
                'is_active' => true,
                'translations' => [
                    'fr' => ['name' => 'Autres emissions indirectes'],
                    'de' => ['name' => 'Sonstige indirekte Emissionen'],
                ],
                'children' => [
                    // Category 1: Purchased goods and services
                    [
                        'code' => 'purchased_goods',
                        'name' => 'Purchased Goods & Services',
                        'description' => 'Emissions from purchased goods and services',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 1,
                        'icon' => 'shopping-cart',
                        'default_unit' => 'EUR',
                        'calculation_method' => 'spend_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Achats de biens et services'],
                            'de' => ['name' => 'Eingekaufte Waren und Dienstleistungen'],
                        ],
                        'children' => [
                            [
                                'code' => 'SCOPE3_IT_EQUIPMENT',
                                'ghg_category' => 'purchased_goods',
                                'name' => 'IT Equipment',
                                'scope' => 3,
                                'scope_3_category' => 1,
                                'mcc_codes' => ['5045', '5732', '5734', '7372'],
                                'keywords' => ['computer', 'laptop', 'apple', 'dell', 'hp', 'lenovo', 'ordinateur'],
                                'default_unit' => 'EUR',
                                'calculation_method' => 'spend_based',
                                'is_active' => true,
                                'translations' => [
                                    'fr' => ['name' => 'Materiel informatique'],
                                    'de' => ['name' => 'IT-Ausrustung'],
                                ],
                            ],
                            [
                                'code' => 'SCOPE3_OFFICE_SUPPLIES',
                                'name' => 'Office Supplies',
                                'scope' => 3,
                                'scope_3_category' => 1,
                                'mcc_codes' => ['5943', '5111'],
                                'keywords' => ['office', 'staples', 'paper', 'bureau', 'papeterie', 'buro'],
                                'default_unit' => 'EUR',
                                'calculation_method' => 'spend_based',
                                'is_active' => true,
                                'translations' => [
                                    'fr' => ['name' => 'Fournitures de bureau'],
                                    'de' => ['name' => 'Burobedarf'],
                                ],
                            ],
                            [
                                'code' => 'SCOPE3_CLOUD_SERVICES',
                                'name' => 'Cloud & SaaS Services',
                                'scope' => 3,
                                'scope_3_category' => 1,
                                'mcc_codes' => ['7372', '7379', '4816'],
                                'keywords' => ['aws', 'azure', 'google cloud', 'saas', 'software', 'subscription'],
                                'default_unit' => 'EUR',
                                'calculation_method' => 'spend_based',
                                'is_active' => true,
                                'translations' => [
                                    'fr' => ['name' => 'Services Cloud et SaaS'],
                                    'de' => ['name' => 'Cloud- und SaaS-Dienste'],
                                ],
                            ],
                        ],
                    ],

                    // Category 2: Capital goods
                    [
                        'code' => 'capital_goods',
                        'name' => 'Capital Goods',
                        'description' => 'Emissions from capital goods purchases (buildings, equipment, vehicles)',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 2,
                        'icon' => 'building',
                        'default_unit' => 'EUR',
                        'calculation_method' => 'spend_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Biens d\'equipement'],
                            'de' => ['name' => 'Investitionsguter'],
                        ],
                    ],

                    // Category 3: Fuel and energy related activities
                    [
                        'code' => 'fuel_energy',
                        'name' => 'Fuel & Energy Activities',
                        'description' => 'Upstream emissions from fuel and electricity (WTT, T&D losses)',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 3,
                        'icon' => 'battery-charging',
                        'default_unit' => 'kWh',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Activites liees a l\'energie'],
                            'de' => ['name' => 'Brennstoff- und energiebezogene Aktivitaten'],
                        ],
                    ],

                    // Category 4: Upstream transportation and distribution
                    [
                        'code' => 'upstream_transport',
                        'name' => 'Upstream Transport & Distribution',
                        'description' => 'Emissions from inbound logistics and distribution',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 4,
                        'icon' => 'truck',
                        'default_unit' => 'tonne-km',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Transport et distribution amont'],
                            'de' => ['name' => 'Vorgelagerter Transport und Vertrieb'],
                        ],
                    ],

                    // Category 5: Waste
                    [
                        'code' => 'waste',
                        'name' => 'Waste Generated',
                        'description' => 'Emissions from waste disposal',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 5,
                        'mcc_codes' => ['4214'],
                        'keywords' => ['waste', 'disposal', 'recycling', 'dechets', 'abfall'],
                        'default_unit' => 'kg',
                        'calculation_method' => 'activity_based',
                        'icon' => 'trash',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Dechets generes'],
                            'de' => ['name' => 'Erzeugte Abfalle'],
                        ],
                    ],

                    // Category 6: Business Travel
                    [
                        'code' => 'business_travel',
                        'name' => 'Business Travel',
                        'description' => 'Emissions from employee business travel',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 6,
                        'icon' => 'airplane',
                        'default_unit' => 'passenger-km',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Deplacements professionnels'],
                            'de' => ['name' => 'Geschaftsreisen'],
                        ],
                        'children' => [
                            [
                                'code' => 'SCOPE3_FLIGHTS',
                                'name' => 'Air Travel',
                                'scope' => 3,
                                'scope_3_category' => 6,
                                'mcc_codes' => ['3000', '3001', '3002', '3003', '4511'],
                                'keywords' => ['airline', 'flight', 'air france', 'lufthansa', 'easyjet', 'vol', 'flug'],
                                'default_unit' => 'passenger-km',
                                'calculation_method' => 'activity_based',
                                'is_active' => true,
                                'translations' => [
                                    'fr' => ['name' => 'Transport aerien'],
                                    'de' => ['name' => 'Flugreisen'],
                                ],
                            ],
                            [
                                'code' => 'SCOPE3_TRAIN',
                                'name' => 'Rail Travel',
                                'scope' => 3,
                                'scope_3_category' => 6,
                                'mcc_codes' => ['4011', '4112'],
                                'keywords' => ['train', 'rail', 'sncf', 'deutsche bahn', 'tgv', 'ice', 'eurostar'],
                                'default_unit' => 'passenger-km',
                                'calculation_method' => 'activity_based',
                                'is_active' => true,
                                'translations' => [
                                    'fr' => ['name' => 'Transport ferroviaire'],
                                    'de' => ['name' => 'Bahnreisen'],
                                ],
                            ],
                            [
                                'code' => 'SCOPE3_RENTAL_CAR',
                                'name' => 'Rental Cars',
                                'scope' => 3,
                                'scope_3_category' => 6,
                                'mcc_codes' => ['7512', '7513', '7519'],
                                'keywords' => ['car rental', 'hertz', 'avis', 'europcar', 'sixt', 'location voiture', 'mietwagen'],
                                'default_unit' => 'km',
                                'calculation_method' => 'activity_based',
                                'is_active' => true,
                                'translations' => [
                                    'fr' => ['name' => 'Location de voiture'],
                                    'de' => ['name' => 'Mietwagen'],
                                ],
                            ],
                            [
                                'code' => 'SCOPE3_TAXI',
                                'name' => 'Taxi & Rideshare',
                                'scope' => 3,
                                'scope_3_category' => 6,
                                'mcc_codes' => ['4121'],
                                'keywords' => ['taxi', 'uber', 'bolt', 'kapten', 'vtc'],
                                'default_unit' => 'km',
                                'calculation_method' => 'activity_based',
                                'is_active' => true,
                                'translations' => [
                                    'fr' => ['name' => 'Taxi et VTC'],
                                    'de' => ['name' => 'Taxi und Rideshare'],
                                ],
                            ],
                            [
                                'code' => 'SCOPE3_HOTEL',
                                'name' => 'Hotel Stays',
                                'scope' => 3,
                                'scope_3_category' => 6,
                                'mcc_codes' => ['3501', '3502', '3503', '3504', '7011'],
                                'keywords' => ['hotel', 'accommodation', 'booking', 'marriott', 'hilton', 'accor', 'ibis'],
                                'default_unit' => 'nights',
                                'calculation_method' => 'activity_based',
                                'is_active' => true,
                                'translations' => [
                                    'fr' => ['name' => 'Nuitees d\'hotel'],
                                    'de' => ['name' => 'Hotelubernachtungen'],
                                ],
                            ],
                        ],
                    ],

                    // Category 7: Employee Commuting
                    [
                        'code' => 'employee_commuting',
                        'name' => 'Employee Commuting',
                        'description' => 'Emissions from employee commuting',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 7,
                        'keywords' => ['commute', 'commuting', 'trajet domicile', 'pendeln'],
                        'default_unit' => 'km',
                        'calculation_method' => 'activity_based',
                        'icon' => 'car',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Deplacements domicile-travail'],
                            'de' => ['name' => 'Pendeln der Mitarbeiter'],
                        ],
                    ],

                    // Category 8: Upstream leased assets
                    [
                        'code' => 'upstream_leased',
                        'name' => 'Upstream Leased Assets',
                        'description' => 'Emissions from leased assets (not in Scope 1/2)',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 8,
                        'icon' => 'key',
                        'default_unit' => 'm2',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Actifs loues amont'],
                            'de' => ['name' => 'Vorgelagerte gemietete Anlagen'],
                        ],
                    ],

                    // Category 9: Downstream transportation and distribution
                    [
                        'code' => 'downstream_transport',
                        'name' => 'Downstream Transport & Distribution',
                        'description' => 'Emissions from outbound logistics to customers',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 9,
                        'icon' => 'package',
                        'default_unit' => 'tonne-km',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Transport et distribution aval'],
                            'de' => ['name' => 'Nachgelagerter Transport und Vertrieb'],
                        ],
                    ],

                    // Category 10: Processing of sold products
                    [
                        'code' => 'processing',
                        'name' => 'Processing of Sold Products',
                        'description' => 'Emissions from processing of intermediate products by customers',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 10,
                        'icon' => 'cog',
                        'default_unit' => 'kg',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Transformation des produits vendus'],
                            'de' => ['name' => 'Verarbeitung verkaufter Produkte'],
                        ],
                    ],

                    // Category 11: Use of sold products
                    [
                        'code' => 'product_use',
                        'name' => 'Use of Sold Products',
                        'description' => 'Emissions from customer use of sold products',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 11,
                        'icon' => 'play',
                        'default_unit' => 'kWh',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Utilisation des produits vendus'],
                            'de' => ['name' => 'Nutzung verkaufter Produkte'],
                        ],
                    ],

                    // Category 12: End-of-life treatment of sold products
                    [
                        'code' => 'end_of_life',
                        'name' => 'End-of-Life Treatment',
                        'description' => 'Emissions from disposal/recycling of sold products',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 12,
                        'icon' => 'recycle',
                        'default_unit' => 'kg',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Fin de vie des produits vendus'],
                            'de' => ['name' => 'End-of-Life der verkauften Produkte'],
                        ],
                    ],

                    // Category 13: Downstream leased assets
                    [
                        'code' => 'downstream_leased',
                        'name' => 'Downstream Leased Assets',
                        'description' => 'Emissions from assets leased to others',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 13,
                        'icon' => 'home',
                        'default_unit' => 'm2',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Actifs loues aval'],
                            'de' => ['name' => 'Nachgelagerte vermietete Anlagen'],
                        ],
                    ],

                    // Category 14: Franchises
                    [
                        'code' => 'franchises',
                        'name' => 'Franchises',
                        'description' => 'Emissions from franchise operations',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 14,
                        'icon' => 'store',
                        'default_unit' => 'units',
                        'calculation_method' => 'activity_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Franchises'],
                            'de' => ['name' => 'Franchise-Betriebe'],
                        ],
                    ],

                    // Category 15: Investments
                    [
                        'code' => 'investments',
                        'name' => 'Investments',
                        'description' => 'Emissions from equity investments and financing',
                        'scope' => 3,
                        'ghg_category' => 'scope3',
                        'scope_3_category' => 15,
                        'icon' => 'trending-up',
                        'default_unit' => 'EUR',
                        'calculation_method' => 'spend_based',
                        'is_active' => true,
                        'translations' => [
                            'fr' => ['name' => 'Investissements'],
                            'de' => ['name' => 'Investitionen'],
                        ],
                    ],
                ],
            ],

            // Uncategorized/Excluded
            [
                'code' => 'EXCLUDED',
                'name' => 'Excluded / Non-Emission',
                'description' => 'Transactions excluded from carbon calculations',
                'scope' => 0,
                'ghg_category' => 'excluded',
                'mcc_codes' => ['6010', '6011', '6012', '6051', '6211', '6300', '9399', '9402'],
                'keywords' => ['transfer', 'tax', 'salary', 'insurance', 'virement', 'impot', 'salaire'],
                'icon' => 'ban',
                'color' => '#9CA3AF',
                'sort_order' => 99,
                'is_active' => true,
                'translations' => [
                    'fr' => ['name' => 'Exclus / Sans emission'],
                    'de' => ['name' => 'Ausgeschlossen / Keine Emission'],
                ],
            ],
        ];
    }
}

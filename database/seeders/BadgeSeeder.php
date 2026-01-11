<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

/**
 * BadgeSeeder
 *
 * Seed les badges de gamification prédéfinis.
 *
 * Constitution Carbex v3.0 - Section 9.9 (Gamification)
 */
class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // ========================================
            // Catégorie: Assessment
            // ========================================
            [
                'code' => Badge::FIRST_ASSESSMENT,
                'name' => 'Premier Bilan',
                'name_en' => 'First Assessment',
                'name_de' => 'Erste Bilanz',
                'description' => 'Félicitations ! Vous avez réalisé votre premier bilan carbone.',
                'description_en' => 'Congratulations! You have completed your first carbon assessment.',
                'description_de' => 'Herzlichen Glückwunsch! Sie haben Ihre erste CO2-Bilanz erstellt.',
                'icon' => 'trophy',
                'color' => 'emerald',
                'category' => Badge::CATEGORY_ASSESSMENT,
                'criteria' => [
                    'type' => 'assessment_count',
                    'value' => 1,
                ],
                'points' => 25,
                'sort_order' => 1,
            ],
            [
                'code' => Badge::FIVE_ASSESSMENTS,
                'name' => 'Expert Bilan',
                'name_en' => 'Assessment Expert',
                'name_de' => 'Bilanz-Experte',
                'description' => 'Vous avez réalisé 5 bilans carbone annuels. Votre engagement est exemplaire !',
                'description_en' => 'You have completed 5 annual carbon assessments. Your commitment is exemplary!',
                'description_de' => 'Sie haben 5 jährliche CO2-Bilanzen erstellt. Ihr Engagement ist vorbildlich!',
                'icon' => 'academic-cap',
                'color' => 'purple',
                'category' => Badge::CATEGORY_ASSESSMENT,
                'criteria' => [
                    'type' => 'assessment_count',
                    'value' => 5,
                ],
                'points' => 100,
                'sort_order' => 2,
            ],

            // ========================================
            // Catégorie: Réduction
            // ========================================
            [
                'code' => Badge::CARBON_REDUCER_10,
                'name' => 'Réducteur -10%',
                'name_en' => 'Carbon Reducer -10%',
                'name_de' => 'CO2-Reduzierer -10%',
                'description' => 'Vous avez réduit vos émissions de 10% par rapport à votre bilan de référence.',
                'description_en' => 'You have reduced your emissions by 10% compared to your baseline.',
                'description_de' => 'Sie haben Ihre Emissionen um 10% gegenüber Ihrer Basisbilanz reduziert.',
                'icon' => 'arrow-trending-down',
                'color' => 'blue',
                'category' => Badge::CATEGORY_REDUCTION,
                'criteria' => [
                    'type' => 'emission_reduction',
                    'value' => 10,
                ],
                'points' => 50,
                'sort_order' => 3,
            ],
            [
                'code' => Badge::CARBON_REDUCER_25,
                'name' => 'Réducteur -25%',
                'name_en' => 'Carbon Reducer -25%',
                'name_de' => 'CO2-Reduzierer -25%',
                'description' => 'Excellent ! Vous avez réduit vos émissions de 25%. Continuez ainsi !',
                'description_en' => 'Excellent! You have reduced your emissions by 25%. Keep it up!',
                'description_de' => 'Ausgezeichnet! Sie haben Ihre Emissionen um 25% reduziert. Weiter so!',
                'icon' => 'arrow-trending-down',
                'color' => 'emerald',
                'category' => Badge::CATEGORY_REDUCTION,
                'criteria' => [
                    'type' => 'emission_reduction',
                    'value' => 25,
                ],
                'points' => 150,
                'sort_order' => 4,
            ],

            // ========================================
            // Catégorie: Engagement
            // ========================================
            [
                'code' => Badge::SCOPE3_CHAMPION,
                'name' => 'Champion Scope 3',
                'name_en' => 'Scope 3 Champion',
                'name_de' => 'Scope 3 Champion',
                'description' => 'Vous avez documenté plus de 80% de vos émissions Scope 3. Bravo pour cette transparence !',
                'description_en' => 'You have documented more than 80% of your Scope 3 emissions. Congratulations on this transparency!',
                'description_de' => 'Sie haben mehr als 80% Ihrer Scope-3-Emissionen dokumentiert. Herzlichen Glückwunsch zu dieser Transparenz!',
                'icon' => 'globe-alt',
                'color' => 'blue',
                'category' => Badge::CATEGORY_ENGAGEMENT,
                'criteria' => [
                    'type' => 'scope3_coverage',
                    'value' => 80,
                ],
                'points' => 75,
                'sort_order' => 5,
            ],
            [
                'code' => Badge::SUPPLIER_ENGAGED,
                'name' => 'Chaîne Verte',
                'name_en' => 'Green Supply Chain',
                'name_de' => 'Grüne Lieferkette',
                'description' => 'Vous avez engagé au moins 5 fournisseurs dans votre démarche carbone.',
                'description_en' => 'You have engaged at least 5 suppliers in your carbon journey.',
                'description_de' => 'Sie haben mindestens 5 Lieferanten in Ihre CO2-Strategie eingebunden.',
                'icon' => 'building-office-2',
                'color' => 'emerald',
                'category' => Badge::CATEGORY_ENGAGEMENT,
                'criteria' => [
                    'type' => 'supplier_count',
                    'value' => 5,
                ],
                'points' => 50,
                'sort_order' => 6,
            ],

            // ========================================
            // Catégorie: Expert
            // ========================================
            [
                'code' => Badge::DATA_QUALITY,
                'name' => 'Données Premium',
                'name_en' => 'Premium Data',
                'name_de' => 'Premium-Daten',
                'description' => 'Plus de 80% de vos données sont de qualité primaire. Excellence !',
                'description_en' => 'More than 80% of your data is primary quality. Excellence!',
                'description_de' => 'Mehr als 80% Ihrer Daten sind Primärdaten. Exzellent!',
                'icon' => 'chart-bar',
                'color' => 'yellow',
                'category' => Badge::CATEGORY_EXPERT,
                'criteria' => [
                    'type' => 'data_quality',
                    'value' => 80,
                ],
                'points' => 100,
                'sort_order' => 7,
            ],
            [
                'code' => Badge::SBTI_ALIGNED,
                'name' => 'Aligné SBTi',
                'name_en' => 'SBTi Aligned',
                'name_de' => 'SBTi-konform',
                'description' => 'Vos objectifs de réduction sont alignés avec la Science Based Targets initiative.',
                'description_en' => 'Your reduction targets are aligned with the Science Based Targets initiative.',
                'description_de' => 'Ihre Reduktionsziele sind auf die Science Based Targets Initiative ausgerichtet.',
                'icon' => 'beaker',
                'color' => 'purple',
                'category' => Badge::CATEGORY_EXPERT,
                'criteria' => [
                    'type' => 'sbti_aligned',
                    'value' => true,
                ],
                'points' => 200,
                'sort_order' => 8,
            ],
        ];

        foreach ($badges as $badgeData) {
            Badge::updateOrCreate(
                ['code' => $badgeData['code']],
                $badgeData
            );
        }

        $this->command->info('Seeded '.count($badges).' badges.');
    }
}

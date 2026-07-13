<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder for Action (Plan de transition) model
 *
 * Creates realistic reduction actions for testing
 */
class ActionSeeder extends Seeder
{
    /**
     * Predefined actions based on common carbon reduction strategies.
     */
    private array $actionTemplates = [
        // Scope 1 - Direct emissions
        [
            'title' => 'Diesel-Flotte durch Elektrofahrzeuge ersetzen',
            'description' => 'Schrittweise Umstellung des Fuhrparks auf 100% Elektrofahrzeuge. Beginn mit Stadtfahrzeugen, danach Ausweitung auf Langstreckenfahrzeuge.',
            'co2_reduction_percent' => 15.0,
            'estimated_cost' => 45000.00,
            'difficulty' => Action::DIFFICULTY_HARD,
            'priority' => 1,
        ],
        [
            'title' => 'Gebäudeheizung optimieren',
            'description' => 'Installation intelligenter Thermostate und Verbesserung der Wärmedämmung der Büros.',
            'co2_reduction_percent' => 8.0,
            'estimated_cost' => 12000.00,
            'difficulty' => Action::DIFFICULTY_MEDIUM,
            'priority' => 2,
        ],
        [
            'title' => 'Kältemittel-Leckagen reduzieren',
            'description' => 'Audit und vorbeugende Wartung der Klimaanlagen zur Begrenzung flüchtiger Emissionen.',
            'co2_reduction_percent' => 3.0,
            'estimated_cost' => 5000.00,
            'difficulty' => Action::DIFFICULTY_EASY,
            'priority' => 4,
        ],

        // Scope 2 - Indirect energy
        [
            'title' => 'Ökostrom-Vertrag abschließen',
            'description' => 'Wechsel zu einem zertifizierten Ökostrom-Anbieter mit Herkunftsnachweisen.',
            'co2_reduction_percent' => 20.0,
            'estimated_cost' => 2000.00,
            'difficulty' => Action::DIFFICULTY_EASY,
            'priority' => 1,
        ],
        [
            'title' => 'Solaranlage installieren',
            'description' => 'Installation von Photovoltaik-Modulen auf dem Dach des Hauptgebäudes zur Eigenversorgung.',
            'co2_reduction_percent' => 12.0,
            'estimated_cost' => 35000.00,
            'difficulty' => Action::DIFFICULTY_HARD,
            'priority' => 3,
        ],

        // Scope 3 - Other indirect
        [
            'title' => 'Homeoffice an 2 Tagen/Woche fördern',
            'description' => 'Einführung einer Homeoffice-Richtlinie zur Reduzierung des Pendlerverkehrs.',
            'co2_reduction_percent' => 10.0,
            'estimated_cost' => 500.00,
            'difficulty' => Action::DIFFICULTY_EASY,
            'priority' => 1,
        ],
        [
            'title' => 'Bahn für Geschäftsreisen bevorzugen',
            'description' => 'Reiserichtlinie mit Bahnpflicht für Strecken < 4h und CO2-Kompensation für Flüge.',
            'co2_reduction_percent' => 6.0,
            'estimated_cost' => 3000.00,
            'difficulty' => Action::DIFFICULTY_MEDIUM,
            'priority' => 2,
        ],
        [
            'title' => 'Nachhaltige Beschaffung optimieren',
            'description' => 'Umweltkriterien in die Lieferantenauswahl integrieren und lokale Beschaffung bevorzugen.',
            'co2_reduction_percent' => 5.0,
            'estimated_cost' => 1000.00,
            'difficulty' => Action::DIFFICULTY_MEDIUM,
            'priority' => 3,
        ],
        [
            'title' => 'Abfälle reduzieren und verwerten',
            'description' => 'Einführung von Mülltrennung, Kompostierung und Partnerschaften mit Recycling-Betrieben.',
            'co2_reduction_percent' => 2.0,
            'estimated_cost' => 2500.00,
            'difficulty' => Action::DIFFICULTY_EASY,
            'priority' => 5,
        ],
        [
            'title' => 'Papierprozesse digitalisieren',
            'description' => 'Reduzierung des Papierverbrauchs um 80% durch Digitalisierung der Dokumente.',
            'co2_reduction_percent' => 1.5,
            'estimated_cost' => 8000.00,
            'difficulty' => Action::DIFFICULTY_MEDIUM,
            'priority' => 6,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = Organization::all();

        if ($organizations->isEmpty()) {
            $this->command->warn('No organizations found. Run AssessmentSeeder first.');

            return;
        }

        foreach ($organizations as $organization) {
            $this->createActionsForOrganization($organization);
        }

        $this->command->info('Actions seeded successfully!');
    }

    /**
     * Create actions for a specific organization.
     */
    private function createActionsForOrganization(Organization $organization): void
    {
        // Get first user for assignment (if exists)
        $user = User::where('organization_id', $organization->id)->first();

        $statusDistribution = [
            Action::STATUS_COMPLETED,
            Action::STATUS_COMPLETED,
            Action::STATUS_IN_PROGRESS,
            Action::STATUS_IN_PROGRESS,
            Action::STATUS_IN_PROGRESS,
            Action::STATUS_TODO,
            Action::STATUS_TODO,
            Action::STATUS_TODO,
            Action::STATUS_TODO,
            Action::STATUS_TODO,
        ];

        foreach ($this->actionTemplates as $index => $template) {
            $status = $statusDistribution[$index] ?? Action::STATUS_TODO;

            // Set due dates based on status
            $dueDate = match ($status) {
                Action::STATUS_COMPLETED => now()->subMonths(rand(1, 6)),
                Action::STATUS_IN_PROGRESS => now()->addMonths(rand(1, 3)),
                Action::STATUS_TODO => now()->addMonths(rand(3, 12)),
            };

            $this->createOrUpdateAction($organization->id, $template['title'], [
                'description' => $template['description'],
                'status' => $status,
                'due_date' => $dueDate,
                'co2_reduction_percent' => $template['co2_reduction_percent'],
                'estimated_cost' => $template['estimated_cost'],
                'difficulty' => $template['difficulty'],
                'priority' => $template['priority'],
                'assigned_to' => $user?->id,
                'metadata' => [
                    'created_by_seeder' => true,
                    'scope' => $this->inferScope($template['title']),
                ],
            ]);
        }

        $count = count($this->actionTemplates);
        $this->command->info("  Created {$count} actions for: {$organization->name}");
    }

    /**
     * Create or update an action with proper UUID handling.
     */
    private function createOrUpdateAction(string $organizationId, string $title, array $data): Action
    {
        $action = Action::where('organization_id', $organizationId)
            ->where('title', $title)
            ->first();

        if ($action) {
            $action->update($data);

            return $action;
        }

        $action = new Action(array_merge($data, [
            'organization_id' => $organizationId,
            'title' => $title,
        ]));
        $action->id = Str::uuid()->toString();
        $action->save();

        return $action;
    }

    /**
     * Infer scope from action title.
     */
    private function inferScope(string $title): int
    {
        $scope1Keywords = ['flotte', 'chauffage', 'fluides', 'combustion'];
        $scope2Keywords = ['électricité', 'solaire', 'énergie'];

        $titleLower = mb_strtolower($title);

        foreach ($scope1Keywords as $keyword) {
            if (str_contains($titleLower, $keyword)) {
                return 1;
            }
        }

        foreach ($scope2Keywords as $keyword) {
            if (str_contains($titleLower, $keyword)) {
                return 2;
            }
        }

        return 3;
    }
}

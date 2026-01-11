<?php

namespace App\Listeners;

use App\Events\EmissionCalculated;
use App\Models\Organization;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class UpdateOrganizationEmissionTotals implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'emissions';

    /**
     * Handle the event.
     */
    public function handle(EmissionCalculated $event): void
    {
        $organization = $event->organization;
        $emission = $event->emission;
        $year = $emission->date->year;

        // Update organization's emission totals for the year
        $this->updateYearlyTotals($organization, $year);
    }

    /**
     * Update yearly emission totals for the organization.
     */
    protected function updateYearlyTotals(Organization $organization, int $year): void
    {
        // Calculate totals by scope
        $totals = DB::table('emissions')
            ->where('organization_id', $organization->id)
            ->whereYear('date', $year)
            ->selectRaw('
                scope,
                SUM(co2_kg) as total_co2_kg,
                COUNT(*) as emission_count
            ')
            ->groupBy('scope')
            ->get()
            ->keyBy('scope');

        // Store in organization metadata or separate table
        $yearlyData = [
            'year' => $year,
            'scope_1' => [
                'total_kg' => $totals->get(1)?->total_co2_kg ?? 0,
                'count' => $totals->get(1)?->emission_count ?? 0,
            ],
            'scope_2' => [
                'total_kg' => $totals->get(2)?->total_co2_kg ?? 0,
                'count' => $totals->get(2)?->emission_count ?? 0,
            ],
            'scope_3' => [
                'total_kg' => $totals->get(3)?->total_co2_kg ?? 0,
                'count' => $totals->get(3)?->emission_count ?? 0,
            ],
            'total_kg' => $totals->sum('total_co2_kg'),
            'total_tonnes' => $totals->sum('total_co2_kg') / 1000,
            'updated_at' => now()->toISOString(),
        ];

        // Update organization settings with yearly totals
        $settings = $organization->settings ?? [];
        $settings['emission_totals'] = $settings['emission_totals'] ?? [];
        $settings['emission_totals'][$year] = $yearlyData;

        $organization->update(['settings' => $settings]);
    }
}

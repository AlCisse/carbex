<?php

namespace App\Filament\Widgets;

use App\Models\EmissionRecord;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EmissionsByCountryChart extends ChartWidget
{
    protected static ?string $heading = 'Emissions by Country';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = EmissionRecord::query()
            ->join('organizations', 'emission_records.organization_id', '=', 'organizations.id')
            ->select('organizations.country', DB::raw('SUM(emission_records.co2e_kg) / 1000 as total_tonnes'))
            ->whereYear('emission_records.date', now()->year)
            ->groupBy('organizations.country')
            ->orderByDesc('total_tonnes')
            ->limit(8)
            ->get();

        $countryNames = [
            'FR' => 'France',
            'DE' => 'Germany',
            'BE' => 'Belgium',
            'NL' => 'Netherlands',
            'AT' => 'Austria',
            'CH' => 'Switzerland',
            'ES' => 'Spain',
            'IT' => 'Italy',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Emissions (t CO₂e)',
                    'data' => $data->pluck('total_tonnes')->map(fn ($v) => round($v, 2))->toArray(),
                    'backgroundColor' => [
                        '#10b981', '#3b82f6', '#8b5cf6', '#f59e0b',
                        '#ef4444', '#06b6d4', '#ec4899', '#6366f1',
                    ],
                ],
            ],
            'labels' => $data->pluck('country')->map(fn ($c) => $countryNames[$c] ?? $c)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

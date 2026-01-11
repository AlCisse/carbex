<?php

namespace App\Filament\Widgets;

use App\Models\Emission;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyEmissionsChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Emissions Trend';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Emission::query()
            ->select(
                DB::raw("DATE_TRUNC('month', date) as month"),
                DB::raw('SUM(CASE WHEN scope = 1 THEN co2_kg ELSE 0 END) / 1000 as scope_1'),
                DB::raw('SUM(CASE WHEN scope = 2 THEN co2_kg ELSE 0 END) / 1000 as scope_2'),
                DB::raw('SUM(CASE WHEN scope = 3 THEN co2_kg ELSE 0 END) / 1000 as scope_3')
            )
            ->where('date', '>=', now()->subMonths(12))
            ->groupBy(DB::raw("DATE_TRUNC('month', date)"))
            ->orderBy('month')
            ->get();

        $labels = $data->pluck('month')->map(fn ($d) => \Carbon\Carbon::parse($d)->format('M Y'))->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Scope 1',
                    'data' => $data->pluck('scope_1')->map(fn ($v) => round($v, 2))->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Scope 2',
                    'data' => $data->pluck('scope_2')->map(fn ($v) => round($v, 2))->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Scope 3',
                    'data' => $data->pluck('scope_3')->map(fn ($v) => round($v, 2))->toArray(),
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

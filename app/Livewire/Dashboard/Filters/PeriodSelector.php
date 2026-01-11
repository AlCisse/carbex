<?php

namespace App\Livewire\Dashboard\Filters;

use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Period Selector Component
 *
 * Date range picker for dashboard:
 * - Preset periods (YTD, Q1-Q4, Last 12 months, etc.)
 * - Custom date range picker
 * - Emits filter event on change
 */
class PeriodSelector extends Component
{
    public string $preset = 'ytd';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public bool $showCustom = false;

    public function mount(
        ?string $startDate = null,
        ?string $endDate = null
    ): void {
        if ($startDate && $endDate) {
            $this->startDate = $startDate;
            $this->endDate = $endDate;
            $this->preset = 'custom';
            $this->showCustom = true;
        } else {
            $this->applyPreset('ytd');
        }
    }

    #[Computed]
    public function presets(): array
    {
        $now = Carbon::now();
        $year = $now->year;

        return [
            'ytd' => [
                'label' => __('Year to date'),
                'start' => Carbon::create($year, 1, 1)->toDateString(),
                'end' => $now->toDateString(),
            ],
            'last_month' => [
                'label' => __('Last month'),
                'start' => $now->copy()->subMonth()->startOfMonth()->toDateString(),
                'end' => $now->copy()->subMonth()->endOfMonth()->toDateString(),
            ],
            'last_quarter' => [
                'label' => __('Last quarter'),
                'start' => $now->copy()->subQuarter()->startOfQuarter()->toDateString(),
                'end' => $now->copy()->subQuarter()->endOfQuarter()->toDateString(),
            ],
            'q1' => [
                'label' => 'Q1 ' . $year,
                'start' => Carbon::create($year, 1, 1)->toDateString(),
                'end' => Carbon::create($year, 3, 31)->toDateString(),
            ],
            'q2' => [
                'label' => 'Q2 ' . $year,
                'start' => Carbon::create($year, 4, 1)->toDateString(),
                'end' => Carbon::create($year, 6, 30)->toDateString(),
            ],
            'q3' => [
                'label' => 'Q3 ' . $year,
                'start' => Carbon::create($year, 7, 1)->toDateString(),
                'end' => Carbon::create($year, 9, 30)->toDateString(),
            ],
            'q4' => [
                'label' => 'Q4 ' . $year,
                'start' => Carbon::create($year, 10, 1)->toDateString(),
                'end' => Carbon::create($year, 12, 31)->toDateString(),
            ],
            'last_year' => [
                'label' => __('Last year'),
                'start' => Carbon::create($year - 1, 1, 1)->toDateString(),
                'end' => Carbon::create($year - 1, 12, 31)->toDateString(),
            ],
            'last_12_months' => [
                'label' => __('Last 12 months'),
                'start' => $now->copy()->subYear()->toDateString(),
                'end' => $now->toDateString(),
            ],
            'custom' => [
                'label' => __('Custom range'),
                'start' => null,
                'end' => null,
            ],
        ];
    }

    #[Computed]
    public function currentLabel(): string
    {
        if ($this->preset === 'custom' && $this->startDate && $this->endDate) {
            return Carbon::parse($this->startDate)->format('M j, Y') . ' - ' . Carbon::parse($this->endDate)->format('M j, Y');
        }

        return $this->presets[$this->preset]['label'] ?? '';
    }

    public function applyPreset(string $preset): void
    {
        if ($preset === 'custom') {
            $this->preset = 'custom';
            $this->showCustom = true;

            return;
        }

        $this->preset = $preset;
        $this->showCustom = false;

        if (isset($this->presets[$preset])) {
            $this->startDate = $this->presets[$preset]['start'];
            $this->endDate = $this->presets[$preset]['end'];
            $this->emitChange();
        }
    }

    public function applyCustomDates(): void
    {
        if ($this->startDate && $this->endDate) {
            $this->preset = 'custom';
            $this->emitChange();
        }
    }

    private function emitChange(): void
    {
        $this->dispatch('period-changed',
            startDate: $this->startDate,
            endDate: $this->endDate
        );
    }

    public function render()
    {
        return view('livewire.dashboard.filters.period-selector');
    }
}

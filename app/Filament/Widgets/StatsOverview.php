<?php

namespace App\Filament\Widgets;

use App\Models\EmissionRecord;
use App\Models\Organization;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Organizations stats
        $totalOrgs = Organization::count();
        $newOrgsThisMonth = Organization::where('created_at', '>=', $currentMonth)->count();
        $newOrgsLastMonth = Organization::whereBetween('created_at', [$lastMonth, $currentMonth])->count();
        $orgGrowth = $newOrgsLastMonth > 0
            ? round((($newOrgsThisMonth - $newOrgsLastMonth) / $newOrgsLastMonth) * 100, 1)
            : 0;

        // Users stats
        $totalUsers = User::count();
        $activeUsers = User::where('last_login_at', '>=', now()->subDays(30))->count();

        // Transactions stats
        $totalTransactions = Transaction::count();
        $pendingReview = Transaction::where('confidence', '<', 0.7)
            ->where('validated', false)
            ->count();

        // Emissions stats
        $totalEmissions = EmissionRecord::sum('co2e_kg') / 1000; // Convert to tonnes
        $thisYearEmissions = EmissionRecord::whereYear('date', now()->year)->sum('co2e_kg') / 1000;

        return [
            Stat::make('Total Organizations', number_format($totalOrgs))
                ->description($newOrgsThisMonth . ' new this month')
                ->descriptionIcon($orgGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($orgGrowth >= 0 ? 'success' : 'danger')
                ->chart([7, 3, 4, 5, 6, 3, $newOrgsThisMonth]),

            Stat::make('Total Users', number_format($totalUsers))
                ->description($activeUsers . ' active (30 days)')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Transactions', number_format($totalTransactions))
                ->description($pendingReview . ' pending review')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($pendingReview > 100 ? 'warning' : 'success'),

            Stat::make('Total Emissions', number_format($totalEmissions, 1) . ' t CO₂e')
                ->description(number_format($thisYearEmissions, 1) . ' t this year')
                ->descriptionIcon('heroicon-m-cloud')
                ->color('gray'),
        ];
    }
}

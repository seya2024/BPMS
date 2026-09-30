<?php

namespace App\Filament\Widgets;

use App\Helpers\FinancialHelper;
use App\Models\Branch;
use App\Models\District;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PerformanceOverviewWidget extends BaseWidget
{
    protected ?string $heading = 'Performance Overview';

    protected function getStats(): array
    {
        $totalBranches = Branch::count();
        $totalDistricts = District::count();
        $currentFY = FinancialHelper::currentFY();
        $currentQuarter = FinancialHelper::currentQuarter();

        $fyName = $currentFY ? $currentFY->name : 'N/A';
        $quarterLabel = $currentQuarter ? "Q{$currentQuarter->quarter}" : 'N/A';

        // Calculate quarter progress
        $quarterProgress = null;
        if ($currentQuarter) {
            $start = $currentQuarter->start_date;
            $end = $currentQuarter->end_date;
            $now = now();
            $totalDays = $start->diffInDays($end) + 1;
            $elapsedDays = $start->diffInDays($now) + 1;
            $quarterProgress = $now->isAfter($end)
                ? 100
                : min(100, max(0, round(($elapsedDays / $totalDays) * 100)));
        }

        return [
            Stat::make('Total Branches', $totalBranches)
                ->description('Active branches in the system')
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make('Total Districts', $totalDistricts)
                ->description('Administrative districts')
                ->icon('heroicon-o-map')
                ->color('info'),

            Stat::make('Current Financial Year', $fyName)
                ->description($currentFY ? "{$currentFY->start_date->format('Y')} - {$currentFY->end_date->format('Y')}" : 'No active financial year')
                ->icon('heroicon-o-calendar-days')
                ->color('success'),

            Stat::make('Current Quarter', $quarterLabel)
                ->description($quarterProgress !== null ? "{$quarterProgress}% elapsed" : 'No active quarter')
                ->icon('heroicon-o-chart-pie')
                ->color('warning'),
        ];
    }
}

<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AccountAnalyticsWidget;
use App\Filament\Widgets\AccountPerformanceChart;
use App\Filament\Widgets\AccountTrendChart;
use App\Filament\Widgets\BankingTypeSplitChart;
use App\Filament\Widgets\BranchAttainmentChart;
use App\Filament\Widgets\DepositAnalyticsWidget;
use App\Filament\Widgets\DepositTrendChart;
use App\Filament\Widgets\DistrictPerformanceChart;
use App\Filament\Widgets\KpiScorecardWidget;
use App\Filament\Widgets\NetChangeChart;
use App\Filament\Widgets\PlanAchievementWidget;
use App\Filament\Widgets\SegmentMixChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /**
     * Four columns at lg.
     *
     * Stats cards use columnSpan 1 (four per row) and charts use columnSpan 2
     * (two per row). Mixing the two spans in one grid is what keeps every row
     * completely filled with no empty slots.
     *
     * @return array<string, int>
     */
    public function getColumns(): int | array
    {
        return [
            'lg' => 4,
            'md' => 2,
            'sm' => 1,
        ];
    }

    /**
     * Note: Filament only reads getWidgets(). An earlier getFooterWidgets()
     * method looked plausible but was never called, so those widgets silently
     * never rendered.
     *
     * Totals add up to a whole number of rows: 4 stat cards (1 row) plus
     * 8 charts at double width (4 rows) = 5 full rows.
     *
     * DepositPerformanceChart is omitted because DepositTrendChart covers the
     * same ground with a moving average and a real daily-target line, and
     * PerformanceOverviewWidget duplicates static sidebar information.
     *
     * @return array<int, class-string>
     */
    public function getWidgets(): array
    {
        return [
            // Row 1 - four compact headline cards
            DepositAnalyticsWidget::class,
            PlanAchievementWidget::class,
            AccountAnalyticsWidget::class,
            KpiScorecardWidget::class,

            // Rows 2-5 - two charts per row
            SegmentMixChart::class,
            BankingTypeSplitChart::class,
            DistrictPerformanceChart::class,
            BranchAttainmentChart::class,
            DepositTrendChart::class,
            NetChangeChart::class,
            AccountTrendChart::class,
            AccountPerformanceChart::class,

            ];
    }
}

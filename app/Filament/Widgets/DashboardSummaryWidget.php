<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use App\Helpers\FinancialHelper;
use App\Models\DailyDepositPerformance;
use Illuminate\Support\Carbon;

/**
 * Full-width summary band shown as the last row of the dashboard.
 *
 * Includes the copyright notice to avoid duplicate rendering from the panel
 * render hook.
 */
class DashboardSummaryWidget extends \Filament\Widgets\Widget
{
    use HasDistrictFilter;

    /**
     * The district filter state. Base Widget doesn't declare this,
     * so we must declare it ourselves since we use HasDistrictFilter.
     */
    public ?string $filter = '';

    protected string $view = 'widgets.dashboard-summary';

    protected static ?int $sort = 99;

    /**
     * Deliberately full width: this is a closing summary band, so it takes a
     * whole row and must stay last in the grid.
     */
    protected int | string | array $columnSpan = 4;

    public function getSummary(): array
    {
        $branchIds = $this->branchIdsForFilter();

        $windowStart = \App\Filament\Widgets\AnalyticsService::windowStart();
        $totals = \App\Filament\Widgets\AnalyticsService::depositTotals(branchIds: $branchIds);
        $accounts = \App\Filament\Widgets\AnalyticsService::accountSnapshot($branchIds);
        $fy = FinancialHelper::currentFY();
        $quarter = FinancialHelper::currentQuarter();

        $reportedDays = DailyDepositPerformance::query()
            ->whereDate('business_day', '>=', $windowStart->toDateString())
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->distinct()
            ->count('business_day');

        return [
            'windowStart' => $windowStart,
            'windowEnd' => Carbon::yesterday(),
            'reportedDays' => $reportedDays,
            'windowDays' => $windowStart->diffInDays(Carbon::yesterday()) + 1,
            'totals' => $totals,
            'accounts' => $accounts,
            'fy' => $fy?->name,
            'quarter' => $quarter ? "Q{$quarter->quarter} - {$quarter->label}" : null,
        ];
    }
}
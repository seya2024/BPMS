<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class DepositAnalyticsWidget extends BaseWidget
{
    use HasDistrictFilter;

    protected ?string $heading = 'Deposit Analytics (Last 45 Days)';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 1;

    /** Compact 2-up layout so the widget fits a third of the dashboard row. */
    protected int | array | null $columns = 1;

    protected string $view = 'widgets.analytics-stats';

    /** StatsOverviewWidget does not declare $filter, so the trait's state lives here. */
    public ?string $filter = '';

    protected function getStats(): array
    {
        $branchIds = $this->branchIdsForFilter();

        $totals = AnalyticsService::depositTotals(branchIds: $branchIds);
        $dod = AnalyticsService::dayOnDayChange($branchIds);
        $accounts = AnalyticsService::accountSnapshot($branchIds);

        if ($totals['days'] === 0) {
            return [
                Stat::make('Deposit Performance', 'No data')
                    ->description('No deposit performance recorded for this selection')
                    ->icon('heroicon-o-chart-bar')
                    ->color('gray'),
            ];
        }

        $avgDaily = $totals['total'] / max(1, $totals['days']);

        return [
            Stat::make('Total Deposits', Number::format($totals['total'], precision: 2))
                ->description("{$totals['days']} days | avg " . Number::format($avgDaily, precision: 0) . '/day')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->icon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make('Net Change', Number::format($totals['net'], precision: 2))
                ->description($totals['net'] >= 0 ? 'Net growth' : 'Net decline')
                ->descriptionIcon($totals['net'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->icon('heroicon-o-arrow-trending-up')
                ->color($totals['net'] >= 0 ? 'success' : 'danger'),

            Stat::make('Day-over-Day', $dod ? Number::format($dod['percent'], precision: 2) . '%' : 'n/a')
                ->description($dod ? 'vs ' . Number::format($dod['previous'], precision: 0) : 'Not enough history')
                ->descriptionIcon($dod && $dod['percent'] >= 0 ? 'heroicon-m-arrow-up' : 'heroicon-m-arrow-down')
                ->icon('heroicon-o-chart-pie')
                ->color(! $dod ? 'gray' : ($dod['percent'] >= 0 ? 'success' : 'danger')),

            Stat::make('Accounts', Number::format($accounts['total'], precision: 0))
                ->description(
                    ($accounts['day'] ? Carbon::parse($accounts['day'])->format('M d') . ' | ' : '')
                    . Number::format((float) $accounts['active_rate'], precision: 1) . '% active'
                )
                ->icon('heroicon-o-users')
                ->color('info'),
        ];
    }
}

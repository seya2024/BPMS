<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Filament\Widgets\Concerns\HasDistrictFilter;
use App\Models\DailyForeignCurrencyGeneration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class FxAnalyticsWidget extends BaseWidget
{
    use HasDistrictFilter, HasDateRangeFilter;

    protected ?string $heading = 'FX Generation Analytics';

    protected static ?int $sort = 10;

    protected int | string | array $columnSpan = 1;

    protected string $view = 'widgets.analytics-stats';

    protected function getStats(): array
    {
        $branchIds = $this->branchIdsForFilter();
        $range = $this->getEffectiveDateRange();
        $from = \Carbon\Carbon::parse($range['from']);
        $to = \Carbon\Carbon::parse($range['to']);

        $totals = AnalyticsService::kpiTotals(
            DailyForeignCurrencyGeneration::class,
            'amount',
            $from,
            $to,
            $branchIds
        );

        $dod = AnalyticsService::kpiDayOnDayChange(
            DailyForeignCurrencyGeneration::class,
            'amount',
            $branchIds
        );

        $compareRange = $this->getComparisonDateRange();
        $comparison = null;
        if ($compareRange) {
            $comparison = AnalyticsService::kpiTotals(
                DailyForeignCurrencyGeneration::class,
                'amount',
                \Carbon\Carbon::parse($compareRange['from']),
                \Carbon\Carbon::parse($compareRange['to']),
                $branchIds
            );
        }

        if ($totals['days'] === 0) {
            return [
                Stat::make('FX Generation', 'No data')
                    ->description('No FX generation recorded for this selection')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('gray'),
            ];
        }

        $changePercent = null;
        $changeColor = 'gray';
        $changeIcon = 'heroicon-m-minus';

        if ($comparison && $comparison['total'] > 0) {
            $changePercent = (($totals['total'] - $comparison['total']) / $comparison['total']) * 100;
            $changeColor = $changePercent >= 0 ? 'success' : 'danger';
            $changeIcon = $changePercent >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        } elseif ($dod) {
            $changePercent = $dod['percent'];
            $changeColor = $changePercent >= 0 ? 'success' : 'danger';
            $changeIcon = $changePercent >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        }

        return [
            Stat::make('Total FX Generated', Number::format($totals['total'], precision: 2))
                ->description("{$totals['days']} days | avg " . Number::format($totals['avg'], precision: 0) . '/day' . ($this->getPeriodLabel() ? ' | ' . $this->getPeriodLabel() : ''))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->icon('heroicon-o-currency-dollar')
                ->color('primary'),

            Stat::make('Period Change', $changePercent !== null ? Number::format($changePercent, precision: 2) . '%' : 'n/a')
                ->description($comparison ? 'vs ' . $this->getCompareLabel() : ($dod ? 'vs previous day' : 'Not enough history'))
                ->descriptionIcon($changeIcon)
                ->icon('heroicon-o-chart-bar')
                ->color($changeColor),

            Stat::make('Day-over-Day', $dod ? Number::format($dod['percent'], precision: 2) . '%' : 'n/a')
                ->description($dod ? 'vs ' . Number::format($dod['previous'], precision: 0) : 'Not enough history')
                ->descriptionIcon($dod && $dod['percent'] >= 0 ? 'heroicon-m-arrow-up' : 'heroicon-m-arrow-down')
                ->icon('heroicon-o-chart-pie')
                ->color(! $dod ? 'gray' : ($dod['percent'] >= 0 ? 'success' : 'danger')),

            Stat::make('Daily Target', $this->getFxTarget($branchIds))
                ->description('Branch FX targets')
                ->icon('heroicon-o-target')
                ->color('info'),
        ];
    }

    protected function getFxTarget(?array $branchIds): string
    {
        $target = \App\Models\Branch::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('id', $branchIds))
            ->sum('fx_daily_target');

        return $target > 0 ? Number::format($target, precision: 2) : 'Not set';
    }

    protected function getCompareLabel(): string
    {
        return match ($this->compareRange) {
            'previous_period' => 'previous period',
            'same_period_last_year' => 'same period last year',
            default => 'previous period',
        };
    }
}
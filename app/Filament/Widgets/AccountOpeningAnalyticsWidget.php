<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Filament\Widgets\Concerns\HasDistrictFilter;
use App\Models\DailyAccountOpening;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class AccountOpeningAnalyticsWidget extends BaseWidget
{
    use HasDistrictFilter, HasDateRangeFilter;

    protected ?string $heading = 'Account Opening Analytics';

    protected static ?int $sort = 12;

    protected int | string | array $columnSpan = 1;

    protected string $view = 'widgets.analytics-stats';

    protected function getStats(): array
    {
        $branchIds = $this->branchIdsForFilter();
        $range = $this->getEffectiveDateRange();
        $from = \Carbon\Carbon::parse($range['from']);
        $to = \Carbon\Carbon::parse($range['to']);

        // Total conventional + IFB accounts
        $totals = DailyAccountOpening::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereDate('business_day', '>=', $from->toDateString())
            ->whereDate('business_day', '<=', $to->toDateString())
            ->selectRaw('
                COALESCE(SUM(conventional_accounts + ifb_accounts),0) as total,
                COALESCE(SUM(conventional_accounts),0) as conventional,
                COALESCE(SUM(ifb_accounts),0) as ifb,
                COALESCE(SUM(target_accounts),0) as target,
                COUNT(DISTINCT business_day) as days
            ')
            ->first();

        $totalAccounts = (float) $totals->total;
        $conventional = (float) $totals->conventional;
        $ifb = (float) $totals->ifb;
        $target = (float) $totals->target;
        $days = (int) $totals->days;

        $dod = AnalyticsService::kpiDayOnDayChange(
            DailyAccountOpening::class,
            'conventional_accounts + ifb_accounts',
            $branchIds
        );

        $compareRange = $this->getComparisonDateRange();
        $comparison = null;
        if ($compareRange) {
            $comparison = DailyAccountOpening::query()
                ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
                ->whereDate('business_day', '>=', $compareRange['from'])
                ->whereDate('business_day', '<=', $compareRange['to'])
                ->selectRaw('COALESCE(SUM(conventional_accounts + ifb_accounts),0) as total')
                ->first();
        }

        if ($days === 0) {
            return [
                Stat::make('Account Openings', 'No data')
                    ->description('No account opening data recorded for this selection')
                    ->icon('heroicon-o-user-plus')
                    ->color('gray'),
            ];
        }

        $avgDaily = $days > 0 ? $totalAccounts / $days : 0;
        $achievement = $target > 0 ? ($totalAccounts / $target) * 100 : null;
        $convPct = $totalAccounts > 0 ? ($conventional / $totalAccounts) * 100 : 0;
        $ifbPct = $totalAccounts > 0 ? ($ifb / $totalAccounts) * 100 : 0;

        $changePercent = null;
        $changeColor = 'gray';
        $changeIcon = 'heroicon-m-minus';

        if ($comparison && $comparison->total > 0) {
            $changePercent = (($totalAccounts - $comparison->total) / $comparison->total) * 100;
            $changeColor = $changePercent >= 0 ? 'success' : 'danger';
            $changeIcon = $changePercent >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        } elseif ($dod) {
            $changePercent = $dod['percent'];
            $changeColor = $changePercent >= 0 ? 'success' : 'danger';
            $changeIcon = $changePercent >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        }

        $achievementColor = $achievement === null ? 'gray' : ($achievement >= 100 ? 'success' : ($achievement >= 80 ? 'warning' : 'danger'));

        return [
            Stat::make('Total Accounts Opened', Number::format($totalAccounts, precision: 0))
                ->description("{$days} days | avg " . Number::format($avgDaily, precision: 0) . '/day' . ($this->getPeriodLabel() ? ' | ' . $this->getPeriodLabel() : ''))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->icon('heroicon-o-user-plus')
                ->color('primary'),

            Stat::make('Plan Attainment', $achievement === null ? 'No target' : Number::format($achievement, precision: 1) . '%')
                ->description($target > 0 ? 'Target: ' . Number::format($target, precision: 0) : 'Set branch targets')
                ->descriptionIcon($achievement !== null && $achievement >= 100 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-circle')
                ->icon('heroicon-o-calculator')
                ->color($achievementColor),

            Stat::make('Conventional / IFB Split', Number::format($convPct, precision: 1) . '% / ' . Number::format($ifbPct, precision: 1) . '%')
                ->description("Conv: " . Number::format($conventional, precision: 0) . " | IFB: " . Number::format($ifb, precision: 0))
                ->icon('heroicon-o-rectangle-stack')
                ->color('info'),

            Stat::make('Period Change', $changePercent !== null ? Number::format($changePercent, precision: 2) . '%' : 'n/a')
                ->description($comparison ? 'vs ' . $this->getCompareLabel() : ($dod ? 'vs previous day' : 'Not enough history'))
                ->descriptionIcon($changeIcon)
                ->icon('heroicon-o-chart-bar')
                ->color($changeColor),
        ];
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
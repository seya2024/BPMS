<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use App\Models\DailyAccountPerformance;
use Filament\Widgets\LineChartWidget as BaseWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class AccountTrendChart extends BaseWidget
{
    use HasDistrictFilter;

    protected ?string $heading = 'Account Balance & New Accounts Trend';

    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 2;

    /** Cap the canvas height so the card stays compact in a narrow column. */
    protected ?string $maxHeight = '240px';

    /** Custom view so the district filter renders in the widget header. */
    protected string $view = 'widgets.analytics-chart';

    protected function getData(): array
    {
        $branchIds = $this->branchIdsForFilter();

        $aligned = AnalyticsService::alignSeries();
        $labels = $aligned['labels'];

        $from = AnalyticsService::windowStart()->toDateString();
        $to = Carbon::yesterday()->toDateString();

        $rows = DailyAccountPerformance::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to)
            ->selectRaw('DATE(business_day) d, SUM(total_accounts) total, SUM(new_accounts) new')
            ->groupBy('d')
            ->get()
            ->mapWithKeys(fn ($r) => [$r->d => ['total' => (float) $r->total, 'new' => (float) $r->new]])
            ->all();

        if ($rows === []) {
            return [
                'datasets' => [[
                    'label' => 'Total accounts',
                    'data' => array_fill(0, max(1, count($labels)), 0),
                    'borderColor' => 'rgba(156, 163, 175, 1)',
                ]],
                'labels' => $labels ?: ['No data'],
            ];
        }

        $totals = [];
        $new = [];

        foreach ($aligned['dates'] as $date) {
            $row = $rows[$date] ?? null;
            $totals[] = (float) ($row['total'] ?? 0);
            $new[] = (float) ($row['new'] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total accounts',
                    'data' => $totals,
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                    'fill' => true,
                    'yAxisID' => 'y',
                    'tension' => 0.35,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 4,
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'New accounts per day',
                    'data' => $new,
                    'borderColor' => 'rgba(234, 88, 12, 1)',
                    'backgroundColor' => 'rgba(234, 88, 12, 0.05)',
                    'fill' => false,
                    'yAxisID' => 'y1',
                    'tension' => 0.35,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 4,
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'position' => 'left',
                    'title' => ['display' => true, 'text' => 'Total accounts'],
                    'ticks' => [
                        'callback' => 'function(value) { return new Intl.NumberFormat("en-US").format(value); }',
                    ],
                ],
                'y1' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'grid' => ['drawOnChartArea' => false],
                    'title' => ['display' => true, 'text' => 'New per day'],
                ],
                'x' => [
                    'ticks' => ['maxTicksLimit' => 15, 'maxRotation' => 0, 'autoSkip' => true],
                ],
            ],
        ];
    }

    protected function getFooter(): ?string
    {
        $accounts = AnalyticsService::accountSnapshot($this->branchIdsForFilter());

        if (! $accounts['day']) {
            return null;
        }

        return 'Active ' . Number::format((float) $accounts['active'], precision: 0)
            . ' | Dormant ' . Number::format((float) $accounts['dormant'], precision: 0)
            . ' | Reactivated ' . Number::format((float) $accounts['reactivated'], precision: 0)
            . ' as at ' . Carbon::parse($accounts['day'])->format('M d, Y');
    }
}

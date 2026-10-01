<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use App\Models\DailyDepositPerformance;
use Filament\Widgets\BarChartWidget as BaseWidget;
use Illuminate\Support\Carbon;

class NetChangeChart extends BaseWidget
{
    use HasDistrictFilter;

    protected ?string $heading = 'Net Deposit Change by Day (Inflow - Outflow)';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 2;

    /** Cap the canvas height so the card stays compact in a narrow column. */
    protected ?string $maxHeight = '240px';

    /** Custom view so the district filter renders in the widget header. */
    protected string $view = 'widgets.analytics-chart';

    protected function getData(): array
    {
        $branchIds = $this->branchIdsForFilter();
        $aligned = AnalyticsService::alignSeries(branchIds: $branchIds);

        $from = AnalyticsService::windowStart()->toDateString();
        $to = Carbon::yesterday()->toDateString();

        $rows = DailyDepositPerformance::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to)
            ->selectRaw('DATE(business_day) d, SUM(net_deposit_change) n')
            ->groupBy('d')
            ->pluck('n', 'd')
            ->map(fn ($v) => (float) $v)
            ->all();

        $net = [];

        foreach ($aligned['dates'] as $date) {
            $net[] = round($rows[$date] ?? 0.0, 2);
        }

        // Positive days green, negative days red.
        $colors = array_map(
            fn ($v) => $v >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)',
            $net
        );

        return [
            'datasets' => [[
                'label' => 'Net change',
                'data' => $net,
                'backgroundColor' => $colors,
                'borderColor' => array_map(fn ($c) => str_replace('0.8', '1', $c), $colors),
                'borderWidth' => 1,
            ]],
            'labels' => $aligned['labels'] ?: ['No data'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const v = context.parsed.y;
                            return (v >= 0 ? "Net growth: ETB " : "Net decline: ETB ")
                                + new Intl.NumberFormat("en-US").format(Math.abs(v));
                        }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'ticks' => [
                        'callback' => 'function(value) {
                            return new Intl.NumberFormat("en-US", { maximumFractionDigits: 0 }).format(value);
                        }',
                    ],
                ],
                'x' => [
                    'ticks' => ['maxTicksLimit' => 15, 'maxRotation' => 0, 'autoSkip' => true],
                ],
            ],
        ];
    }
}

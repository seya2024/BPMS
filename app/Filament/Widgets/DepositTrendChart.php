<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use App\Models\BranchDepositPlan;
use Filament\Widgets\LineChartWidget as BaseWidget;
use Illuminate\Support\Number;

class DepositTrendChart extends BaseWidget
{
    use HasDistrictFilter;

    protected ?string $heading = 'Deposit Trend with 7-Day Moving Average';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 2;

    /** Cap the canvas height so the card stays compact in a narrow column. */
    protected ?string $maxHeight = '240px';

    /** Custom view so the district filter renders in the widget header. */
    protected string $view = 'widgets.analytics-chart';

    protected function getData(): array
    {
        $branchIds = $this->branchIdsForFilter();
        $aligned = AnalyticsService::alignSeries(branchIds: $branchIds);

        if (! array_sum($aligned['values'])) {
            return [
                'datasets' => [[
                    'label' => 'Daily deposits',
                    'data' => array_fill(0, max(1, count($aligned['labels'])), 0),
                    'borderColor' => 'rgba(156, 163, 175, 1)',
                ]],
                'labels' => $aligned['labels'] ?: ['No data'],
            ];
        }

        $ma = AnalyticsService::movingAverage($aligned['values'], 7);

        $dailyTarget = (float) BranchDepositPlan::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('daily_target_amount');

        $datasets = [
            [
                'label' => 'Daily deposits',
                'data' => array_map(fn ($v) => round($v, 2), $aligned['values']),
                'borderColor' => 'rgba(59, 130, 246, 1)',
                'backgroundColor' => 'rgba(59, 130, 246, 0.12)',
                'fill' => true,
                'tension' => 0.35,
                'pointRadius' => 2,
                'pointHoverRadius' => 5,
                'borderWidth' => 1,
            ],
            [
                'label' => '7-day moving average',
                'data' => array_map(fn ($v) => round($v, 2), $ma),
                'borderColor' => 'rgba(234, 88, 12, 1)',
                'backgroundColor' => 'rgba(234, 88, 12, 0.05)',
                'fill' => false,
                'tension' => 0.35,
                'pointRadius' => 0,
                'pointHoverRadius' => 4,
                'borderWidth' => 2,
            ],
        ];

        if ($dailyTarget > 0) {
            $datasets[] = [
                'label' => 'Daily target',
                'data' => array_fill(0, count($aligned['values']), round($dailyTarget, 2)),
                'borderColor' => 'rgba(156, 163, 175, 1)',
                'backgroundColor' => 'rgba(156, 163, 175, 0.05)',
                'fill' => false,
                'borderDash' => [6, 6],
                'pointRadius' => 0,
                'pointHoverRadius' => 0,
                'borderWidth' => 1,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $aligned['labels'],
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
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.dataset.label + ": ETB "
                                + new Intl.NumberFormat("en-US").format(context.parsed.y);
                        }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
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

    protected function getFooter(): ?string
    {
        $aligned = AnalyticsService::alignSeries(branchIds: $this->branchIdsForFilter());

        if (! array_sum($aligned['values'])) {
            return null;
        }

        $values = $aligned['values'];
        $ma = AnalyticsService::movingAverage($values, 7);

        $last = end($values);
        $lastMa = end($ma);
        $trend = $count = count($ma);
        $direction = 'steady';

        if ($count >= 7) {
            $recent = array_slice($ma, -7);
            $prior = array_slice($ma, -14, 7);

            if (count($recent) === 7 && count($prior) === 7) {
                $r = array_sum($recent) / 7;
                $p = array_sum($prior) / 7;
                $direction = $r > $p ? 'improving' : ($r < $p ? 'softening' : 'steady');
            }
        }

        return 'Latest day ETB ' . Number::format($last, precision: 0)
            . ' | 7-day average ETB ' . Number::format($lastMa, precision: 0)
            . ' | Trend: ' . ucfirst($direction);
    }
}

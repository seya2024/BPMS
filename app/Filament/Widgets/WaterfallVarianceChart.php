<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateRangeFilter;
use App\Filament\Widgets\Concerns\HasDistrictFilter;
use App\Models\Branch;
use App\Models\BranchDepositPlan;
use App\Models\DailyDepositPerformance;
use Filament\Widgets\ChartWidget;

class WaterfallVarianceChart extends ChartWidget
{
    use HasDistrictFilter, HasDateRangeFilter;

    protected ?string $heading = 'Variance Waterfall';

    protected static ?int $sort = 14;

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '350px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $branchIds = $this->branchIdsForFilter();
        $range = $this->getEffectiveDateRange();
        $from = \Carbon\Carbon::parse($range['from']);
        $to = \Carbon\Carbon::parse($range['to']);
        $fromDate = $from->toDateString();
        $toDate = $to->toDateString();

        // Get branch-level actual vs target
        $attainment = \App\Filament\Widgets\AnalyticsService::branchAttainment($from, $to, $branchIds)
            ->filter(fn ($r) => $r['achievable'] > 0)
            ->sortBy('variance')
            ->values();

        if ($attainment->isEmpty()) {
            return [
                'datasets' => [[
                    'label' => 'Variance',
                    'data' => [0],
                    'backgroundColor' => ['rgba(156, 163, 175, 0.8)'],
                ]],
                'labels' => ['No data'],
            ];
        }

        $labels = [];
        $variances = [];
        $colors = [];

        foreach ($attainment as $row) {
            $labels[] = $row['branch'];
            $variance = $row['variance'];
            $variances[] = round($variance, 2);
            $colors[] = $variance >= 0 ? 'rgba(34, 197, 94, 0.8)' : 'rgba(239, 68, 68, 0.8)'; // green/red
        }

        // Add total at the end
        $totalVariance = array_sum($variances);
        $labels[] = 'Total';
        $variances[] = round($totalVariance, 2);
        $colors[] = $totalVariance >= 0 ? 'rgba(34, 197, 94, 1)' : 'rgba(239, 68, 68, 1)';

        return [
            'datasets' => [[
                'label' => 'Variance vs Target (ETB)',
                'data' => $variances,
                'backgroundColor' => $colors,
                'borderColor' => array_map(fn ($c) => str_replace('0.8', '1', str_replace('1', '1', $c)), $colors),
                'borderWidth' => 1,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'indexAxis' => 'y',
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.dataset.label + ": ETB "
                                + new Intl.NumberFormat("en-US").format(context.parsed.x);
                        }',
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'callback' => 'function(value) {
                            return new Intl.NumberFormat("en-US", { maximumFractionDigits: 0 }).format(value);
                        }',
                    ],
                    'grid' => [
                        'display' => true,
                    ],
                ],
                'y' => [
                    'ticks' => [
                        'maxTicksLimit' => 20,
                    ],
                ],
            ],
        ];
    }

    protected function getFooter(): ?string
    {
        $branchIds = $this->branchIdsForFilter();
        $range = $this->getEffectiveDateRange();
        $from = \Carbon\Carbon::parse($range['from']);
        $to = \Carbon\Carbon::parse($range['to']);

        $attainment = \App\Filament\Widgets\AnalyticsService::branchAttainment($from, $to, $branchIds)
            ->filter(fn ($r) => $r['achievable'] > 0);

        if ($attainment->isEmpty()) {
            return null;
        }

        $totalActual = $attainment->sum('actual');
        $totalTarget = $attainment->sum('achievable');
        $totalVariance = $totalActual - $totalTarget;
        $achievement = $totalTarget > 0 ? ($totalActual / $totalTarget) * 100 : 0;
        $onTarget = $attainment->filter(fn ($r) => $r['achievement'] >= 100)->count();
        $totalBranches = $attainment->count();

        return "Total: ETB " . \Illuminate\Support\Number::format($totalActual, precision: 0)
            . " | Target: ETB " . \Illuminate\Support\Number::format($totalTarget, precision: 0)
            . " | Variance: ETB " . \Illuminate\Support\Number::format($totalVariance, precision: 0)
            . " | Achievement: " . \Illuminate\Support\Number::format($achievement, precision: 1) . "%"
            . " | {$onTarget}/{$totalBranches} branches on target";
    }
}
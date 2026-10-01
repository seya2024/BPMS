<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\BranchDepositPlan;
use App\Models\DailyDepositPerformance;
use Carbon\Carbon;
use Filament\Widgets\BarChartWidget;
use App\Models\District;

class DepositPerformanceChart extends BarChartWidget
{
    protected ?string $heading = 'Deposit Performance vs Target';

    /** One third of the dashboard row, matching the analytics widgets. */
    protected int | string | array $columnSpan = 1;

    /** Cap the canvas height so the card stays compact in a narrow column. */
    protected ?string $maxHeight = '220px';

    protected static ?int $sort = 1;

     
    protected function getFilters(): ?array
    {
        return [
            '' => 'All districts',
        ] + District::pluck('name', 'id')->toArray();
    }


    protected function getData(): array
    {
        $startDate = Carbon::now()->subDays(30);

        // Get actual deposits for the last 30 days
        $actualDeposits = DailyDepositPerformance::where('business_day', '>=', $startDate)
            ->selectRaw('DATE(business_day) as date, SUM(total_deposit_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Get target deposits (daily average from plans)
        $branchPlans = BranchDepositPlan::with('branch')->get();

        $dailyTargets = [];
        foreach ($branchPlans as $plan) {
            $dailyTarget = (float) ($plan->daily_target_amount ?? 0);
            if ($dailyTarget > 0) {
                $dailyTargets[] = $dailyTarget;
            }
        }

        $totalDailyTarget = array_sum($dailyTargets);

        // Build labels for the last 30 days
        $labels = [];
        $actualData = [];
        $targetData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('M d');
            $actualData[] = round((float) ($actualDeposits[$date] ?? 0), 2);
            $targetData[] = round($totalDailyTarget, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Actual Deposits',
                    'data' => $actualData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Target Deposits',
                    'data' => $targetData,
                    'backgroundColor' => 'rgba(156, 163, 175, 0.6)',
                    'borderColor' => 'rgba(156, 163, 175, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.dataset.label || "";
                            if (label) {
                                label += ": ";
                            }
                            label += new Intl.NumberFormat("en-US", { style: "currency", currency: "ETB" }).format(context.parsed.y);
                            return label;
                        }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) {
                            return new Intl.NumberFormat("en-US", { style: "currency", currency: "ETB", maximumFractionDigits: 0 }).format(value);
                        }',
                    ],
                ],
            ],
        ];
    }
}

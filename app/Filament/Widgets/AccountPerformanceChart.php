<?php

namespace App\Filament\Widgets;

use App\Models\BranchAccountPlan;
use App\Models\DailyAccountPerformance;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;

class AccountPerformanceChart extends LineChartWidget
{
    protected ?string $heading = 'Account Performance vs Target';

    /** One third of the dashboard row, matching the analytics widgets. */
    protected int | string | array $columnSpan = 2;

    /** Cap the canvas height so the card stays compact in a narrow column. */
    protected ?string $maxHeight = '240px';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $startDate = Carbon::now()->subDays(30);

        // Get actual accounts for the last 30 days
        $actualAccounts = DailyAccountPerformance::where('business_day', '>=', $startDate)
            ->selectRaw('DATE(business_day) as date, SUM(total_accounts) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Get target accounts (daily average from plans)
        $branchPlans = BranchAccountPlan::with('branch')->get();

        $dailyTargets = [];
        foreach ($branchPlans as $plan) {
            $dailyTarget = (int) ($plan->daily_target_accounts ?? 0);
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
            $actualData[] = (int) ($actualAccounts[$date] ?? 0);
            $targetData[] = $totalDailyTarget;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Actual Accounts',
                    'data' => $actualData,
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                ],
                [
                    'label' => 'Target Accounts',
                    'data' => $targetData,
                    'borderColor' => 'rgba(156, 163, 175, 1)',
                    'backgroundColor' => 'rgba(156, 163, 175, 0.05)',
                    'fill' => false,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                    'borderDash' => [5, 5],
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
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) {
                            return new Intl.NumberFormat("en-US").format(value);
                        }',
                    ],
                ],
            ],
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use App\Models\District;
use App\Models\BranchDepositPlan;
use App\Models\DailyDepositPerformance;
use Carbon\Carbon;
use Filament\Widgets\PieChartWidget;

class DistrictPerformanceChart extends PieChartWidget
{
    use HasDistrictFilter;

    protected ?string $heading = 'Deposit Achievement by District';

    /** Half the dashboard row, matching the other charts. */
    protected int | string | array $columnSpan = 2;

    /** Custom view so the district filter renders in the widget header. */
    protected string $view = 'widgets.analytics-chart';

    /** Cap the canvas height so the card stays compact. */
    protected ?string $maxHeight = '240px';

    protected static ?int $sort = 4;

        protected function getFilters(): ?array
    {
        return [
            '' => 'All districts',
        ] + District::pluck('name', 'id')->toArray();
    }

    protected function getData(): array
    {
        $startDate = Carbon::now()->startOfMonth();

        $districts = District::with(['branches.branchDepositPlans'])->get();

        $labels = [];
        $data = [];
        $colors = [
            'rgba(59, 130, 246, 0.8)',   // Blue
            'rgba(16, 185, 129, 0.8)',   // Green
            'rgba(245, 158, 11, 0.8)',   // Amber
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(139, 92, 246, 0.8)',   // Violet
            'rgba(236, 72, 153, 0.8)',   // Pink
            'rgba(20, 184, 166, 0.8)',   // Teal
            'rgba(249, 115, 22, 0.8)',   // Orange
            'rgba(99, 102, 241, 0.8)',   // Indigo
            'rgba(168, 85, 247, 0.8)',   // Purple
        ];

        $colorIndex = 0;

        foreach ($districts as $district) {
            $totalActual = 0;

            foreach ($district->branches as $branch) {
                $actual = DailyDepositPerformance::where('branch_id', $branch->id)
                    ->where('business_day', '>=', $startDate)
                    ->sum('total_deposit_amount');

                $totalActual += (float) $actual;
            }

            if ($totalActual > 0) {
                $labels[] = $district->name;
                $data[] = round($totalActual, 2);
                $colorIndex++;
            }
        }

        // Handle empty data
        if (empty($data)) {
            $labels = ['No Data'];
            $data = [1];
            $colors = ['rgba(156, 163, 175, 0.5)'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Deposit Achievement',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_map(function ($color) {
                        return str_replace('0.8', '1', $color);
                    }, array_slice($colors, 0, count($data))),
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
                    'position' => 'right',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.label || "";
                            if (label) {
                                label += ": ";
                            }
                            label += new Intl.NumberFormat("en-US", { style: "currency", currency: "ETB" }).format(context.parsed);
                            return label;
                        }',
                    ],
                ],
            ],
        ];
    }
}

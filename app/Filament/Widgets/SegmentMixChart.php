<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use Filament\Widgets\DoughnutChartWidget as BaseWidget;
use Illuminate\Support\Number;

class SegmentMixChart extends BaseWidget
{
    use HasDistrictFilter;

    protected ?string $heading = 'Deposit Mix by Segment';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 2;

        /** Cap the canvas height so the card stays compact in a narrow column. */
    protected ?string $maxHeight = '240px';

    /** Custom view so the district filter renders in the widget header. */
    protected string $view = 'widgets.analytics-chart';

    private const COLORS = [
        'Corporate' => 'rgba(59, 130, 246, 0.85)',
        'Retail' => 'rgba(16, 185, 129, 0.85)',
        'MSME' => 'rgba(245, 158, 11, 0.85)',
    ];

    protected function getData(): array
    {
        $mix = AnalyticsService::segmentMix(branchIds: $this->branchIdsForFilter());

        // Always show all three segments so the chart shape is stable between loads.
        $segments = ['Corporate', 'Retail', 'MSME'];
        $data = [];
        $colors = [];

        foreach ($segments as $segment) {
            $data[] = round((float) ($mix[$segment] ?? 0), 2);
            $colors[] = self::COLORS[$segment];
        }

        return [
            'datasets' => [[
                'label' => 'Deposits by segment',
                'data' => $data,
                'backgroundColor' => $colors,
                'borderColor' => array_map(fn ($c) => str_replace('0.85', '1', $c), $colors),
                'borderWidth' => 1,
            ]],
            'labels' => $segments,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'bottom'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : "0.0";
                            return context.label + ": ETB "
                                + new Intl.NumberFormat("en-US").format(context.parsed)
                                + " (" + pct + "%)";
                        }',
                    ],
                ],
            ],
        ];
    }

    protected function getFooter(): ?string
    {
        $mix = AnalyticsService::segmentMix(branchIds: $this->branchIdsForFilter());

        if (! array_sum($mix)) {
            return 'No segment data recorded yet.';
        }

        $parts = [];

        foreach (['Corporate', 'Retail', 'MSME'] as $segment) {
            $parts[] = $segment . ': ' . Number::format((float) ($mix[$segment] ?? 0), precision: 0);
        }

        return implode('  |  ', $parts);
    }
}

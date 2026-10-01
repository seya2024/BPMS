<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use Filament\Widgets\DoughnutChartWidget as BaseWidget;
use Illuminate\Support\Number;

class BankingTypeSplitChart extends BaseWidget
{
    use HasDistrictFilter;

    protected ?string $heading = 'Conventional vs Islamic Banking';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 2;

        /** Cap the canvas height so the card stays compact in a narrow column. */
    protected ?string $maxHeight = '240px';

    /** Custom view so the district filter renders in the widget header. */
    protected string $view = 'widgets.analytics-chart';

    protected function getData(): array
    {
        $mix = AnalyticsService::bankingTypeMix(branchIds: $this->branchIdsForFilter());

        $conventional = (float) ($mix['Conventional'] ?? 0);
        $ifb = (float) ($mix['IFB'] ?? 0);

        // Keep both slices present so the split reads consistently.
        if ($conventional <= 0 && $ifb <= 0) {
            $labels = ['No data'];
            $data = [1];
            $colors = ['rgba(156, 163, 175, 0.5)'];
        } else {
            $labels = ['Conventional', 'IFB'];
            $data = [round($conventional, 2), round($ifb, 2)];
            $colors = ['rgba(59, 130, 246, 0.85)', 'rgba(16, 185, 129, 0.85)'];
        }

        return [
            'datasets' => [[
                'label' => 'Deposits by banking type',
                'data' => $data,
                'backgroundColor' => $colors,
                'borderColor' => array_map(fn ($c) => str_replace('0.85', '1', $c), $colors),
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
        $mix = AnalyticsService::bankingTypeMix(branchIds: $this->branchIdsForFilter());
        $total = array_sum($mix);

        if ($total <= 0) {
            return 'No deposit data recorded yet.';
        }

        $ifbShare = (($mix['IFB'] ?? 0) / $total) * 100;

        return 'IFB share: ' . Number::format($ifbShare, precision: 1) . '% of total deposits';
    }
}

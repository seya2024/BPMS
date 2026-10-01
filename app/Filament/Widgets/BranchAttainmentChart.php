<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use Filament\Widgets\BarChartWidget as BaseWidget;
use Illuminate\Support\Number;

/**
 * Horizontal bar chart of branch attainment, replacing the BranchAttainmentTable.
 *
 * Fits a single dashboard column far better than a 9-column table and makes the
 * spread between the strongest and weakest branches immediately visible.
 */
class BranchAttainmentChart extends BaseWidget
{
    use HasDistrictFilter;

    protected ?string $heading = 'Top vs Bottom Branch Attainment';

    protected ?string $description = 'Deposit actual vs daily target, % of achievable over the reporting window. Green = target met, amber = 80-99%, red = below 80%.';

    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 2;

    /** Custom view so the district filter renders in the widget header. */
    protected string $view = 'widgets.analytics-chart';

    /** Cap the canvas height so the card stays compact in a narrow column. */
    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $rows = AnalyticsService::branchAttainment(branchIds: $this->branchIdsForFilter())
            ->filter(fn ($r) => $r['achievement'] !== null && $r['days'] > 0)
            ->sortByDesc('achievement')
            ->values();

        if ($rows->isEmpty()) {
            return [
                'datasets' => [[
                    'label' => 'Attainment %',
                    'data' => [0],
                    'backgroundColor' => ['rgba(156, 163, 175, 0.5)'],
                ]],
                'labels' => ['No targets set'],
            ];
        }

        // Show the strongest few and the weakest few; the middle is noise here.
        // Both ends are capped at 5. The bottom slice used to be sized as
        // count - topCount, which for 30 branches meant 5 + 25 = every branch, so
        // the chart was a flat full ranking and the "Top vs Bottom" framing meant
        // nothing. Cap both ends and de-duplicate so a small branch count cannot
        // repeat a branch across both slices.
        $topCount = min(5, $rows->count());
        $bottomCount = min(5, $rows->count() - $topCount);

        $selected = $rows->take($topCount)
            ->concat($bottomCount > 0 ? $rows->reverse()->take($bottomCount) : collect())
            ->unique('branch_id')
            ->values();

        // Keep the strongest at the top of a horizontal bar chart.
        $selected = $selected->sortByDesc('achievement')->values();

        $data = [];
        $colors = [];

        foreach ($selected as $row) {
            $data[] = round((float) $row['achievement'], 1);
            $colors[] = match (true) {
                $row['achievement'] >= 100 => 'rgba(16, 185, 129, 0.85)',
                $row['achievement'] >= 80 => 'rgba(245, 158, 11, 0.85)',
                default => 'rgba(239, 68, 68, 0.85)',
            };
        }

        return [
            'datasets' => [[
                'label' => 'Attainment %',
                'data' => $data,
                'backgroundColor' => $colors,
                'borderColor' => array_map(fn ($c) => str_replace('0.85', '1', $c), $colors),
                'borderWidth' => 1,
                'barThickness' => 14,
            ]],
            'labels' => $selected->pluck('branch')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const v = context.parsed.x;
                            const s = v >= 100 ? "Met target" : (v >= 80 ? "Near target" : "Below target");
                            return context.label + ": " + v.toFixed(1) + "% (" + s + ")";
                        }',
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return value + "%"; }',
                    ],
                ],
                'y' => [
                    'ticks' => ['callback' => 'function(label) { return label.length > 22 ? label.slice(0, 21) + "…" : label; }'],
                ],
            ],
        ];
    }

    protected function getFooter(): ?string
    {
        $rows = AnalyticsService::branchAttainment(branchIds: $this->branchIdsForFilter())
            ->filter(fn ($r) => $r['achievement'] !== null && $r['days'] > 0);

        if ($rows->isEmpty()) {
            return 'Set branch targets to compare attainment.';
        }

        $best = $rows->sortByDesc('achievement')->first();
        $worst = $rows->sortBy('achievement')->first();

        return 'Best ' . Number::format((float) $best['achievement'], precision: 1) . '% ('
            . $best['branch'] . ') | Worst ' . Number::format((float) $worst['achievement'], precision: 1) . '% ('
            . $worst['branch'] . ')';
    }
}

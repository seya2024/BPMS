<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class PlanAchievementWidget extends BaseWidget
{
    use HasDistrictFilter;

    protected ?string $heading = 'Plan Attainment (Last 45 Days)';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 1;

    /** Compact 2-up layout so the widget fits a third of the dashboard row. */
    protected int | array | null $columns = 1;

    protected string $view = 'widgets.analytics-stats';

    /** StatsOverviewWidget does not declare $filter, so the trait's state lives here. */
    public ?string $filter = '';

    protected function getStats(): array
    {
        $rows = AnalyticsService::branchAttainment(branchIds: $this->branchIdsForFilter())
            ->filter(fn ($r) => $r['days'] > 0);

        if ($rows->isEmpty()) {
            return [
                Stat::make('Plan Attainment', 'No data')
                    ->description('No performance recorded against a plan yet')
                    ->icon('heroicon-o-calculator')
                    ->color('gray'),
            ];
        }

        $actual = (float) $rows->sum('actual');
        $achievable = (float) $rows->sum('achievable');

        // Branch-level average so one dominant branch cannot skew the headline.
        $branchRates = $rows->pluck('achievement')->filter(fn ($v) => $v !== null);
        $avgRate = $branchRates->isNotEmpty() ? (float) $branchRates->average() : 0.0;

        $onTarget = $branchRates->filter(fn ($v) => $v >= 100)->count();
        $near = $branchRates->filter(fn ($v) => $v >= 80 && $v < 100)->count();
        $below = $branchRates->filter(fn ($v) => $v < 80)->count();

        $total = max(1, $branchRates->count());

        $best = $rows->sortByDesc('achievement')->first();
        $worst = $rows->filter(fn ($r) => $r['achievement'] !== null)->sortBy('achievement')->first();

        // Four stats keeps this card compact at quarter width.
        return [
            Stat::make('Overall Achievement', Number::format($avgRate, precision: 1) . '%')
                ->description($branchRates->count() . ' branches with a target')
                ->icon('heroicon-o-calculator')
                ->color($avgRate >= 100 ? 'success' : ($avgRate >= 80 ? 'warning' : 'danger')),

            Stat::make('On Target', $onTarget)
                ->description(round($onTarget / $total * 100) . '% at 100%+')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Top Performer', $best ? $best['branch'] : 'n/a')
                ->description($best && $best['achievement'] !== null ? Number::format($best['achievement'], precision: 1) . '% attainment' : 'No target set')
                ->icon('heroicon-o-trophy')
                ->color('success'),

            Stat::make('Needs Attention', $worst ? $worst['branch'] : 'n/a')
                ->description($worst && $worst['achievement'] !== null ? Number::format($worst['achievement'], precision: 1) . '% attainment' : 'No target set')
                ->icon('heroicon-o-exclamation-circle')
                ->color('danger'),
        ];
    }
}

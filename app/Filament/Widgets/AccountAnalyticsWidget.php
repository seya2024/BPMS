<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDistrictFilter;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class AccountAnalyticsWidget extends BaseWidget
{
    use HasDistrictFilter;

    protected ?string $heading = 'Account Analytics';

    protected static ?int $sort = 8;

    protected int | string | array $columnSpan = 1;

    /** Compact 2-up layout so the widget fits a third of the dashboard row. */
    protected int | array | null $columns = 1;

    protected string $view = 'widgets.analytics-stats';

    /** StatsOverviewWidget does not declare $filter, so the trait's state lives here. */
    public ?string $filter = '';

    protected function getStats(): array
    {
        $branchIds = $this->branchIdsForFilter();

        $snapshot = AnalyticsService::accountSnapshot($branchIds);
        $targets = AnalyticsService::accountTargets($branchIds);

        if (! $snapshot['day']) {
            return [
                Stat::make('Account Analytics', 'No data')
                    ->description('No account performance recorded for this selection')
                    ->icon('heroicon-o-users')
                    ->color('gray'),
            ];
        }

        $day = Carbon::parse($snapshot['day']);
        $total = (float) $snapshot['total'];

        // Achievable accounts = daily account target x days actually reported.
        // Carbon 3 diffInDays() is signed, so the earlier date must come first:
        // $day->diffInDays($windowStart) returned -44, and the max(0, ...) below
        // then hid the sign error by reporting "no target" instead of a figure.
        $windowStart = AnalyticsService::windowStart();
        $daysReported = $windowStart->diffInDays($day) + 1;
        $achievable = $targets['daily'] * max(0, $daysReported);

        // New accounts over the window for this selection.
        $newTotal = AnalyticsService::accountFlowTotals($branchIds)['new'] ?? 0.0;

        $achievement = $achievable > 0 ? ($newTotal / $achievable) * 100 : null;

        // Four stats keeps this card compact at quarter width.
        return [
            Stat::make('Total Accounts', Number::format($total, precision: 0))
                ->description('As at ' . $day->format('M d, Y'))
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Active', Number::format((float) $snapshot['active'], precision: 0))
                ->description(Number::format((float) $snapshot['active_rate'], precision: 1) . '% of portfolio')
                ->icon('heroicon-o-check-badge')
                ->color('success'),

            Stat::make('Dormant', Number::format((float) $snapshot['dormant'], precision: 0))
                ->description($total > 0 ? Number::format(((float) $snapshot['dormant'] / $total) * 100, precision: 1) . '% of portfolio' : 'n/a')
                ->icon('heroicon-o-pause-circle')
                ->color('warning'),

            Stat::make('Plan Attainment', $achievement === null ? 'No target' : Number::format($achievement, precision: 1) . '%')
                ->description($achievable > 0 ? 'of ' . Number::format($achievable, precision: 0) . ' new' : 'Set branch account targets')
                ->icon('heroicon-o-calculator')
                ->color($achievement === null ? 'gray' : ($achievement >= 100 ? 'success' : ($achievement >= 80 ? 'warning' : 'danger'))),
        ];
    }
}

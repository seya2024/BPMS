<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\BranchAccountPlan;
use App\Models\BranchDepositPlan;
use App\Models\DailyAccountOpening;
use App\Models\DailyAccountPerformance;
use App\Models\DailyDepositPerformance;
use App\Models\DailyForeignCurrencyGeneration;
use App\Models\DailySuperAppSubscription;
use Carbon\Carbon as BaseCarbon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Shared aggregation helpers for the analytics widgets.
 *
 * All queries return scalars keyed in PHP so the dashboard does not fan out into
 * hundreds of round trips, and every date filter uses whereDate() because a bare
 * date string does not match a stored datetime on every driver.
 */
class AnalyticsService
{
    public static function windowStart(int $days = 45): Carbon
    {
        return Carbon::yesterday()->startOfDay()->subDays($days - 1);
    }

    /**
     * System-wide deposit total + net change over the window.
     *
     * @param  array<int, int>|null  $branchIds  Null means every branch.
     * @return array{total: float, net: float, inflow: float, outflow: float, new: float, days: int}
     */
    public static function depositTotals(?BaseCarbon $from = null, ?BaseCarbon $to = null, ?array $branchIds = null): array
    {
        $from ??= self::windowStart();
        $to ??= Carbon::yesterday();

        $row = DailyDepositPerformance::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereDate('business_day', '>=', $from->toDateString())
            ->whereDate('business_day', '<=', $to->toDateString())
            ->selectRaw('COALESCE(SUM(total_deposit_amount),0) t, COALESCE(SUM(net_deposit_change),0) n, COALESCE(SUM(deposit_inflow_amount),0) i, COALESCE(SUM(deposit_outflow_amount),0) o, COALESCE(SUM(new_deposit_amount),0) nd, COUNT(DISTINCT business_day) d')
            ->first();

        return [
            'total' => (float) $row->t,
            'net' => (float) $row->n,
            'inflow' => (float) $row->i,
            'outflow' => (float) $row->o,
            'new' => (float) $row->nd,
            'days' => (int) $row->d,
        ];
    }

    /**
     * Generic KPI totals for any daily performance model.
     *
     * @param  class-string  $model
     * @param  string  $amountColumn
     * @param  array<int, int>|null  $branchIds
     * @return array{total: float, net: float, days: int, avg: float}
     */
    public static function kpiTotals(string $model, string $amountColumn, ?BaseCarbon $from = null, ?BaseCarbon $to = null, ?array $branchIds = null): array
    {
        $from ??= self::windowStart();
        $to ??= Carbon::yesterday();

        $row = $model::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereDate('business_day', '>=', $from->toDateString())
            ->whereDate('business_day', '<=', $to->toDateString())
            ->selectRaw('COALESCE(SUM(' . $amountColumn . '),0) t, COUNT(DISTINCT business_day) d')
            ->first();

        $total = (float) $row->t;
        $days = (int) $row->d;

        return [
            'total' => $total,
            'net' => $total,
            'days' => $days,
            'avg' => $days > 0 ? $total / $days : 0,
        ];
    }

    /**
     * Daily series for any KPI model.
     *
     * @param  class-string  $model
     * @param  string  $amountColumn
     * @param  array<int, int>|null  $branchIds
     * @return array<string, float>
     */
    public static function dailyKpiSeries(string $model, string $amountColumn, ?BaseCarbon $from = null, ?BaseCarbon $to = null, ?array $branchIds = null): array
    {
        $from ??= self::windowStart();
        $to ??= Carbon::yesterday();

        return $model::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereDate('business_day', '>=', $from->toDateString())
            ->whereDate('business_day', '<=', $to->toDateString())
            ->selectRaw('DATE(business_day) d, SUM(' . $amountColumn . ') t')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('t', 'd')
            ->map(fn ($v) => (float) $v)
            ->all();
    }

    /**
     * Daily deposit totals across the window, keyed by Y-m-d.
     *
     * @param  array<int, int>|null  $branchIds
     * @return array<string, float>
     */
    public static function dailyDepositSeries(?BaseCarbon $from = null, ?BaseCarbon $to = null, ?array $branchIds = null): array
    {
        $from ??= self::windowStart();
        $to ??= Carbon::yesterday();

        return DailyDepositPerformance::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereDate('business_day', '>=', $from->toDateString())
            ->whereDate('business_day', '<=', $to->toDateString())
            ->selectRaw('DATE(business_day) d, SUM(total_deposit_amount) t')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('t', 'd')
            ->map(fn ($v) => (float) $v)
            ->all();
    }

    /**
     * Simple trailing moving average.
     *
     * @param  array<int, float>  $values
     * @return array<int, float>
     */
    public static function movingAverage(array $values, int $period = 7): array
    {
        $out = [];
        $count = count($values);

        for ($i = 0; $i < $count; $i++) {
            $start = max(0, $i - $period + 1);
            $slice = array_slice($values, $start, $i - $start + 1);
            $out[] = $slice ? array_sum($slice) / count($slice) : 0.0;
        }

        return $out;
    }

    /**
     * Segment mix (Corporate / Retail / MSME) over the window.
     *
     * @param  array<int, int>|null  $branchIds
     * @return array<string, float>
     */
    public static function segmentMix(?BaseCarbon $from = null, ?BaseCarbon $to = null, ?array $branchIds = null): array
    {
        $from ??= self::windowStart();
        $to ??= Carbon::yesterday();

        return DB::table('daily_deposit_performance_details as d')
            ->join('business_segments as s', 's.id', '=', 'd.business_segment_id')
            ->when($branchIds !== null, fn ($q) => $q->whereIn('d.branch_id', $branchIds))
            ->whereDate('d.business_day', '>=', $from->toDateString())
            ->whereDate('d.business_day', '<=', $to->toDateString())
            ->selectRaw('s.name, SUM(d.amount) total')
            ->groupBy('s.name')
            ->pluck('total', 'name')
            ->map(fn ($v) => (float) $v)
            ->all();
    }

    /**
     * Conventional vs IFB split over the window.
     *
     * @param  array<int, int>|null  $branchIds
     * @return array<string, float>
     */
    public static function bankingTypeMix(?BaseCarbon $from = null, ?BaseCarbon $to = null, ?array $branchIds = null): array
    {
        $from ??= self::windowStart();
        $to ??= Carbon::yesterday();

        return DB::table('daily_deposit_performance_details as d')
            ->join('banking_types as b', 'b.id', '=', 'd.banking_type_id')
            ->when($branchIds !== null, fn ($q) => $q->whereIn('d.branch_id', $branchIds))
            ->whereDate('d.business_day', '>=', $from->toDateString())
            ->whereDate('d.business_day', '<=', $to->toDateString())
            ->selectRaw('b.name, SUM(d.amount) total')
            ->groupBy('b.name')
            ->pluck('total', 'name')
            ->map(fn ($v) => (float) $v)
            ->all();
    }

    /**
     * Per-branch actual vs target over the window.
     *
     * "Achievable" target is the branch daily target multiplied by the number of
     * distinct business days it actually reported, so days without data are not
     * silently counted as a shortfall.
     *
     * @param  array<int, int>|null  $branchIds  Null means every branch.
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function branchAttainment(?BaseCarbon $from = null, ?BaseCarbon $to = null, ?array $branchIds = null)
    {
        $from ??= self::windowStart();
        $to ??= Carbon::yesterday();
        $fromDate = $from->toDateString();
        $toDate = $to->toDateString();

        $actual = DailyDepositPerformance::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereDate('business_day', '>=', $fromDate)
            ->whereDate('business_day', '<=', $toDate)
            ->selectRaw('branch_id, SUM(total_deposit_amount) actual, COUNT(DISTINCT business_day) days')
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        $targets = BranchDepositPlan::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->pluck('daily_target_amount', 'branch_id')
            ->map(fn ($v) => (float) $v);

        return Branch::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('id', $branchIds))
            ->with(['district', 'bankingType'])
            ->orderBy('name')
            ->get()
            ->map(function (Branch $branch) use ($actual, $targets) {
                $row = $actual->get($branch->id);
                $days = (int) ($row->days ?? 0);
                $dailyTarget = (float) ($targets[$branch->id] ?? 0);
                $achieved = (float) ($row->actual ?? 0);
                $achievable = $dailyTarget * $days;

                return [
                    'branch_id' => $branch->id,
                    'branch' => $branch->name,
                    'district' => $branch->district?->name ?? '-',
                    'banking_type' => $branch->bankingType?->name ?? '-',
                    'days' => $days,
                    'actual' => $achieved,
                    'daily_target' => $dailyTarget,
                    'achievable' => $achievable,
                    'variance' => $achieved - $achievable,
                    'achievement' => $achievable > 0 ? ($achieved / $achievable) * 100 : null,
                ];
            });
    }

    /**
 * * Account balance + flow totals on the most recent reported day.
     *
     * @param  array<int, int>|null  $branchIds
     * @return array<string, float|int|string|null>
     */
    public static function accountSnapshot(?array $branchIds = null): array
    {
        $latest = DailyAccountPerformance::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->max('business_day');

        if (! $latest) {
            return ['day' => null, 'total' => 0, 'active' => 0, 'dormant' => 0, 'new' => 0, 'reactivated' => 0, 'active_rate' => 0.0];
        }

        // max('business_day') can come back as a full "YYYY-MM-DD HH:MM:SS"
        // string, but whereDate() compares the column's date part against the
        // binding verbatim. On SQLite that yields '2026-09-30' = '2026-09-30
        // 00:00:00', which is false, and the snapshot silently totals zero.
        // Normalise the binding to a plain date.
        $latestDay = Carbon::parse($latest)->toDateString();

        $row = DailyAccountPerformance::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereDate('business_day', $latestDay)
            ->selectRaw('COALESCE(SUM(total_accounts),0) t, COALESCE(SUM(active_accounts),0) a, COALESCE(SUM(dormant_accounts),0) d, COALESCE(SUM(new_accounts),0) n, COALESCE(SUM(reactivated_accounts),0) r')
            ->first();

        $total = (float) $row->t;

        return [
            'day' => $latest,
            'total' => $total,
            'active' => (float) $row->a,
            'dormant' => (float) $row->d,
            'new' => (float) $row->n,
            'reactivated' => (float) $row->r,
            'active_rate' => $total > 0 ? ((float) $row->a / $total) * 100 : 0.0,
        ];
    }

    /**
     * Account targets summed across branches.
     *
     * @param  array<int, int>|null  $branchIds
     * @return array<string, float>
     */
    public static function accountTargets(?array $branchIds = null): array
    {
        return [
            'daily' => (float) BranchAccountPlan::query()
                ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
                ->sum('daily_target_accounts'),
            'annual' => (float) BranchAccountPlan::query()
                ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
                ->sum('annual_target_accounts'),
        ];
    }

    /**
     * Cumulative account flows over the whole window.
     *
     * Balances are point-in-time, but new/reactivated are per-day flows, so they
     * are summed rather than taking the latest value.
     *
     * @param  array<int, int>|null  $branchIds
     * @return array<string, float>
     */
    public static function accountFlowTotals(?array $branchIds = null): array
    {
        $row = DailyAccountPerformance::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereDate('business_day', '>=', self::windowStart()->toDateString())
            ->whereDate('business_day', '<=', Carbon::yesterday()->toDateString())
            ->selectRaw('COALESCE(SUM(new_accounts),0) n, COALESCE(SUM(reactivated_accounts),0) r')
            ->first();

        return [
            'new' => (float) $row->n,
            'reactivated' => (float) $row->r,
        ];
    }

    /**
     * Percentage change between the last two reported days.
     *
     * @param  array<int, int>|null  $branchIds
     * @return array{percent: float, previous: float, current: float}|null
     */
    public static function dayOnDayChange(?array $branchIds = null): ?array
    {
        $series = DailyDepositPerformance::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->selectRaw('DATE(business_day) d, SUM(total_deposit_amount) t')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('t', 'd')
            ->map(fn ($v) => (float) $v)
            ->all();

        if (count($series) < 2) {
            return null;
        }

        $values = array_values($series);
        $current = (float) end($values);
        $previous = (float) $values[count($values) - 2];

        return [
            'current' => $current,
            'previous' => $previous,
            'percent' => $previous != 0.0 ? (($current - $previous) / abs($previous)) * 100 : 0.0,
        ];
    }

    /**
     * Build a consistent label series for the chart x-axis.
     *
     * @param  array<int, int>|null  $branchIds
     * @return array{labels: array<int, string>, values: array<int, float>, dates: array<int, string>}
     */
    public static function alignSeries(?BaseCarbon $from = null, ?BaseCarbon $to = null, ?array $branchIds = null): array
    {
        $from ??= self::windowStart();
        $to ??= Carbon::yesterday();

        $series = self::dailyDepositSeries($from, $to, $branchIds);

        $labels = [];
        $values = [];
        $dates = [];

        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            $key = $day->toDateString();
            $dates[] = $key;
            $labels[] = $day->format('M d');
            $values[] = $series[$key] ?? 0.0;
        }

return ['labels' => $labels, 'values' => $values, 'dates' => $dates];
    }

    /**
     * Generic align series for any KPI model.
     *
     * @param  class-string  $model
     * @param  string  $amountColumn
     * @param  array<int, int>|null  $branchIds
     * @return array{labels: array<int, string>, values: array<int, float>, dates: array<int, string>}
     */
    public static function alignKpiSeries(string $model, string $amountColumn, ?BaseCarbon $from = null, ?BaseCarbon $to = null, ?array $branchIds = null): array
    {
        $from ??= self::windowStart();
        $to ??= Carbon::yesterday();

        $series = self::dailyKpiSeries($model, $amountColumn, $from, $to, $branchIds);

        $labels = [];
        $values = [];
        $dates = [];

        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            $key = $day->toDateString();
            $dates[] = $key;
            $labels[] = $day->format('M d');
            $values[] = $series[$key] ?? 0.0;
        }

        return ['labels' => $labels, 'values' => $values, 'dates' => $dates];
    }

    /**
     * District-level performance aggregation.
     *
     * @param  array<int, int>|null  $branchIds
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public static function districtPerformance(?BaseCarbon $from = null, ?BaseCarbon $to = null, ?array $branchIds = null)
    {
        $from ??= self::windowStart();
        $to ??= Carbon::yesterday();
        $fromDate = $from->toDateString();
        $toDate = $to->toDateString();

        return DB::table('daily_deposit_performances as ddp')
            ->join('branches as b', 'b.id', '=', 'ddp.branch_id')
            ->join('districts as dist', 'dist.id', '=', 'b.district_id')
            ->when($branchIds !== null, fn ($q) => $q->whereIn('ddp.branch_id', $branchIds))
            ->whereDate('ddp.business_day', '>=', $fromDate)
            ->whereDate('ddp.business_day', '<=', $toDate)
            ->selectRaw('dist.id as district_id, dist.name as district, SUM(ddp.total_deposit_amount) as total, COUNT(DISTINCT ddp.branch_id) as branches, COUNT(DISTINCT ddp.business_day) as days')
            ->groupBy('dist.id', 'dist.name')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                return [
                    'district_id' => $row->district_id,
                    'district' => $row->district,
                    'total' => (float) $row->total,
                    'branches' => (int) $row->branches,
                    'days' => (int) $row->days,
                    'avg_daily' => $row->days > 0 ? (float) $row->total / $row->days : 0,
                ];
            });
    }

    /**
     * Generic day-on-day change for any KPI model.
     *
     * @param  class-string  $model
     * @param  string  $amountColumn
     * @param  array<int, int>|null  $branchIds
     * @return array{percent: float, previous: float, current: float}|null
     */
    public static function kpiDayOnDayChange(string $model, string $amountColumn, ?array $branchIds = null): ?array
    {
        $series = $model::query()
            ->when($branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->selectRaw('DATE(business_day) d, SUM(' . $amountColumn . ') t')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('t', 'd')
            ->map(fn ($v) => (float) $v)
            ->all();

        if (count($series) < 2) {
            return null;
        }

        $values = array_values($series);
        $current = (float) end($values);
        $previous = (float) $values[count($values) - 2];

        return [
            'current' => $current,
            'previous' => $previous,
            'percent' => $previous != 0.0 ? (($current - $previous) / abs($previous)) * 100 : 0.0,
        ];
    }

    /**
     * Heatmap data for calendar view.
     *
     * @param  class-string  $model
     * @param  string  $amountColumn
     * @param  array<int, int>|null  $branchIds
     * @return array<int, array{date: string, value: float, intensity: float}>
     */
    public static function heatmapData(string $model, string $amountColumn, ?BaseCarbon $from = null, ?BaseCarbon $to = null, ?array $branchIds = null): array
    {
        $from ??= self::windowStart();
        $to ??= Carbon::yesterday();

        $series = self::dailyKpiSeries($model, $amountColumn, $from, $to, $branchIds);

        if (empty($series)) {
            return [];
        }

        $maxValue = max(array_values($series));
        $minValue = min(array_values($series));

        $data = [];
        foreach ($series as $date => $value) {
            $intensity = $maxValue > 0 ? (($value - $minValue) / ($maxValue - $minValue)) * 100 : 0;
            $data[] = [
                'date' => $date,
                'value' => (float) $value,
                'intensity' => round($intensity, 1),
            ];
        }

        return $data;
    }
}

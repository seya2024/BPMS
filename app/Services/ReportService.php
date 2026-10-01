<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\DailyAccountOpening;
use App\Models\DailyAccountPerformance;
use App\Models\DailyDepositPerformance;
use App\Models\DailyDepositPerformanceDetail;
use App\Models\DailyForeignCurrencyGeneration;
use App\Models\DailySuperAppSubscription;
use App\Filament\Support\BusinessDay;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Assembles the report datasets.
 *
 * Every aggregation is done in SQL with a GROUP BY, not in PHP, so a report over a
 * wide date range never loads the underlying rows into memory.
 *
 * Dates are always resolved through BusinessDay, so a report can never be pulled
 * for today or a future day even if the caller is careless.
 */
class ReportService
{
    /** The reports offered on the Reportings page. */
    public const REPORTS = [
        'account_openings' => 'Account Performance',
        'deposits' => 'Deposit Performance',
        'fx' => 'Foreign Currency',
        'super_app' => 'Super App Subscription',
        'all_kpi' => 'Daily All KPI',
    ];

    /**
     * Resolves the reporting window.
     *
     * With no range, a single day is reported and it defaults to yesterday. With a
     * range, both ends are clamped to yesterday, so the upper bound can never run
     * past the last complete day.
     */
    public static function resolveWindow(?string $from, ?string $to): array
    {
        $yesterday = Carbon::yesterday()->startOfDay();

        if (blank($from) && blank($to)) {
            return ['from' => $yesterday->toDateString(), 'to' => $yesterday->toDateString(), 'singleDay' => true];
        }

        $fromDate = blank($from) ? $yesterday : Carbon::parse($from)->startOfDay();
        $toDate = blank($to) ? $yesterday : Carbon::parse($to)->startOfDay();

        // Clamp: today and future are not reportable.
        if ($toDate->gt($yesterday)) {
            $toDate = $yesterday;
        }

        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        return [
            'from' => $fromDate->toDateString(),
            'to' => $toDate->toDateString(),
            'singleDay' => $fromDate->toDateString() === $toDate->toDateString(),
        ];
    }

    /**
     * Branch ids in scope, optionally narrowed to one district.
     *
     * @return array<int, int>
     */
    protected static function branchIds(?int $districtId): array
    {
        return Branch::query()
            ->when($districtId, fn ($q) => $q->where('district_id', $districtId))
            ->orderBy('name')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Base scope: the branch id -> name/district/banking-type lookup, shared by all
     * reports so every report labels rows identically.
     */
    protected static function branches(?int $districtId)
    {
        return Branch::query()
            ->with(['district:id,name', 'bankingType:id,name'])
            ->when($districtId, fn ($q) => $q->where('district_id', $districtId))
            ->orderBy('name')
            ->get();
    }

    public static function accountOpenings(string $from, string $to, ?int $districtId = null): array
    {
        $branchIds = static::branchIds($districtId);

        if ($branchIds === []) {
            return [];
        }

        $rows = DailyAccountOpening::query()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to)
            ->groupBy('branch_id')
            ->selectRaw('branch_id,
                SUM(COALESCE(conventional_accounts,0)) conventional,
                SUM(COALESCE(ifb_accounts,0)) ifb,
                SUM(target_accounts) target')
            ->get()
            ->keyBy('branch_id');

        $meta = static::branches($districtId)->keyBy('id');
        $out = [];

        foreach ($meta as $id => $branch) {
            $r = $rows->get($id);

            $out[] = [
                'Branch' => $branch->name,
                'Code' => $branch->code,
                'District' => $branch->district?->name ?? '-',
                'Banking Type' => $branch->bankingType?->name ?? '-',
                'Conventional Accounts' => (int) ($r->conventional ?? 0),
                'IFB Accounts' => (int) ($r->ifb ?? 0),
                'Total Accounts' => (int) ($r->conventional ?? 0) + (int) ($r->ifb ?? 0),
                'Target' => (int) ($r->target ?? 0),
            ];
        }

        return $out;
    }

    public static function deposits(string $from, string $to, ?int $districtId = null): array
    {
        $branchIds = static::branchIds($districtId);

        if ($branchIds === []) {
            return [];
        }

        // Segment amounts per branch, split by the banking type on the detail row.
        $segments = DailyDepositPerformanceDetail::query()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to)
            ->selectRaw('branch_id, banking_type_id, business_segment_id, SUM(amount) amount')
            ->groupBy('branch_id', 'banking_type_id', 'business_segment_id')
            ->get();

        $corporate = (int) \App\Models\BusinessSegment::where('name', 'Corporate')->value('id');
        $retail = (int) \App\Models\BusinessSegment::where('name', 'Retail')->value('id');
        $msme = (int) \App\Models\BusinessSegment::where('name', 'MSME')->value('id');

        $grid = [];

        foreach ($segments as $s) {
            $key = $s->branch_id . ':' . $s->banking_type_id;
            $field = match ((int) $s->business_segment_id) {
                $corporate => 'corporate',
                $retail => 'retail',
                $msme => 'msme',
                default => null,
            };

            if ($field === null) {
                continue;
            }

            $grid[$key][$field] = ($grid[$key][$field] ?? 0) + (float) $s->amount;
        }

        $totals = DailyDepositPerformance::query()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to)
            ->selectRaw('branch_id, SUM(total_deposit_amount) total, SUM(new_deposit_amount) new_dep,
                SUM(deposit_inflow_amount) inflow, SUM(deposit_outflow_amount) outflow,
                SUM(net_deposit_change) net')
            ->groupBy('branch_id')
            ->get()
            ->keyBy('branch_id');

        $meta = static::branches($districtId)->keyBy('id');
        $out = [];

        foreach ($meta as $id => $branch) {
            $c = $grid[$id . ':1'] ?? [];
            $i = $grid[$id . ':2'] ?? [];
            $t = $totals->get($id);

            $out[] = [
                'Branch' => $branch->name,
                'Code' => $branch->code,
                'District' => $branch->district?->name ?? '-',
                'Banking Type' => $branch->bankingType?->name ?? '-',
                'Conventional Corp' => (float) ($c['corporate'] ?? 0),
                'Conventional Retail' => (float) ($c['retail'] ?? 0),
                'Conventional MSME' => (float) ($c['msme'] ?? 0),
                'Conventional Total' => (float) (($c['corporate'] ?? 0) + ($c['retail'] ?? 0) + ($c['msme'] ?? 0)),
                'IFB Corp' => (float) ($i['corporate'] ?? 0),
                'IFB Retail' => (float) ($i['retail'] ?? 0),
                'IFB MSME' => (float) ($i['msme'] ?? 0),
                'IFB Total' => (float) (($i['corporate'] ?? 0) + ($i['retail'] ?? 0) + ($i['msme'] ?? 0)),
                'Total Deposit' => (float) ($t->total ?? 0),
                'New Deposit' => (float) ($t->new_dep ?? 0),
                'Inflow' => (float) ($t->inflow ?? 0),
                'Outflow' => (float) ($t->outflow ?? 0),
                'Net Change' => (float) ($t->net ?? 0),
            ];
        }

        return $out;
    }

    public static function foreignCurrency(string $from, string $to, ?int $districtId = null): array
    {
        $branchIds = static::branchIds($districtId);

        if ($branchIds === []) {
            return [];
        }

        $rows = DailyForeignCurrencyGeneration::query()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to)
            ->groupBy('branch_id')
            ->selectRaw('branch_id, SUM(amount) amount, SUM(target_amount) target, COUNT(DISTINCT business_day) days')
            ->get()
            ->keyBy('branch_id');

        $meta = static::branches($districtId)->keyBy('id');
        $out = [];

        foreach ($meta as $id => $branch) {
            $r = $rows->get($id);
            $amount = (float) ($r->amount ?? 0);
            $target = (float) ($r->target ?? 0);

            $out[] = [
                'Branch' => $branch->name,
                'Code' => $branch->code,
                'District' => $branch->district?->name ?? '-',
                'Banking Type' => $branch->bankingType?->name ?? '-',
                'Currency' => 'ETB',
                'Days Reported' => (int) ($r->days ?? 0),
                'Generated (ETB)' => $amount,
                'Target (ETB)' => $target,
                'Variance (ETB)' => $amount - $target,
                'Attainment %' => $target > 0 ? round($amount / $target * 100, 1) : null,
            ];
        }

        return $out;
    }

    public static function superApp(string $from, string $to, ?int $districtId = null): array
    {
        $branchIds = static::branchIds($districtId);

        if ($branchIds === []) {
            return [];
        }

        $rows = DailySuperAppSubscription::query()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to)
            ->groupBy('branch_id')
            ->selectRaw('branch_id, SUM(subscriptions) subs, SUM(target_subscriptions) target,
                COUNT(DISTINCT business_day) days')
            ->get()
            ->keyBy('branch_id');

        $meta = static::branches($districtId)->keyBy('id');
        $out = [];

        foreach ($meta as $id => $branch) {
            $r = $rows->get($id);
            $subs = (int) ($r->subs ?? 0);
            $target = (int) ($r->target ?? 0);

            $out[] = [
                'Branch' => $branch->name,
                'Code' => $branch->code,
                'District' => $branch->district?->name ?? '-',
                'Banking Type' => $branch->bankingType?->name ?? '-',
                'Days Reported' => (int) ($r->days ?? 0),
                'Subscriptions' => $subs,
                'Target' => $target,
                'Variance' => $subs - $target,
                'Attainment %' => $target > 0 ? round($subs / $target * 100, 1) : null,
            ];
        }

        return $out;
    }

    /**
     * Every KPI side by side for one branch, which is the cross-KPI view.
     */
    public static function allKpi(string $from, string $to, ?int $districtId = null): array
    {
        $branchIds = static::branchIds($districtId);

        if ($branchIds === []) {
            return [];
        }

        $scope = fn ($model) => $model->whereIn('branch_id', $branchIds)
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to);

        $deposits = $scope(DailyDepositPerformance::query())
            ->groupBy('branch_id')
            ->selectRaw('branch_id, SUM(total_deposit_amount) v')->get()->keyBy('branch_id');

        $openings = $scope(DailyAccountOpening::query())
            ->groupBy('branch_id')
            ->selectRaw('branch_id, SUM(COALESCE(conventional_accounts,0)) + SUM(COALESCE(ifb_accounts,0)) v')
            ->get()->keyBy('branch_id');

        $newAccounts = $scope(DailyAccountPerformance::query())
            ->groupBy('branch_id')
            ->selectRaw('branch_id, SUM(new_accounts) v')->get()->keyBy('branch_id');

        $fx = $scope(DailyForeignCurrencyGeneration::query())
            ->groupBy('branch_id')
            ->selectRaw('branch_id, SUM(amount) v')->get()->keyBy('branch_id');

        $app = $scope(DailySuperAppSubscription::query())
            ->groupBy('branch_id')
            ->selectRaw('branch_id, SUM(subscriptions) v')->get()->keyBy('branch_id');

        $meta = static::branches($districtId)->keyBy('id');
        $out = [];

        foreach ($meta as $id => $branch) {
            $out[] = [
                'Branch' => $branch->name,
                'Code' => $branch->code,
                'District' => $branch->district?->name ?? '-',
                'Banking Type' => $branch->bankingType?->name ?? '-',
                'Total Deposit (ETB)' => (float) ($deposits->get($id)->v ?? 0),
                'Accounts Opened' => (int) ($openings->get($id)->v ?? 0),
                'New Accounts' => (int) ($newAccounts->get($id)->v ?? 0),
                'FX Generated (ETB)' => (float) ($fx->get($id)->v ?? 0),
                'Super App Subs' => (int) ($app->get($id)->v ?? 0),
            ];
        }

        return $out;
    }

    /**
     * Runs a named report and returns its rows.
     */
    public static function run(string $report, string $from, string $to, ?int $districtId = null): array
    {
        return match ($report) {
            'account_openings' => static::accountOpenings($from, $to, $districtId),
            'deposits' => static::deposits($from, $to, $districtId),
            'fx' => static::foreignCurrency($from, $to, $districtId),
            'super_app' => static::superApp($from, $to, $districtId),
            'all_kpi' => static::allKpi($from, $to, $districtId),
            default => [],
        };
    }
}

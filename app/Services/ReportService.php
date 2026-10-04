<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\DailyAccountOpening;
use App\Models\DailyAccountPerformance;
use App\Models\DailyDepositPerformance;
use App\Models\DailyForeignCurrencyGeneration;
use App\Models\DailySuperAppSubscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /* ============================================================
       REPORTS — loaded from k_p_i_categories
       ============================================================ */

    public static function reports(): array
    {
        $out = ['all_kpi' => 'All KPIs'];
        try {
            $categories = DB::table('k_p_i_categories')->orderBy('id')->get();
            foreach ($categories as $cat) {
                $out['cat_' . $cat->id] = $cat->name;
            }
        } catch (\Throwable $e) {}
        return $out;
    }

    public static function allReports(): array
    {
        return static::reports();
    }

    /* ============================================================
       Window
       ============================================================ */

    public static function resolveWindow(?string $from, ?string $to): array
    {
        $yesterday = Carbon::yesterday()->startOfDay();

        if (blank($from) && blank($to)) {
            return ['from' => $yesterday->toDateString(), 'to' => $yesterday->toDateString(), 'singleDay' => true];
        }

        $fromDate = blank($from) ? $yesterday : Carbon::parse($from)->startOfDay();
        $toDate = blank($to) ? $yesterday : Carbon::parse($to)->startOfDay();

        if ($toDate->gt($yesterday)) $toDate = $yesterday;
        if ($fromDate->gt($toDate)) [$fromDate, $toDate] = [$toDate, $fromDate];

        return [
            'from' => $fromDate->toDateString(),
            'to' => $toDate->toDateString(),
            'singleDay' => $fromDate->toDateString() === $toDate->toDateString(),
        ];
    }

    /* ============================================================
       Shared helpers — now accept district + branch + banking type
       ============================================================ */

    protected static function branchIds(
        ?int $districtId,
        ?int $branchId = null,
        ?int $bankingTypeId = null
    ): array {
        return Branch::query()
            ->when($districtId,    fn ($q) => $q->where('district_id', $districtId))
            ->when($branchId,      fn ($q) => $q->where('id', $branchId))
            ->when($bankingTypeId, fn ($q) => $q->where('bankingType_id', $bankingTypeId))
            ->orderBy('name')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    protected static function branches(
        ?int $districtId,
        ?int $branchId = null,
        ?int $bankingTypeId = null
    ) {
        return Branch::query()
            ->with(['district:id,name', 'bankingType:id,name'])
            ->when($districtId,    fn ($q) => $q->where('district_id', $districtId))
            ->when($branchId,      fn ($q) => $q->where('id', $branchId))
            ->when($bankingTypeId, fn ($q) => $q->where('bankingType_id', $bankingTypeId))
            ->orderBy('name')
            ->get();
    }

    protected static function kpiDefinitions(): array
    {
        return [
            'DEPOSIT' => ['model' => DailyDepositPerformance::class, 'column' => 'total_deposit_amount', 'aggregate' => 'SUM', 'cast' => 'float'],
            'NEW ACCOUNTS' => ['model' => DailyAccountPerformance::class, 'column' => 'new_accounts', 'aggregate' => 'SUM', 'cast' => 'int'],
            'FCY' => ['model' => DailyForeignCurrencyGeneration::class, 'column' => 'amount', 'aggregate' => 'SUM', 'cast' => 'float'],
            'CARD SUBSCRIPTION' => ['model' => null, 'column' => null, 'aggregate' => 'SUM', 'cast' => 'int'],
            'POS SUBSCRIPTION' => ['model' => null, 'column' => null, 'aggregate' => 'SUM', 'cast' => 'int'],
            'SUPER APP SUBSCRIPTION' => ['model' => DailySuperAppSubscription::class, 'column' => 'subscriptions', 'aggregate' => 'SUM', 'cast' => 'int'],
            'ACCOUNT ACTIVATION' => ['model' => DailyAccountOpening::class, 'column' => null, 'aggregate' => 'SUM', 'cast' => 'int', 'raw' => 'SUM(COALESCE(conventional_accounts,0)) + SUM(COALESCE(ifb_accounts,0))'],
            'CARD ACTIVATION' => ['model' => null, 'column' => null, 'aggregate' => 'SUM', 'cast' => 'int'],
            'SUPER APP ACTIVATION' => ['model' => null, 'column' => null, 'aggregate' => 'SUM', 'cast' => 'int'],
            'SERVICE QUALITY' => ['model' => null, 'column' => null, 'aggregate' => 'AVG', 'cast' => 'float'],
            'ATM TRANSACTION' => ['model' => null, 'column' => null, 'aggregate' => 'SUM', 'cast' => 'int'],
            'POS TRANSACTION' => ['model' => null, 'column' => null, 'aggregate' => 'SUM', 'cast' => 'int'],
        ];
    }

    /* ============================================================
       Legacy individual reports
       ============================================================ */

    public static function accountOpenings(
        string $from,
        string $to,
        ?int $districtId = null,
        ?int $branchId = null,
        ?int $bankingTypeId = null
    ): array {
        $branchIds = static::branchIds($districtId, $branchId, $bankingTypeId);
        if ($branchIds === []) return [];

        $rows = DailyAccountOpening::query()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to)
            ->groupBy('branch_id')
            ->selectRaw('branch_id, SUM(COALESCE(conventional_accounts,0)) conventional, SUM(COALESCE(ifb_accounts,0)) ifb, SUM(target_accounts) target')
            ->get()->keyBy('branch_id');

        $meta = static::branches($districtId, $branchId, $bankingTypeId)->keyBy('id');
        $out = [];
        foreach ($meta as $id => $branch) {
            $r = $rows->get($id);
            $out[] = [
                'District' => $branch->district?->name ?? '-',
                'Branch' => $branch->name,
                'Banking Type' => $branch->bankingType?->name ?? '-',
                'Conventional Accounts' => (int) ($r->conventional ?? 0),
                'IFB Accounts' => (int) ($r->ifb ?? 0),
                'Total Accounts' => (int) ($r->conventional ?? 0) + (int) ($r->ifb ?? 0),
                'Target' => (int) ($r->target ?? 0),
            ];
        }
        return $out;
    }

    public static function deposits(
        string $from,
        string $to,
        ?int $districtId = null,
        ?int $branchId = null,
        ?int $bankingTypeId = null
    ): array {
        $branchIds = static::branchIds($districtId, $branchId, $bankingTypeId);
        if ($branchIds === []) return [];

        $totals = DailyDepositPerformance::query()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to)
            ->groupBy('branch_id')
            ->selectRaw('branch_id, SUM(total_deposit_amount) total, SUM(new_deposit_amount) new_dep, SUM(deposit_inflow_amount) inflow, SUM(deposit_outflow_amount) outflow, SUM(net_deposit_change) net')
            ->get()->keyBy('branch_id');

        $meta = static::branches($districtId, $branchId, $bankingTypeId)->keyBy('id');
        $out = [];
        foreach ($meta as $id => $branch) {
            $t = $totals->get($id);
            $out[] = [
                'District' => $branch->district?->name ?? '-',
                'Branch' => $branch->name,
                'Banking Type' => $branch->bankingType?->name ?? '-',
                'Total Deposit' => (float) ($t->total ?? 0),
                'New Deposit' => (float) ($t->new_dep ?? 0),
                'Inflow' => (float) ($t->inflow ?? 0),
                'Outflow' => (float) ($t->outflow ?? 0),
                'Net Change' => (float) ($t->net ?? 0),
            ];
        }
        return $out;
    }

    public static function foreignCurrency(
        string $from,
        string $to,
        ?int $districtId = null,
        ?int $branchId = null,
        ?int $bankingTypeId = null
    ): array {
        $branchIds = static::branchIds($districtId, $branchId, $bankingTypeId);
        if ($branchIds === []) return [];

        $rows = DailyForeignCurrencyGeneration::query()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to)
            ->groupBy('branch_id')
            ->selectRaw('branch_id, SUM(amount) amount, SUM(target_amount) target, COUNT(DISTINCT business_day) days')
            ->get()->keyBy('branch_id');

        $meta = static::branches($districtId, $branchId, $bankingTypeId)->keyBy('id');
        $out = [];
        foreach ($meta as $id => $branch) {
            $r = $rows->get($id);
            $amount = (float) ($r->amount ?? 0);
            $target = (float) ($r->target ?? 0);
            $out[] = [
                'District' => $branch->district?->name ?? '-',
                'Branch' => $branch->name,
                'Banking Type' => $branch->bankingType?->name ?? '-',
                'Days Reported' => (int) ($r->days ?? 0),
                'Generated (ETB)' => $amount,
                'Target (ETB)' => $target,
                'Variance (ETB)' => $amount - $target,
                'Attainment %' => $target > 0 ? round($amount / $target * 100, 1) : null,
            ];
        }
        return $out;
    }

    public static function superApp(
        string $from,
        string $to,
        ?int $districtId = null,
        ?int $branchId = null,
        ?int $bankingTypeId = null
    ): array {
        $branchIds = static::branchIds($districtId, $branchId, $bankingTypeId);
        if ($branchIds === []) return [];

        $rows = DailySuperAppSubscription::query()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('business_day', '>=', $from)
            ->whereDate('business_day', '<=', $to)
            ->groupBy('branch_id')
            ->selectRaw('branch_id, SUM(subscriptions) subs, SUM(target_subscriptions) target, COUNT(DISTINCT business_day) days')
            ->get()->keyBy('branch_id');

        $meta = static::branches($districtId, $branchId, $bankingTypeId)->keyBy('id');
        $out = [];
        foreach ($meta as $id => $branch) {
            $r = $rows->get($id);
            $subs = (int) ($r->subs ?? 0);
            $target = (int) ($r->target ?? 0);
            $out[] = [
                'District' => $branch->district?->name ?? '-',
                'Branch' => $branch->name,
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

    /* ============================================================
       Main KPI report
       ============================================================ */

    public static function allKpi(
        string $from,
        string $to,
        ?int $districtId = null,
        ?int $categoryId = null,
        ?int $branchId = null,
        ?int $bankingTypeId = null
    ): array {
        $branchIds = static::branchIds($districtId, $branchId, $bankingTypeId);
        if ($branchIds === []) return [];

        // 1. Load KPI names
        $kpiQuery = DB::table('k_p_i_s')->orderBy('id');
        if ($categoryId !== null) $kpiQuery->where('category_id', $categoryId);
        $kpiNames = $kpiQuery->pluck('name')->all();
        if (empty($kpiNames)) return [];

        // 2. Fetch values
        $allDefs = static::kpiDefinitions();
        $kpiValues = [];
        foreach ($kpiNames as $kpiName) {
            $def = $allDefs[$kpiName] ?? null;
            if (!$def || empty($def['model'])) {
                $kpiValues[$kpiName] = collect();
                continue;
            }
            $query = $def['model']::query()
                ->whereIn('branch_id', $branchIds)
                ->whereDate('business_day', '>=', $from)
                ->whereDate('business_day', '<=', $to)
                ->groupBy('branch_id');

            if (!empty($def['raw'])) {
                $query->selectRaw("branch_id, {$def['raw']} v");
            } else {
                $query->selectRaw("branch_id, {$def['aggregate']}({$def['column']}) v");
            }
            $kpiValues[$kpiName] = $query->get()->keyBy('branch_id');
        }

        // 3. Build rows
        $meta = static::branches($districtId, $branchId, $bankingTypeId)->keyBy('id');
        $out = [];
        foreach ($meta as $id => $branch) {
            $row = [
                'District'     => $branch->district?->name ?? '-',
                'Branch'       => $branch->name,
                'Banking Type' => $branch->bankingType?->name ?? '-',
            ];
            foreach ($kpiNames as $kpiName) {
                $def = $allDefs[$kpiName] ?? null;
                $collection = $kpiValues[$kpiName] ?? collect();
                $record = $collection->get($id);
                $value = (float) ($record->v ?? 0);
                $row[$kpiName] = ($def['cast'] ?? 'float') === 'int' ? (int) $value : $value;
            }
            $out[] = $row;
        }

        return $out;
    }

    /* ============================================================
       Dispatcher — now forwards branch + banking type filters
       ============================================================ */

    public static function run(
        string $report,
        string $from,
        string $to,
        ?int $districtId = null,
        ?int $branchId = null,
        ?int $bankingTypeId = null
    ): array {
        if (str_starts_with($report, 'cat_')) {
            $categoryId = (int) substr($report, 4);
            if ($categoryId <= 0) return [];
            return static::allKpi($from, $to, $districtId, $categoryId, $branchId, $bankingTypeId);
        }

        return match ($report) {
            'account_openings' => static::accountOpenings($from, $to, $districtId, $branchId, $bankingTypeId),
            'deposits'         => static::deposits($from, $to, $districtId, $branchId, $bankingTypeId),
            'fx'               => static::foreignCurrency($from, $to, $districtId, $branchId, $bankingTypeId),
            'super_app'        => static::superApp($from, $to, $districtId, $branchId, $bankingTypeId),
            'all_kpi'          => static::allKpi($from, $to, $districtId, null, $branchId, $bankingTypeId),
            default            => [],
        };
    }
}
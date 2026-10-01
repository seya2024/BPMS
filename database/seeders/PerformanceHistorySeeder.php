<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchAccountPlan;
use App\Models\BranchDepositPlan;
use App\Models\BusinessSegment;
use App\Models\DailyAccountPerformance;
use App\Models\DailyDepositPerformance;
use App\Models\DailyDepositPerformanceDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Generates ~45 days of realistic daily performance history so trend, moving
 * average and target-attainment analytics have something to plot.
 *
 * Idempotent and non-destructive: only writes rows for the generated window and
 * never truncates anything outside it.
 */
class PerformanceHistorySeeder extends Seeder
{
    private const DAYS = 45;

    public function run(): void
    {
        $branches = Branch::query()->orderBy('name')->get();

        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Run TestDataSeeder first.');

            return;
        }

        $segmentIds = BusinessSegment::query()
            ->whereIn('name', ['Corporate', 'Retail', 'MSME'])
            ->pluck('id', 'name');

        if ($segmentIds->count() < 3) {
            $this->command->warn('Expected Corporate, Retail and MSME segments.');

            return;
        }

        $today = Carbon::yesterday();
        $firstDay = $today->copy()->subDays(self::DAYS - 1);

        // Anchor generated volumes to each branch's own daily target so achievement
        // lands in a believable band instead of orders of magnitude away.
        $dailyTargets = BranchDepositPlan::query()
            ->pluck('daily_target_amount', 'branch_id')
            ->map(fn ($v) => (float) $v);

        $dailyAccountTargets = BranchAccountPlan::query()
            ->pluck('daily_target_accounts', 'branch_id')
            ->map(fn ($v) => (int) $v);

        $written = 0;

        DB::transaction(function () use ($branches, $segmentIds, $today, $firstDay, $dailyTargets, $dailyAccountTargets, &$written): void {
            foreach ($branches as $branch) {
                $bankingTypeId = (int) $branch->bankingType_id;

                // Each branch gets a stable baseline + weekly seasonality so charts
                // look organic rather than like random noise.
                $seed = (int) $branch->id * 7919;

                // Target attainment varies per branch (some over, some under) so the
                // leaderboard and below-target widgets have a real spread.
                $attainment = 0.72 + ((($seed / 100) % 60) / 100); // 0.72 - 1.31

                $target = (float) ($dailyTargets[$branch->id] ?? 0);

                // Fall back to a plausible target if no plan exists for this branch.
                if ($target <= 0) {
                    $target = 2_000 + ($seed % 20_000);
                }

                $base = $target * $attainment;

                // Corporate-weighted profile per banking type.
                $mixes = [
                    1 => ['Corporate' => 0.40, 'Retail' => 0.33, 'MSME' => 0.27],
                    2 => ['Corporate' => 0.22, 'Retail' => 0.48, 'MSME' => 0.30],
                ];

                // A branch reports ONLY its own banking type, matching
                // branches.bankingType_id and the disjoint batch sections. The
                // whole target therefore lands on that single type, so attainment
                // percentages stay meaningful.
                $typeWeights = [$bankingTypeId => 1.0];

                $runningAccountBase = 4_000 + ($seed % 12_000);

                for ($i = 0; $i < self::DAYS; $i++) {
                    $day = $firstDay->copy()->addDays($i);

                    $weekday = (int) $day->dayOfWeek;
                    $weekendFactor = ($weekday === 0 || $weekday === 6) ? 0.35 : 1.0;

                    // Deterministic pseudo-random wobble in the range 0.65 - 1.35.
                    $noise = 1 + (((($i * 31) + $seed) % 70) / 100);
                    $growth = 1 + ($i * 0.004);

                    $dayBase = $base * $weekendFactor * $noise * $growth;
                    $dayBase = max(0, round($dayBase, 2));

                    // Amounts per banking type, then summed into the branch total.
                    $amountsByType = [];
                    $total = 0.0;

                    foreach ($typeWeights as $typeId => $weight) {
                        $typeBase = max(0, round($dayBase * $weight, 2));

                        foreach ($mixes[$typeId] as $segmentName => $share) {
                            $variance = 1 + (((($i * 13) + $seed + $typeId + strlen($segmentName)) % 25) / 100);
                            $amount = round($typeBase * $share * $variance, 2);

                            $amountsByType[$typeId][$segmentName] = $amount;
                            $total += $amount;
                        }
                    }

                    $total = round($total, 2);

                    $inflow = round($total * 1.18, 2);
                    $outflow = round($total * 0.92, 2);
                    $netChange = round($inflow - $outflow, 2);
                    $newDeposit = round($total * 0.14, 2);

                    $record = self::upsertForDay(
                        DailyDepositPerformance::query(),
                        $branch->id,
                        $day,
                        [
                            'total_deposit_amount' => $total,
                            'new_deposit_amount' => $newDeposit,
                            'deposit_inflow_amount' => $inflow,
                            'deposit_outflow_amount' => $outflow,
                            'net_deposit_change' => $netChange,
                            'remarks' => null,
                        ]
                    );
                    $written++;

                    // Replace this branch's segment rows for the day. The seeder
                    // owns every leg the branch reports, so clearing them all is
                    // safe here (the batch form and the row edit action clear only
                    // the single banking type they are editing).
                    DailyDepositPerformanceDetail::query()
                        ->where('branch_id', $branch->id)
                        ->whereDate('business_day', $day->toDateString())
                        ->delete();

                    foreach ($amountsByType as $typeId => $typeAmounts) {
                        foreach ($typeAmounts as $segmentName => $amount) {
                            DailyDepositPerformanceDetail::query()->create([
                                'branch_id' => $branch->id,
                                'business_day' => $day->toDateString(),
                                'banking_type_id' => $typeId,
                                'business_segment_id' => $segmentIds[$segmentName],
                                'amount' => $amount,
                                'remarks' => null,
                            ]);
                        }
                    }

                    // Account performance: balances drift slowly, flows move daily.
                    // New accounts are anchored to the branch account target too.
                    $acctTarget = (int) ($dailyAccountTargets[$branch->id] ?? 0);

                    if ($acctTarget > 0) {
                        $dailyNew = (int) max(0, round($acctTarget * $attainment * ($weekendFactor > 0.5 ? 1 : 0.2)));
                    } else {
                        $dailyNew = (int) max(0, round((($seed % 9) + 1) * ($weekendFactor > 0.5 ? 1 : 0.2)));
                    }

                    $dailyDormant = (int) max(0, round((($seed % 7) + 1) * ($weekendFactor > 0.5 ? 1 : 0.3)));
                    $reactivated = (int) max(0, round(($i % 11 === 0) ? (($seed % 5) + 1) : 0));

                    $active = (int) max(0, round($runningAccountBase * 0.78));
                    $dormantTotal = (int) max(0, round($runningAccountBase * 0.19));
                    $totalAccounts = $active + $dormantTotal;

                    self::upsertForDay(
                        DailyAccountPerformance::query(),
                        $branch->id,
                        $day,
                        [
                            'total_accounts' => $totalAccounts,
                            'active_accounts' => $active,
                            'dormant_accounts' => $dormantTotal,
                            'new_accounts' => $dailyNew,
                            'reactivated_accounts' => $reactivated,
                            'remarks' => null,
                        ]
                    );

                    // Slowly grow the account base over the window.
                    $runningAccountBase += $dailyNew - ($dailyDormant / 4);
                }
            }
        });

        $this->command->info(
            'Seeded ' . self::DAYS . ' days (' . $firstDay->toDateString()
            . ' to ' . $today->toDateString() . ') for ' . $branches->count() . ' branches.'
        );
        $this->command->info("  {$written} deposit performance rows, matching segment details, and account performance rows.");
    }

    /**
     * Insert-or-update a branch/day row in a way that is safe on both MySQL and
     * SQLite.
     *
     * The obvious `updateOrCreate(['branch_id' => .., 'business_day' => ..])`
     * is not safe here: business_day is a DATE column, but rows written by
     * another seeder (or by an earlier run) hold a "YYYY-MM-DD 00:00:00" value.
     * A bare equality test on "YYYY-MM-DD" does not match that string on SQLite,
     * so updateOrCreate concludes the row is absent and tries to INSERT, which
     * then violates the unique (branch_id, business_day) index. whereDate()
     * normalises both sides and matches reliably.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TModel>  $query
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    protected static function upsertForDay($query, int $branchId, Carbon $day, array $attributes)
    {
        $dayString = $day->toDateString();

        $model = (clone $query)
            ->where('branch_id', $branchId)
            ->whereDate('business_day', $dayString)
            ->first();

        if ($model === null) {
            $model = $query->getModel()->newInstance();
            $model->branch_id = $branchId;
            $model->business_day = $dayString;
        }

        $model->forceFill($attributes)->save();

        return $model;
    }
}

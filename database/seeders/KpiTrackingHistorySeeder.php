<?php

namespace Database\Seeders;

use App\Models\AnnualPlan;
use App\Models\Branch;
use App\Models\DailyForeignCurrencyGeneration;
use App\Models\DailySuperAppSubscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Seeds daily Super App subscription and Foreign Currency generation history.
 *
 * Volumes are derived from the district annual plan targets so attainment lands
 * in a believable band instead of being arbitrary. Idempotent: re-running
 * replaces the same branch/day rows rather than duplicating them.
 */
class KpiTrackingHistorySeeder extends Seeder
{
    private const DAYS = 45;

    public function run(): void
    {
        $branches = Branch::query()->orderBy('name')->get();

        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Run TestDataSeeder first.');

            return;
        }

        $lastDay = Carbon::yesterday();
        $firstDay = $lastDay->copy()->subDays(self::DAYS - 1);

        // District targets spread across the branches in that district.
        $planByDistrict = AnnualPlan::query()
            ->orderByDesc('id')
            ->get()
            ->keyBy('district_id');

        $branchesByDistrict = $branches->groupBy('district_id');

        $superAppRows = 0;
        $fxRows = 0;

        DB::transaction(function () use ($branches, $branchesByDistrict, $planByDistrict, $firstDay, $lastDay, &$superAppRows, &$fxRows): void {
            foreach ($branches as $branch) {
                $seed = (int) $branch->id * 7919;
                $siblings = max(1, $branchesByDistrict[$branch->district_id]->count());

                $plan = $planByDistrict[$branch->district_id] ?? null;

                // Per-branch share of the district target, plus a stable wobble so
                // branches differ instead of being uniform.
                $share = 1 / $siblings;
                $wobble = 0.85 + ((($seed / 100) % 30) / 100); // 0.85 - 1.14

                $districtSuperApp = (float) ($plan->super_app_subscriptions ?? 20000);
                $districtFx = (float) ($plan->foreign_currency_target ?? 5_000_000);

                // Daily run-rate implied by the annual target.
                $dailySuperApp = ($districtSuperApp * $share * $wobble) / 365;
                $dailyFx = ($districtFx * $share * $wobble) / 365;

                // Branch-level attainment varies so ranking widgets have spread.
                $attainment = 0.75 + ((($seed / 7) % 55) / 100); // 0.75 - 1.29

                for ($i = 0; $i < self::DAYS; $i++) {
                    $day = $firstDay->copy()->addDays($i);
                    $weekday = (int) $day->dayOfWeek;
                    $weekend = ($weekday === 0 || $weekday === 6) ? 0.30 : 1.0;

                    $growth = 1 + ($i * 0.003);
                    $noise = 1 + (((($i * 29) + $seed) % 45) / 100);

                    $scale = $weekend * $growth * $noise * $attainment;

                    $subscriptions = (int) max(0, round($dailySuperApp * $scale));
                    $amount = round($dailyFx * $scale, 2);

                    DailySuperAppSubscription::updateOrCreate(
                        [
                            'branch_id' => $branch->id,
                            'business_day' => $day->toDateString(),
                        ],
                        [
                            'subscriptions' => $subscriptions,
                            'target_subscriptions' => (int) max(0, round($dailySuperApp)),
                            'remarks' => null,
                        ]
                    );
                    $superAppRows++;

                    DailyForeignCurrencyGeneration::updateOrCreate(
                        [
                            'branch_id' => $branch->id,
                            'business_day' => $day->toDateString(),
                        ],
                        [
                            'amount' => $amount,
                            'target_amount' => round($dailyFx, 2),
                            'currency_code' => 'ETB',
                            'remarks' => null,
                        ]
                    );
                    $fxRows++;
                }
            }
        });

        $this->command->info(sprintf(
            'KPI tracking: %d super app rows + %d foreign currency rows (%s to %s).',
            $superAppRows,
            $fxRows,
            $firstDay->toDateString(),
            $lastDay->toDateString()
        ));
    }
}

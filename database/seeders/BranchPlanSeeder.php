<?php

namespace Database\Seeders;

use App\Models\AnnualAccountPlan;
use App\Models\AnnualDepositPlan;
use App\Models\Branch;
use App\Models\BranchAccountPlan;
use App\Models\BranchDepositPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Creates branch-level deposit and account plans with realistic daily / monthly /
 * quarterly / annual targets, so target-vs-actual analytics have something to
 * compare against. Idempotent: safe to re-run.
 */
class BranchPlanSeeder extends Seeder
{
    public function run(): void
    {
        $annualDepositPlan = AnnualDepositPlan::query()->first();

        if (! $annualDepositPlan) {
            $this->command->warn('No annual deposit plan found. Run TestDataSeeder first.');

            return;
        }

        $annualAccountPlan = AnnualAccountPlan::query()->first();

        // Resolve the approver by email rather than hardcoding an id, so the
        // approver is correct even after a reseed renumbers users.
        $approver = User::query()->where('email', 'seidm2031@gmail.com')->first()
            ?? User::query()->orderBy('id')->first();

        if (! $approver) {
            $this->command->warn('No user available to approve plans; approved_by will be left null.');
        }

        $today = Carbon::yesterday();
        $dayOfYear = (int) $today->dayOfYear;
        $daysInYear = (int) $today->daysInYear;
        $yearElapsed = min(1.0, $dayOfYear / max(1, $daysInYear));

        $branches = Branch::query()->orderBy('name')->get();

        foreach ($branches as $branch) {
            // Base the target on the branch grade so bigger branches carry bigger plans.
            $base = match ($branch->grade) {
                'A' => 4_000_000,
                'B' => 2_500_000,
                'C' => 1_200_000,
                default => 600_000,
            };

            // Deterministic per-branch jitter keeps re-runs stable.
            $jitter = 1 + (((int) $branch->id * 37) % 40) / 100;

            $annual = round($base * $jitter, 2);

            $q1 = round($annual * 0.25, 2);
            $q2 = round($annual * 0.25, 2);
            $q3 = round($annual * 0.25, 2);
            $q4 = round($annual - $q1 - $q2 - $q3, 2);

            BranchDepositPlan::updateOrCreate(
                ['branch_id' => $branch->id, 'annual_deposit_plan_id' => $annualDepositPlan->id],
                [
                    'annual_target_amount' => $annual,
                    'q1_target_amount' => $q1,
                    'q2_target_amount' => $q2,
                    'q3_target_amount' => $q3,
                    'q4_target_amount' => $q4,
                    'monthly_target_amount' => round($annual / 12, 2),
                    'weekly_target_amount' => round($annual / 52, 2),
                    'daily_target_amount' => round($annual / 365, 2),
                    'approval_status' => 'approved',
                    'approved_by' => $approver?->id,
                    'approved_at' => $today,
                    'approval_remarks' => 'Approved by ' . ($approver?->name ?? 'system') . ' for analytics testing',
                    'remarks' => 'Seeded plan for analytics testing',
                ]
            );

            if ($annualAccountPlan) {
                $acctBase = match ($branch->grade) {
                    'A' => 900,
                    'B' => 550,
                    'C' => 260,
                    default => 120,
                };

                $acctJitter = 1 + (((int) $branch->id * 23) % 35) / 100;
                $acctAnnual = (int) round($acctBase * $acctJitter);

                $a1 = (int) round($acctAnnual * 0.25);
                $a2 = (int) round($acctAnnual * 0.25);
                $a3 = (int) round($acctAnnual * 0.25);
                $a4 = $acctAnnual - $a1 - $a2 - $a3;

                // A branch with a 120-160 account annual target averages well under
                // one opening per day, so annual/365 rounds to 0 and leaves the
                // daily target unusable (any per-day comparison divides by zero).
                // Floor it at 1: the daily figure is then a "at least one" minimum
                // rather than a strict annual/365 share, which is noted in remarks.
                $acctDaily = max(1, (int) round($acctAnnual / 365));

                BranchAccountPlan::updateOrCreate(
                    ['branch_id' => $branch->id, 'annual_account_plan_id' => $annualAccountPlan->id],
                    [
                        'annual_target_accounts' => $acctAnnual,
                        'q1_target_accounts' => $a1,
                        'q2_target_accounts' => $a2,
                        'q3_target_accounts' => $a3,
                        'q4_target_accounts' => $a4,
                        'monthly_target_accounts' => (int) round($acctAnnual / 12),
                        'weekly_target_accounts' => (int) round($acctAnnual / 52),
                        'daily_target_accounts' => $acctDaily,
                        'approval_status' => 'approved',
                        'approved_by' => $approver?->id,
                        'approved_at' => $today,
                        'approval_remarks' => 'Approved by ' . ($approver?->name ?? 'system') . ' for analytics testing',
                        'remarks' => $acctDaily * 365 < $acctAnnual
                            ? 'Seeded plan for analytics testing. Daily target is a minimum of 1; it is not a strict annual/365 share.'
                            : 'Seeded plan for analytics testing',
                    ]
                );
            }
        }

        $this->command->info(
            "Branch plans ready for {$branches->count()} branches. "
            . round($yearElapsed * 100) . '% of the year elapsed.'
        );
    }
}

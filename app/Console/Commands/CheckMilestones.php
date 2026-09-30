<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\BranchAccountPlan;
use App\Models\BranchDepositPlan;
use App\Models\DailyAccountPerformance;
use App\Models\DailyDepositPerformance;
use App\Models\User;
use App\Notifications\MilestoneAchievedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckMilestones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bpms:check-milestones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check branch performance milestones and send notifications';

    /**
     * Milestone thresholds to check.
     *
     * @var array<int, int>
     */
    protected array $milestones = [25, 50, 75, 100];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking branch performance milestones...');

        $branches = Branch::with(['branchDepositPlans', 'branchAccountPlans'])->get();

        if ($branches->isEmpty()) {
            $this->warn('No branches found.');
            Log::info('Milestone check: No branches found.');
            return self::SUCCESS;
        }

        $admins = User::role('admin')->get();

        if ($admins->isEmpty()) {
            $this->warn('No admin users found. Notifications will not be sent.');
            Log::warning('Milestone check: No admin users found.');
        }

        $notificationsSent = 0;

        foreach ($branches as $branch) {
            // Check deposit milestones
            $depositPlans = $branch->branchDepositPlans;
            if ($depositPlans->isNotEmpty()) {
                $totalDepositTarget = $depositPlans->sum('annual_target_amount');
                $totalDepositActual = DailyDepositPerformance::where('branch_id', $branch->id)->sum('total_deposit_amount');

                $notificationsSent += $this->checkMilestonesForBranch(
                    $branch,
                    'deposit',
                    $totalDepositTarget,
                    $totalDepositActual,
                    $admins,
                );
            }

            // Check account milestones
            $accountPlans = $branch->branchAccountPlans;
            if ($accountPlans->isNotEmpty()) {
                $totalAccountTarget = $accountPlans->sum('annual_target_accounts');
                $totalAccountActual = DailyAccountPerformance::where('branch_id', $branch->id)->sum('total_accounts');

                $notificationsSent += $this->checkMilestonesForBranch(
                    $branch,
                    'account',
                    $totalAccountTarget,
                    $totalAccountActual,
                    $admins,
                );
            }
        }

        $this->info("Milestone check completed. {$notificationsSent} notification(s) sent.");
        Log::info("Milestone check completed. {$notificationsSent} notification(s) sent.");

        return self::SUCCESS;
    }

    /**
     * Check milestones for a specific branch and metric type.
     *
     * @param  Branch  $branch
     * @param  string  $type
     * @param  float  $target
     * @param  float  $actual
     * @param  \Illuminate\Support\Collection<int, User>  $admins
     */
    protected function checkMilestonesForBranch(
        Branch $branch,
        string $type,
        float $target,
        float $actual,
        $admins,
    ): int {
        if ($target <= 0) {
            return 0;
        }

        $achievementPercentage = round(($actual / $target) * 100, 2);
        $sent = 0;

        foreach ($this->milestones as $milestone) {
            if ($achievementPercentage >= $milestone) {
                // Check if this milestone was already notified (avoid duplicates)
                $alreadyNotified = $this->wasMilestoneNotified($branch, $type, $milestone);

                if (! $alreadyNotified) {
                    if ($admins->isNotEmpty()) {
                        Notification::send($admins, new MilestoneAchievedNotification(
                            branchName: $branch->name,
                            milestoneType: $type,
                            achievementPercentage: (float) $milestone,
                            targetAmount: $target,
                            actualAmount: $actual,
                        ));
                        $sent++;
                    }

                    $this->info("  [{$branch->name}] {$type} milestone {$milestone}% achieved ({$achievementPercentage}%).");
                    Log::info("Milestone achieved: Branch {$branch->name}, Type: {$type}, Milestone: {$milestone}%, Achievement: {$achievementPercentage}%");
                }
            }
        }

        return $sent;
    }

    /**
     * Check if a milestone notification was already sent.
     * Uses a simple cache-based approach to prevent duplicate notifications.
     */
    protected function wasMilestoneNotified(Branch $branch, string $type, int $milestone): bool
    {
        $cacheKey = "milestone_notified:{$branch->id}:{$type}:{$milestone}";

        if (cache()->has($cacheKey)) {
            return true;
        }

        // Mark as notified for 24 hours to prevent duplicates
        cache()->put($cacheKey, true, now()->addDay());

        return false;
    }
}

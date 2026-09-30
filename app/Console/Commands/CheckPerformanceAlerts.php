<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\BranchAccountPlan;
use App\Models\BranchDepositPlan;
use App\Models\DailyAccountPerformance;
use App\Models\DailyDepositPerformance;
use App\Models\User;
use App\Notifications\PerformanceAlertNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CheckPerformanceAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bpms:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check branch performance and send alerts for underperforming branches';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking branch performance alerts...');

        $branches = Branch::with(['branchDepositPlans', 'branchAccountPlans'])->get();

        if ($branches->isEmpty()) {
            $this->warn('No branches found.');
            Log::info('Performance alert check: No branches found.');
            return self::SUCCESS;
        }

        $admins = User::role('admin')->get();

        if ($admins->isEmpty()) {
            $this->warn('No admin users found. Notifications will not be sent.');
            Log::warning('Performance alert check: No admin users found.');
        }

        $alertsSent = 0;

        foreach ($branches as $branch) {
            // Check deposit performance
            $depositPlans = $branch->branchDepositPlans;
            if ($depositPlans->isNotEmpty()) {
                $totalDepositTarget = $depositPlans->sum('annual_target_amount');
                $totalDepositActual = DailyDepositPerformance::where('branch_id', $branch->id)->sum('total_deposit_amount');

                $alertsSent += $this->evaluatePerformance(
                    $branch,
                    'Deposit Amount',
                    $totalDepositTarget,
                    $totalDepositActual,
                    $admins,
                );
            }

            // Check account performance
            $accountPlans = $branch->branchAccountPlans;
            if ($accountPlans->isNotEmpty()) {
                $totalAccountTarget = $accountPlans->sum('annual_target_accounts');
                $totalAccountActual = DailyAccountPerformance::where('branch_id', $branch->id)->sum('total_accounts');

                $alertsSent += $this->evaluatePerformance(
                    $branch,
                    'Total Accounts',
                    $totalAccountTarget,
                    $totalAccountActual,
                    $admins,
                );
            }
        }

        $this->info("Performance alert check completed. {$alertsSent} alert(s) sent.");
        Log::info("Performance alert check completed. {$alertsSent} alert(s) sent.");

        return self::SUCCESS;
    }

    /**
     * Evaluate performance and send alerts if thresholds are crossed.
     *
     * @param  Branch  $branch
     * @param  string  $metricName
     * @param  float  $target
     * @param  float  $actual
     * @param  \Illuminate\Support\Collection<int, User>  $admins
     */
    protected function evaluatePerformance(
        Branch $branch,
        string $metricName,
        float $target,
        float $actual,
        $admins,
    ): int {
        if ($target <= 0) {
            return 0;
        }

        $percentage = round(($actual / $target) * 100, 2);
        $sent = 0;

        if ($percentage < 50) {
            // Below target alert
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new PerformanceAlertNotification(
                    branchName: $branch->name,
                    alertType: 'below_target',
                    metricName: $metricName,
                    currentValue: $actual,
                    targetValue: $target,
                    percentage: $percentage,
                ));
                $sent++;
            }

            $this->warn("  [ALERT] {$branch->name} - {$metricName}: {$percentage}% (below 50%)");
            Log::warning("Performance alert: Branch {$branch->name}, Metric: {$metricName}, Achievement: {$percentage}% (below target)");
        } elseif ($percentage > 100) {
            // Above target - congratulatory
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new PerformanceAlertNotification(
                    branchName: $branch->name,
                    alertType: 'above_target',
                    metricName: $metricName,
                    currentValue: $actual,
                    targetValue: $target,
                    percentage: $percentage,
                ));
                $sent++;
            }

            $this->info("  [EXCEEDED] {$branch->name} - {$metricName}: {$percentage}% (above 100%)");
            Log::info("Performance exceeded: Branch {$branch->name}, Metric: {$metricName}, Achievement: {$percentage}% (above target)");
        }

        return $sent;
    }
}

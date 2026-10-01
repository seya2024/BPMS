<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\BranchPlanSeeder;
use Database\Seeders\KpiTrackingHistorySeeder;
use Database\Seeders\PerformanceHistorySeeder;
use Database\Seeders\ReferenceDataSeeder;
use Database\Seeders\TestDataSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Guards against N+1 regressions on the list pages.
 *
 * Two measurement details this test exists to respect:
 *
 *  1. The tables use deferLoading(), so a plain GET renders only the table shell
 *     and issues no row query at all. Measuring the GET reports ~0 queries and
 *     hides every N+1, so the row query is triggered explicitly via loadTable().
 *  2. Query COUNT is asserted, not wall time. Timing in the in-memory SQLite test
 *     database is far too noisy to gate on; an N+1 shows up as a repeated query
 *     whether the data is small or not.
 */
class NoNPlusOneTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Per-page query budget for a full page of rows. A page that starts issuing
     * one query per row or per cell blows straight through this.
     */
    private const BUDGETS = [
        \App\Filament\Resources\DailyDepositPerformances\Pages\ListDailyDepositPerformances::class => 30,
        \App\Filament\Resources\BranchDepositPlans\Pages\ListBranchDepositPlans::class => 20,
        \App\Filament\Resources\BranchAccountPlans\Pages\ListBranchAccountPlans::class => 20,
        \App\Filament\Resources\DailyAccountOpenings\Pages\ListDailyAccountOpenings::class => 20,
        \App\Filament\Resources\DailySuperAppSubscriptions\Pages\ListDailySuperAppSubscriptions::class => 20,
        \App\Filament\Resources\DailyForeignCurrencyGenerations\Pages\ListDailyForeignCurrencyGenerations::class => 20,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Model::withoutEvents(function (): void {
            $this->seed(TestDataSeeder::class);
            $this->seed(BranchPlanSeeder::class);
            $this->seed(PerformanceHistorySeeder::class);
            $this->seed(KpiTrackingHistorySeeder::class);
            $this->seed(ReferenceDataSeeder::class);
        });

        $this->actingAs(User::where('email', 'seidm2031@gmail.com')->firstOrFail());
    }

    public function test_list_pages_do_not_issue_n_plus_one_queries(): void
    {
        $failures = [];
        $report = [];

        foreach (self::BUDGETS as $class => $budget) {
            DB::flushQueryLog();
            DB::enableQueryLog();

            try {
                $component = Livewire::test($class)->call('loadTable');
            } catch (\Throwable $e) {
                DB::disableQueryLog();
                $failures[] = $class . ' threw ' . get_class($e) . ': ' . substr($e->getMessage(), 0, 120);

                continue;
            }

            $log = DB::getQueryLog();
            DB::disableQueryLog();

            $groups = [];
            foreach ($log as $q) {
                $sql = preg_replace('/\s+/', ' ', trim($q['query']));
                $groups[$sql] = ($groups[$sql] ?? 0) + 1;
            }

            $maxRepeat = $groups ? max($groups) : 0;
            $count = count($log);

            $report[] = sprintf('%-58s %3d queries (max repeat %2d, budget %d)',
                class_basename($class), $count, $maxRepeat, $budget);

            if ($count > $budget) {
                $repeatedSql = '';
                foreach ($groups as $sql => $n) {
                    if ($n === $maxRepeat) {
                        $repeatedSql = $sql;

                        break;
                    }
                }

                $failures[] = sprintf(
                    '%s issued %d queries, budget is %d. Most repeated (x%d): %s',
                    class_basename($class),
                    $count,
                    $budget,
                    $maxRepeat,
                    substr($repeatedSql, 0, 100)
                );
            }

            // A single query repeated more than 20 times for a 10-row page is an
            // N+1 regardless of the total.
            if ($maxRepeat > 20) {
                $failures[] = sprintf(
                    '%s repeated one query %d times - N+1',
                    class_basename($class), $maxRepeat
                );
            }

            // A page that silently returns an empty shell is a functional
            // failure that also masks any perf regression, so check it here.
            if (! str_contains($component->html(), '<table')) {
                $failures[] = class_basename($class) . ' rendered no table after loadTable()';
            }
        }

        fwrite(STDERR, "\n" . implode("\n", $report) . "\n");

        $this->assertSame([], $failures, "List page regressions detected:\n" . implode("\n", $failures));
    }
}

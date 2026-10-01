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
use Tests\TestCase;

/**
 * Measures the dashboard's real cost.
 *
 * The dashboard runs ~12 analytics widgets, each with its own aggregation. Query
 * COUNT is the stable signal here: wall time in in-memory SQLite swings by an
 * order of magnitude between otherwise identical runs, so timing is reported but
 * never asserted. The budget guards against a widget silently starting to scan a
 * table row by row.
 */
class DashboardCostTest extends TestCase
{
    use RefreshDatabase;

    /** One full dashboard pass should not need more than this. */
    private const BUDGET = 80;

    /** No single query may repeat more than this many times. */
    private const MAX_REPEAT = 6;

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

    public function test_dashboard_query_cost(): void
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $start = microtime(true);
        $response = $this->get('/admin');
        $ms = (microtime(true) - $start) * 1000;

        $log = DB::getQueryLog();
        DB::disableQueryLog();

        $groups = [];
        foreach ($log as $q) {
            $sql = preg_replace('/\s+/', ' ', trim($q['query']));
            $groups[$sql] = ($groups[$sql] ?? 0) + 1;
        }
        arsort($groups);

        fwrite(STDERR, sprintf(
            "\n  dashboard: %d queries, max repeat %d, %.0f ms, peak %.1f MB, status %d\n",
            count($log),
            $groups ? max($groups) : 0,
            $ms,
            memory_get_peak_usage(true) / 1048576,
            $response->getStatusCode()
        ));

        fwrite(STDERR, "  most repeated queries:\n");
        foreach (array_slice($groups, 0, 6, true) as $sql => $n) {
            fwrite(STDERR, sprintf("    x%-3d %s\n", $n, substr($sql, 0, 120)));
        }

        $this->assertSame(200, $response->getStatusCode());
        $this->assertLessThanOrEqual(
            self::BUDGET,
            count($log),
            'Dashboard query count regressed. See the repeated queries above for the likely N+1.'
        );
        $this->assertLessThanOrEqual(
            self::MAX_REPEAT,
            $groups ? max($groups) : 0,
            'A single query repeats too many times on the dashboard - likely an N+1.'
        );
    }

    /**
     * Each analytics widget's own aggregation, timed and counted in isolation so
     * a regression can be attributed rather than guessed at.
     */
    public function test_analytics_service_cost(): void
    {
        $ops = [
            'depositTotals' => fn () => \App\Filament\Widgets\AnalyticsService::depositTotals(),
            'accountSnapshot' => fn () => \App\Filament\Widgets\AnalyticsService::accountSnapshot(),
            'branchAttainment' => fn () => \App\Filament\Widgets\AnalyticsService::branchAttainment(),
            'segmentMix' => fn () => \App\Filament\Widgets\AnalyticsService::segmentMix(),
            'bankingTypeMix' => fn () => \App\Filament\Widgets\AnalyticsService::bankingTypeMix(),
            'districtPerformance' => fn () => \App\Filament\Widgets\AnalyticsService::districtPerformance(),
            'dailyDepositSeries' => fn () => \App\Filament\Widgets\AnalyticsService::dailyDepositSeries(),
            'alignSeries' => fn () => \App\Filament\Widgets\AnalyticsService::alignSeries(),
            'accountFlowTotals' => fn () => \App\Filament\Widgets\AnalyticsService::accountFlowTotals(),
            'accountTargets' => fn () => \App\Filament\Widgets\AnalyticsService::accountTargets(),
        ];

        $rows = [];
        $failures = [];

        foreach ($ops as $name => $fn) {
            DB::flushQueryLog();
            DB::enableQueryLog();
            $t = microtime(true);
            $fn();
            $ms = (microtime(true) - $t) * 1000;
            $log = DB::getQueryLog();
            DB::disableQueryLog();

            $rows[] = sprintf('  %-22s %3d queries  %7.1f ms', $name, count($log), $ms);

            // A single aggregation should be a handful of queries at most; more
            // means it is looping in PHP or issuing per-row work.
            if (count($log) > 8) {
                $failures[] = "{$name} issued " . count($log) . ' queries';
            }
        }

        fwrite(STDERR, "\n" . implode("\n", $rows) . "\n");

        $this->assertSame([], $failures, 'Analytics aggregations are too chatty: ' . implode(', ', $failures));
    }
}

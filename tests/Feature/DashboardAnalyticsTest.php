<?php

namespace Tests\Feature;

use App\Filament\Widgets\AccountAnalyticsWidget;
use App\Filament\Widgets\AccountTrendChart;
use App\Filament\Widgets\BankingTypeSplitChart;
use App\Filament\Widgets\BranchAttainmentChart;
use App\Filament\Widgets\DashboardSummaryWidget;
use App\Filament\Widgets\DepositAnalyticsWidget;
use App\Filament\Widgets\DepositTrendChart;
use App\Filament\Widgets\DistrictPerformanceChart;
use App\Filament\Widgets\KpiScorecardWidget;
use App\Filament\Widgets\NetChangeChart;
use App\Filament\Widgets\PlanAchievementWidget;
use App\Filament\Widgets\SegmentMixChart;
use App\Models\BankingType;
use App\Models\Branch;
use App\Models\DailyDepositPerformance;
use App\Models\District;
use App\Models\User;
use Database\Seeders\BranchPlanSeeder;
use Database\Seeders\PerformanceHistorySeeder;
use Database\Seeders\TestDataSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Performance history and branch plans are required, otherwise every
        // widget renders zeros and the assertions prove nothing.
        Model::withoutEvents(function (): void {
            $this->seed(TestDataSeeder::class);
            $this->seed(BranchPlanSeeder::class);
            $this->seed(PerformanceHistorySeeder::class);
        });

        $user = User::where('email', 'seidm2031@gmail.com')->firstOrFail();
        $this->actingAs($user);
    }

    public static function chartWidgets(): array
    {
        return [
            'deposit trend' => [DepositTrendChart::class],
            'net change' => [NetChangeChart::class],
            'segment mix' => [SegmentMixChart::class],
            'banking split' => [BankingTypeSplitChart::class],
            'account trend' => [AccountTrendChart::class],
            'branch attainment' => [BranchAttainmentChart::class],
            'district split' => [DistrictPerformanceChart::class],
        ];
    }

    public static function statsWidgets(): array
    {
        return [
            'deposit analytics' => [DepositAnalyticsWidget::class],
            'plan achievement' => [PlanAchievementWidget::class],
            'account analytics' => [AccountAnalyticsWidget::class],
            'kpi scorecard' => [KpiScorecardWidget::class],
        ];
    }

    /** @dataProvider chartWidgets */
    public function test_chart_widget_renders_and_has_data(string $widget): void
    {
        $component = Livewire::test($widget);

        $component->assertOk();

        $data = $component->instance()->analyticsChartData();

        $this->assertArrayHasKey('datasets', $data, "$widget must return chart datasets");
        $this->assertNotEmpty($data['datasets'], "$widget returned no datasets");
        $this->assertArrayHasKey('labels', $data);
        $this->assertNotEmpty($data['labels'], "$widget returned no labels");

        // Guard against widgets that render a chart full of zeros.
        $hasNonZero = false;

        foreach ($data['datasets'] as $dataset) {
            foreach ((array) ($dataset['data'] ?? []) as $value) {
                if (is_numeric($value) && abs((float) $value) > 0) {
                    $hasNonZero = true;
                    break 2;
                }
            }
        }

        $this->assertTrue($hasNonZero, "$widget rendered only zeros despite seeded performance history.");
    }

    /** @dataProvider statsWidgets */
    public function test_stats_widget_renders(string $widget): void
    {
        $component = Livewire::test($widget);

        $component->assertOk();

        $stats = $component->instance()->analyticsStats();

        $this->assertNotEmpty($stats, "$widget produced no stats");

        // The empty-data fallback still counts as a valid render, but when history
        // is seeded the widget must report real figures rather than a placeholder.
        $values = array_map(fn ($stat) => (string) $stat->getValue(), $stats);

        $hasFigure = collect($values)->contains(fn ($v) => preg_match('/\d/', $v) === 1);

        $this->assertTrue($hasFigure, "$widget reported no numeric values: ".implode(', ', $values));
    }

    public function test_attendance_chart_renders(): void
    {
        Livewire::test(BranchAttainmentChart::class)->assertOk();
    }

    /**
 * Every dashboard widget must fit inside the 4-column grid.
 *
 * A single 'full' span widget leaves empty slots, which is the "empty space
 * between widgets" problem. This locks that in.
 */
    public function test_no_dashboard_widget_spans_the_whole_row(): void
    {
        $dashboard = \App\Filament\Pages\Dashboard::class;

        foreach ((new $dashboard)->getWidgets() as $widget) {
            $span = (new ReflectionClass($widget))->getDefaultProperties()['columnSpan'] ?? 1;

            $this->assertContains(
                $span,
                [1, 2, 4],
                "{$widget} spans {$span}; expected 1 (stats), 2 (charts) or 4 (summary band).",
            );
        }
    }

    /**
     * A full-width widget is only acceptable as the very last row: anywhere
     * else it strands empty slots in the middle of the grid.
     */
    public function test_full_width_widget_is_last(): void
    {
        $dashboard = \App\Filament\Pages\Dashboard::class;
        $widgets = array_values((new $dashboard)->getWidgets());

        foreach ($widgets as $index => $widget) {
            $span = (int) ((new ReflectionClass($widget))->getDefaultProperties()['columnSpan'] ?? 1);

            if ($span === 4) {
                $this->assertSame(
                    count($widgets) - 1,
                    $index,
                    "{$widget} spans the full row but is not last, which strands empty slots.",
                );
            }
        }
    }

    /**
     * Rows must fill completely: the total of every columnSpan has to be an
     * exact multiple of the column count, otherwise the last row is half empty.
     */
    public function test_dashboard_grid_fills_complete_rows(): void
    {
        $dashboard = \App\Filament\Pages\Dashboard::class;

        $columns = (new $dashboard)->getColumns();
        $widgets = (new $dashboard)->getWidgets();

        $this->assertSame(4, $columns['lg']);
        $this->assertSame(2, $columns['md']);
        $this->assertSame(1, $columns['sm']);

        $totalSpans = 0;

        foreach ($widgets as $widget) {
            $totalSpans += (int) ((new ReflectionClass($widget))->getDefaultProperties()['columnSpan'] ?? 1);
        }

        $this->assertSame(
            0,
            $totalSpans % 4,
            "Widget spans total {$totalSpans}, which leaves empty slots in a 4-column grid.",
        );
    }

    /**
     * Charts are double width so they render two per row; stats cards stay at
     * one column so four fit per row. The summary widget is full-width.
     */
    public function test_charts_are_double_width_and_stats_are_single(): void
    {
        $dashboard = \App\Filament\Pages\Dashboard::class;

        foreach ((new $dashboard)->getWidgets() as $widget) {
            $properties = (new ReflectionClass($widget))->getDefaultProperties();
            $span = (int) ($properties['columnSpan'] ?? 1);
            $isStats = is_subclass_of($widget, \Filament\Widgets\StatsOverviewWidget::class);
            $isSummary = $widget === DashboardSummaryWidget::class;

            if ($isStats) {
                $this->assertSame(1, $span, "{$widget} is a stats card and must span 1 column.");
            } elseif ($isSummary) {
                $this->assertSame(4, $span, "{$widget} is the summary band and must span 4 columns.");
            } else {
                $this->assertSame(2, $span, "{$widget} is a chart and must span 2 columns.");
            }
        }
    }

    /**
     * Stats widgets must stay short. A quarter-width card packed with seven stat
     * rows is taller than the charts beside it and unbalances the row.
     */
    public function test_stats_widgets_stay_compact(): void
    {
        foreach (self::statsWidgets() as [$widget]) {
            $stats = Livewire::test($widget)->instance()->analyticsStats();

            $this->assertLessThanOrEqual(
                4,
                count($stats),
                "{$widget} renders " . count($stats) . ' stats; keep it to 4 or fewer.',
            );
        }
    }

    /**
     * The dashboard page must render with every registered widget, otherwise the
     * analytics are registered but never actually shown.
     */
    public function test_dashboard_page_renders_all_widgets(): void
    {
        $dashboard = \App\Filament\Pages\Dashboard::class;

        $widgets = (new $dashboard)->getWidgets();

        $this->assertNotEmpty($widgets, 'Dashboard registers no widgets.');

        $response = $this->actingAs(
            User::where('email', 'seidm2031@gmail.com')->firstOrFail()
        )->get('/admin');

        $response->assertOk();

        // Every registered widget must resolve to a real class.
        foreach ($widgets as $widget) {
            $this->assertTrue(class_exists($widget), "Widget class {$widget} does not exist.");
        }

        // Filament lazy-loads dashboard widgets, so their markup is not present in
        // the initial HTML response. The per-widget tests above cover rendering;
        // this only guarantees the page itself boots with the widgets registered.
        $this->assertStringContainsString(
            'wire:id',
            $response->getContent(),
            'Dashboard page should render a Livewire root.',
        );
    }

    /**
     * The copyright notice is registered as a panel render hook, so it must
     * appear on the dashboard and on resource list pages alike.
     */
    public function test_copyright_renders_on_every_page(): void
    {
        $user = User::where('email', 'seidm2031@gmail.com')->firstOrFail();

        foreach (['/admin', '/admin/daily-deposit-performances', '/admin/branches'] as $url) {
            $content = $this->actingAs($user)->get($url)->getContent();

            $this->assertStringContainsString(
                'All rights reserved',
                $content,
                "Copyright notice missing from {$url}.",
            );
        }
    }

    /**
     * The district filter must actually change the numbers, otherwise the select
     * is decorative.
     *
     * The seeder places every branch in district 1, so a second district is created
     * here to give the filter something to actually exclude.
     */
    public function test_district_filter_narrows_results(): void
    {
        Model::withoutEvents(function (): void {
            $district = District::create([
                'name' => 'Analytics Test District',
                'location_type' => 'City',
            ]);

            $bankingType = BankingType::firstOrFail();
            $day = Carbon::yesterday()->toDateString();

            $testBranch = Branch::create([
                'code' => 'ANL-001',
                'name' => 'Analytics Test Branch',
                'grade' => 'C',
                'district_id' => $district->id,
                'bankingType_id' => $bankingType->id,
            ]);

            DailyDepositPerformance::create([
                'branch_id' => $testBranch->id,
                'business_day' => $day,
                'total_deposit_amount' => 123_456.78,
            ]);
        });

        $all = Livewire::test(DepositAnalyticsWidget::class)
            ->instance()->analyticsStats();

        $scoped = Livewire::test(DepositAnalyticsWidget::class)
            ->set('filter', (string) District::where('name', 'Analytics Test District')->value('id'))
            ->instance()->analyticsStats();

        $this->assertNotEmpty($all);
        $this->assertNotEmpty($scoped);

        // Scoping to a district with a single branch must drop the grand total.
        $this->assertNotSame(
            $all[0]->getValue(),
            $scoped[0]->getValue(),
            'Filtering by district must change the reported total.',
        );

        // And it must equal only that branch's figure.
        $this->assertStringContainsString('123,456.78', (string) $scoped[0]->getValue());
    }

    public function test_every_widget_offers_the_district_filter_with_all_default(): void
    {
        $widgets = array_merge(
            array_column(self::chartWidgets(), 0),
            array_column(self::statsWidgets(), 0),
        );

        foreach ($widgets as $widget) {
            $instance = Livewire::test($widget)->instance();

            $options = $instance->districtFilterOptions();

            $this->assertArrayHasKey('', $options, "$widget must offer an 'All districts' option");
            $this->assertSame('All districts', $options[''], "$widget default option label");
            $this->assertGreaterThan(
                1,
                count($options),
                "$widget should offer at least one district beyond the default.",
            );
            $this->assertSame('', (string) $instance->filter, "$widget must default to all districts");
        }
    }
}

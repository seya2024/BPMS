<?php

namespace Tests\Feature;

use App\Exports\ReportExport;
use App\Models\District;
use App\Models\User;
use App\Services\ReportService;
use Database\Seeders\KpiTrackingHistorySeeder;
use Database\Seeders\PerformanceHistorySeeder;
use Database\Seeders\ReferenceDataSeeder;
use Database\Seeders\TestDataSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

/** The five reports must produce rows, and must never report today or the future. */
class ReportingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Model::withoutEvents(function (): void {
            $this->seed(TestDataSeeder::class);
            $this->seed(PerformanceHistorySeeder::class);
            $this->seed(KpiTrackingHistorySeeder::class);
            $this->seed(ReferenceDataSeeder::class);
        });

        $this->actingAs(User::where('email', 'seidm2031@gmail.com')->firstOrFail());
    }

    public function test_window_defaults_to_a_single_yesterday(): void
    {
        $window = ReportService::resolveWindow(null, null);

        $this->assertSame(Carbon::yesterday()->toDateString(), $window['from']);
        $this->assertSame(Carbon::yesterday()->toDateString(), $window['to']);
        $this->assertTrue($window['singleDay']);
    }

    public function test_window_clamps_a_future_end_date_to_yesterday(): void
    {
        $future = Carbon::tomorrow()->toDateString();
        $window = ReportService::resolveWindow(Carbon::yesterday()->subDays(5)->toDateString(), $future);

        $this->assertSame(
            Carbon::yesterday()->toDateString(),
            $window['to'],
            'A report must not be able to cover today or the future.'
        );
    }

    public function test_window_normalises_a_reversed_range(): void
    {
        $a = Carbon::yesterday()->subDays(3)->toDateString();
        $b = Carbon::yesterday()->subDays(9)->toDateString();

        $window = ReportService::resolveWindow($a, $b);

        $this->assertSame($b, $window['from']);
        $this->assertSame($a, $window['to']);
    }

    /**
     * @dataProvider reportKeys
     */
    public function test_every_report_returns_one_row_per_branch(string $report): void
    {
        $window = ReportService::resolveWindow(null, null);

        $rows = ReportService::run($report, $window['from'], $window['to']);

        $branchCount = District::firstOrFail()->branches()->count();

        $this->assertCount(
            $branchCount,
            $rows,
            "The {$report} report should have one row per branch."
        );
        $this->assertNotEmpty($rows[0], 'Rows must carry data.');
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function reportKeys(): array
    {
        $out = [];

        foreach (array_keys(ReportService::REPORTS) as $key) {
            $out[$key] = [$key];
        }

        return $out;
    }

    public function test_deposit_report_splits_banking_types(): void
    {
        $window = ReportService::resolveWindow(null, null);
        $rows = ReportService::run('deposits', $window['from'], $window['to']);

        $row = $rows[0];

        foreach (['Conventional Total', 'IFB Total', 'Total Deposit', 'Net Change'] as $column) {
            $this->assertArrayHasKey($column, $row);
        }
    }

    public function test_all_kpi_report_carries_every_kpi_column(): void
    {
        $window = ReportService::resolveWindow(null, null);
        $rows = ReportService::run('all_kpi', $window['from'], $window['to']);

        foreach ([
            'Total Deposit (ETB)',
            'Accounts Opened',
            'New Accounts',
            'FX Generated (ETB)',
            'Super App Subs',
        ] as $column) {
            $this->assertArrayHasKey($column, $rows[0]);
        }
    }

    public function test_export_produces_headings_and_a_filename(): void
    {
        $window = ReportService::resolveWindow(null, null);

        $export = new ReportExport('deposits', $window['from'], $window['to']);

        $this->assertNotEmpty($export->array());
        $this->assertContains('Branch', $export->headings());
        $this->assertSame('Daily Deposit Report', $export->title());
        $this->assertStringEndsWith('.xlsx', ReportExport::filename('deposits', $window['from'], $window['to']));
    }

    public function test_page_renders_with_all_five_reports_offered(): void
    {
        $html = Livewire::test(\App\Filament\Pages\Reportings::class)->html();

        foreach (ReportService::REPORTS as $key => $label) {
            $this->assertStringContainsString($label, $html, "Missing report option: {$label}");
        }

        // Defaults to yesterday, and the action offers Excel.
        $this->assertStringContainsString('Export to Excel', $html);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Locks the two batch entry pages to the same conventions.
 *
 * WHY THIS READS SOURCE INSTEAD OF RENDERING:
 * an earlier version of this test mounted both pages and asserted against
 * ~0.7MB of rendered HTML. That made the suite exceed 30 minutes, and a failing
 * string assertion over a single-line 700KB haystack stalled PHPUnit in diff
 * generation rather than reporting a failure. Style conventions are declarations
 * in source, so assert them there: it is orders of magnitude cheaper and the
 * failure message points at the exact file.
 *
 * The behavioural half - which branches land in which section, and that dates are
 * locked to the past - is covered by render-based tests elsewhere
 * (BatchEntrySectionsTest, BusinessDayRuleTest).
 */
class BatchEntryParityTest extends TestCase
{
    /** The pages that must stay visually identical. */
    private const SOURCES = [
        'openings' => __DIR__ . '/../../app/Filament/Pages/BatchDailyAccountOpenings.php',
        'deposit' => __DIR__ . '/../../app/Filament/Pages/BatchDailyDepositPerformances.php',
    ];

    /** Style values both pages must declare. */
    private const SHARED_CONVENTIONS = [
        'row class' => 'fi-ta-table-excel',
        'monospace font' => '"SF Mono", "Fira Code", monospace',
        '12px font' => 'font-size: 12px',
        'row height' => 'height: 28px',
        'compact padding' => 'padding: 2px 4px',
        'right aligned' => 'text-align: right',
        'number input width' => 'width: 150px',
        'remarks input width' => 'width: 100%',
        'branch cell shading' => 'background: #f3f4f6',
        'row border removal' => 'border: none',
    ];

    /**
     * @dataProvider sharedConventions
     */
    public function test_both_pages_declare_the_same_style(string $label, string $needle): void
    {
        foreach (self::SOURCES as $page => $path) {
            $this->assertFileExists($path);

            $this->assertStringContainsString(
                $needle,
                (string) file_get_contents($path),
                "The {$page} batch page should declare the shared '{$label}' style."
            );
        }
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function sharedConventions(): array
    {
        $out = [];

        foreach (self::SHARED_CONVENTIONS as $label => $needle) {
            $out[$label] = [$label, $needle];
        }

        return $out;
    }

    public function test_neither_page_has_drifted_to_a_wider_input(): void
    {
        foreach (self::SOURCES as $page => $path) {
            $src = (string) file_get_contents($path);

            $this->assertStringNotContainsString(
                'width: 200px',
                $src,
                "The {$page} batch page drifted to 200px inputs; both pages use 150px."
            );
            $this->assertStringNotContainsString(
                'width: 150%',
                $src,
                "The {$page} batch page drifted to a 150% remarks input; both pages use 100%."
            );
        }
    }

    /**
     * Column widths must add up to 100%. These used to total 96%, leaving a dead
     * gap at the right edge of the deposit table.
     */
    public function test_column_widths_are_shared_and_sum_to_one_hundred(): void
    {
        foreach (self::SOURCES as $page => $path) {
            $src = (string) file_get_contents($path);

            // The openings page declares literal widths.
            if (str_contains($src, "width('25%')")) {
                $this->assertStringContainsString(
                    "width('15%')",
                    $src,
                    "The {$page} page should use 15% data columns like the other page."
                );
            }

            // The deposit page computes them, so assert the computation exists.
            if (str_contains($src, '$dataWidth')) {
                $this->assertStringContainsString(
                    '(100 - $branchWidth - $remarksWidth)',
                    $src,
                    "The {$page} page should compute data column widths so they total 100%."
                );
            }
        }
    }

    public function test_both_pages_group_sections_by_banking_type(): void
    {
        foreach (self::SOURCES as $page => $path) {
            $src = (string) file_get_contents($path);

            // Sections must be filtered by the relationship, not assumed.
            $this->assertStringContainsString(
                "->where('bankingType_id', 1)",
                $src,
                "The {$page} page must select Conventional branches by bankingType_id."
            );
            $this->assertStringContainsString(
                "->where('bankingType_id', 2)",
                $src,
                "The {$page} page must select IFB branches by bankingType_id."
            );

            // And must not list every branch under IFB.
            $this->assertStringNotContainsString(
                '$ifbBranches = $branches;',
                $src,
                "The {$page} page must not list Conventional branches under IFB."
            );
        }
    }

    public function test_both_pages_use_the_shared_business_day_picker(): void
    {
        foreach (self::SOURCES as $page => $path) {
            $src = (string) file_get_contents($path);

            $this->assertStringContainsString(
                'BusinessDay::picker()',
                $src,
                "The {$page} page must use the shared BusinessDay picker so the date rule cannot drift."
            );

            $this->assertStringNotContainsString(
                "DatePicker::make('business_day')",
                $src,
                "The {$page} page must not declare its own business_day picker."
            );
        }
    }
}

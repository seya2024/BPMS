<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Sheet;

/**
 * Writes a report dataset to a single sheet.
 *
 * Built FromArray rather than FromQuery: the report service has already reduced
 * everything to one row per branch, so the row count is bounded by the branch
 * count and holding it in memory is safe. Aggregating inside the export would
 * instead stream millions of detail rows through Excel.
 */
class ReportExport implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    public function __construct(
        protected string $report,
        protected string $from,
        protected string $to,
        protected ?int $districtId = null,
    ) {}

    public function array(): array
    {
        return ReportService::run($this->report, $this->from, $this->to, $this->districtId);
    }

    /**
     * Headings come from the first row, so a new column in the service is picked
     * up without a second edit here.
     */
    public function headings(): array
    {
        $first = $this->array()[0] ?? [];

        return array_keys($first);
    }

    public function title(): string
    {
        return ReportService::REPORTS[$this->report] ?? 'Report';
    }

    public function sheet(Sheet $sheet): Sheet
    {
        return $sheet->freezePane(1, 1);
    }

    public static function filename(string $report, string $from, string $to): string
    {
        $label = str($report)->replace('_', '-')->value();
        $range = $from === $to ? $from : "{$from}_to_{$to}";

        return "{$label}_{$range}.xlsx";
    }
}

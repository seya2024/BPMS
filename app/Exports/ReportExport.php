<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Sheet;

/**
 * Multi-sheet export:
 *   Sheet 1 → Conventional Branches
 *   Sheet 2 → IFB Branches
 *
 * The report service is queried once per sheet, filtered by bankingType_id,
 * so each tab contains exactly the branches of that banking type.
 */
class ReportExport implements WithMultipleSheets
{
    public function __construct(
        protected string $report,
        protected string $from,
        protected string $to,
        protected ?int $districtId = null,
        protected ?int $branchId = null,
        protected ?int $bankingTypeId = null,
    ) {}

    /**
     * @return array<int, ReportSheet>
     */
    public function sheets(): array
    {
        return [
            // Conventional Banking tab
            new ReportSheet(
                report: $this->report,
                from: $this->from,
                to: $this->to,
                title: 'Conventional Branches',
                districtId: $this->districtId,
                branchId: $this->branchId,
                bankingTypeId: $this->resolveBankingTypeId('conventional'),
            ),

            // IFB tab
            new ReportSheet(
                report: $this->report,
                from: $this->from,
                to: $this->to,
                title: 'IFB Branches',
                districtId: $this->districtId,
                branchId: $this->branchId,
                bankingTypeId: $this->resolveBankingTypeId('ifb'),
            ),
        ];
    }

    /**
     * Resolve the banking_types.id for a given type by matching
     * the name pattern in the banking_types table.
     *
     * If the user already picked a specific bankingTypeId in the filter,
     * only the matching sheet is filled; the other becomes empty.
     */
    protected function resolveBankingTypeId(string $kind): ?int
    {
        // If a specific type was selected by the user, honour it
        // (and let the other sheet come back empty).
        if ($this->bankingTypeId) {
            $selected = \App\Models\BankingType::find($this->bankingTypeId);
            if (!$selected) return $this->bankingTypeId;

            $name = strtolower($selected->name);
            $isIfb = str_contains($name, 'ifb') || str_contains($name, 'islamic');

            if ($kind === 'ifb' && $isIfb) return $this->bankingTypeId;
            if ($kind === 'conventional' && !$isIfb) return $this->bankingTypeId;

            return -1; // force empty result on the wrong sheet
        }

        // Otherwise pick the first banking type that matches the pattern
        $query = \App\Models\BankingType::query()->orderBy('id');

        return $kind === 'ifb'
            ? (int) $query->where(function ($q) {
                $q->where('name', 'like', '%IFB%')
                  ->orWhere('name', 'like', '%Islamic%');
            })->value('id')
            : (int) $query->where('name', 'not like', '%IFB%')
                ->where('name', 'not like', '%Islamic%')
                ->value('id');
    }

    public static function filename(string $report, string $from, string $to): string
    {
        $label = str($report)->replace('_', '-')->value();
        $range = $from === $to ? $from : "{$from}_to_{$to}";

        return "{$label}_{$range}.xlsx";
    }
}

/**
 * One sheet inside the workbook.
 */
class ReportSheet implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    public function __construct(
        protected string $report,
        protected string $from,
        protected string $to,
        protected string $title,
        protected ?int $districtId = null,
        protected ?int $branchId = null,
        protected ?int $bankingTypeId = null,
    ) {}

    public function array(): array
    {
        // bankingTypeId = -1 forces an empty result (used when the user
        // filtered to a specific type that doesn't belong to this sheet).
        if ($this->bankingTypeId === -1) {
            return [];
        }

        return ReportService::run(
            $this->report,
            $this->from,
            $this->to,
            $this->districtId,
            $this->branchId,
            $this->bankingTypeId ?: null,
        );
    }

    public function headings(): array
    {
        $first = $this->array()[0] ?? [];

        // Fallback headings if the sheet is empty — matches the report columns
        // so an empty tab still has a proper header row.
        if (empty($first)) {
            return ['District', 'Branch', 'Banking Type'];
        }

        return array_keys($first);
    }

    public function title(): string
    {
        return $this->title;
    }

    public function sheet(Sheet $sheet): Sheet
    {
        return $sheet->freezePane(1, 1);
    }
}
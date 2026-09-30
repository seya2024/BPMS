<?php

namespace App\Exports;

use App\Models\DailyAccountPerformance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountPerformanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DailyAccountPerformance::with(['branch.district'])
            ->orderBy('business_day', 'desc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Branch Code',
            'Branch Name',
            'District',
            'Business Day',
            'Total Accounts',
            'Active Accounts',
            'New Accounts',
            'Dormant Accounts',
            'Reactivated',
        ];
    }

    /**
     * @param DailyAccountPerformance $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->branch?->code ?? 'N/A',
            $row->branch?->name ?? 'N/A',
            $row->branch?->district?->name ?? 'N/A',
            $row->business_day?->format('Y-m-d') ?? 'N/A',
            number_format((int) $row->total_accounts),
            number_format((int) $row->active_accounts),
            number_format((int) $row->new_accounts),
            number_format((int) $row->dormant_accounts),
            number_format((int) $row->reactivated_accounts),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F4E79'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}

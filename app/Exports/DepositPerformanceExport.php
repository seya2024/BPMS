<?php

namespace App\Exports;

use App\Models\DailyDepositPerformance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepositPerformanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DailyDepositPerformance::with(['branch.district'])
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
            'Total Deposit',
            'New Deposit',
            'Inflow',
            'Outflow',
            'Net Change',
        ];
    }

    /**
     * @param DailyDepositPerformance $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->branch?->code ?? 'N/A',
            $row->branch?->name ?? 'N/A',
            $row->branch?->district?->name ?? 'N/A',
            $row->business_day?->format('Y-m-d') ?? 'N/A',
            number_format((float) $row->total_deposit_amount, 2),
            number_format((float) $row->new_deposit_amount, 2),
            number_format((float) $row->deposit_inflow_amount, 2),
            number_format((float) $row->deposit_outflow_amount, 2),
            number_format((float) $row->net_deposit_change, 2),
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

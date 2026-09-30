<?php

namespace App\Exports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BranchTargetExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Branch::with(['district', 'branchDepositPlans', 'branchAccountPlans'])
            ->orderBy('code')
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
            'Annual Deposit Target',
            'Q1 Deposit Target',
            'Q2 Deposit Target',
            'Q3 Deposit Target',
            'Q4 Deposit Target',
            'Monthly Deposit Target',
            'Annual Account Target',
            'Q1 Account Target',
            'Q2 Account Target',
            'Q3 Account Target',
            'Q4 Account Target',
            'Monthly Account Target',
        ];
    }

    /**
     * @param Branch $row
     * @return array
     */
    public function map($row): array
    {
        $depositPlan = $row->branchDepositPlans->first();
        $accountPlan = $row->branchAccountPlans->first();

        return [
            $row->code ?? 'N/A',
            $row->name ?? 'N/A',
            $row->district?->name ?? 'N/A',
            $depositPlan ? number_format((float) $depositPlan->annual_target_amount, 2) : '0.00',
            $depositPlan ? number_format((float) $depositPlan->q1_target_amount, 2) : '0.00',
            $depositPlan ? number_format((float) $depositPlan->q2_target_amount, 2) : '0.00',
            $depositPlan ? number_format((float) $depositPlan->q3_target_amount, 2) : '0.00',
            $depositPlan ? number_format((float) $depositPlan->q4_target_amount, 2) : '0.00',
            $depositPlan ? number_format((float) $depositPlan->monthly_target_amount, 2) : '0.00',
            $accountPlan ? number_format((int) $accountPlan->annual_target_accounts) : '0',
            $accountPlan ? number_format((int) $accountPlan->q1_target_accounts) : '0',
            $accountPlan ? number_format((int) $accountPlan->q2_target_accounts) : '0',
            $accountPlan ? number_format((int) $accountPlan->q3_target_accounts) : '0',
            $accountPlan ? number_format((int) $accountPlan->q4_target_accounts) : '0',
            $accountPlan ? number_format((int) $accountPlan->monthly_target_accounts) : '0',
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

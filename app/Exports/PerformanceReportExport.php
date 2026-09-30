<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PerformanceReportExport implements WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            'Deposit Performance' => new DepositPerformanceExport(),
            'Account Performance' => new AccountPerformanceExport(),
            'Branch Targets' => new BranchTargetExport(),
        ];
    }
}

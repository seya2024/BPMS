<?php

namespace App\Http\Controllers;

use App\Exports\AccountPerformanceExport;
use App\Exports\BranchTargetExport;
use App\Exports\DepositPerformanceExport;
use App\Exports\PerformanceReportExport;
use App\Models\DailyAccountPerformance;
use App\Models\DailyDepositPerformance;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    /**
     * Export daily deposit performance to Excel.
     */
    public function exportDeposits(): BinaryFileResponse
    {
        $fileName = 'deposit_performance_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new DepositPerformanceExport, $fileName);
    }

    /**
     * Export daily account performance to Excel.
     */
    public function exportAccounts(): BinaryFileResponse
    {
        $fileName = 'account_performance_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new AccountPerformanceExport, $fileName);
    }

    /**
     * Export branch targets to Excel.
     */
    public function exportTargets(): BinaryFileResponse
    {
        $fileName = 'branch_targets_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new BranchTargetExport, $fileName);
    }

    /**
     * Export full performance report (multi-sheet) to Excel.
     */
    public function exportFullReport(): BinaryFileResponse
    {
        $fileName = 'performance_report_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new PerformanceReportExport, $fileName);
    }

    /**
     * Export daily deposit performance to PDF.
     */
    public function exportDepositsPdf(): \Illuminate\Http\Response
    {
        $deposits = DailyDepositPerformance::with(['branch.district'])
            ->orderBy('business_day', 'desc')
            ->get();

        $pdf = Pdf::loadView('exports.performance-pdf', [
            'deposits' => $deposits,
            'accounts' => null,
            'reportType' => 'deposit',
            'dateRange' => $this->getDateRange($deposits->pluck('business_day')),
        ]);

        return $pdf->download('deposit_performance_' . date('Y-m-d_His') . '.pdf');
    }

    /**
     * Export daily account performance to PDF.
     */
    public function exportAccountsPdf(): \Illuminate\Http\Response
    {
        $accounts = DailyAccountPerformance::with(['branch.district'])
            ->orderBy('business_day', 'desc')
            ->get();

        $pdf = Pdf::loadView('exports.performance-pdf', [
            'deposits' => null,
            'accounts' => $accounts,
            'reportType' => 'account',
            'dateRange' => $this->getDateRange($accounts->pluck('business_day')),
        ]);

        return $pdf->download('account_performance_' . date('Y-m-d_His') . '.pdf');
    }

    /**
     * Export full performance report to PDF.
     */
    public function exportFullReportPdf(): \Illuminate\Http\Response
    {
        $deposits = DailyDepositPerformance::with(['branch.district'])
            ->orderBy('business_day', 'desc')
            ->get();

        $accounts = DailyAccountPerformance::with(['branch.district'])
            ->orderBy('business_day', 'desc')
            ->get();

        $allDates = $deposits->pluck('business_day')
            ->merge($accounts->pluck('business_day'));

        $pdf = Pdf::loadView('exports.performance-pdf', [
            'deposits' => $deposits,
            'accounts' => $accounts,
            'reportType' => 'full',
            'dateRange' => $this->getDateRange($allDates),
        ]);

        return $pdf->download('performance_report_' . date('Y-m-d_His') . '.pdf');
    }

    /**
     * Get a formatted date range string from a collection of dates.
     *
     * @param \Illuminate\Support\Collection $dates
     * @return string
     */
    private function getDateRange($dates): string
    {
        $dates = $dates->filter()->sort();

        if ($dates->isEmpty()) {
            return 'No data available';
        }

        $startDate = $dates->first()->format('M d, Y');
        $endDate = $dates->last()->format('M d, Y');

        if ($startDate === $endDate) {
            return $startDate;
        }

        return $startDate . ' - ' . $endDate;
    }
}

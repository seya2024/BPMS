<?php

namespace App\Services;

use App\Models\FinancialYear;
use App\Models\FinancialPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialPeriodService
{
    public function generate(FinancialYear $year): void
    {
        DB::transaction(function () use ($year) {

            // prevent duplicate generation
            if ($year->periods()->exists()) {
                return;
            }

            $startYear = Carbon::parse($year->start_date)->year; // 2026
            $endYearShort = ($startYear + 1) % 100; // 27
            $fyLabel = "{$startYear}/" . str_pad($endYearShort, 2, '0', STR_PAD_LEFT); // 2026/27

            $start = Carbon::parse($year->start_date)->month(7)->day(1);

            $periods = [
                1 => [$start->copy(), $start->copy()->addMonths(3)->subDay()],
                2 => [$start->copy()->addMonths(3), $start->copy()->addMonths(6)->subDay()],
                3 => [$start->copy()->addMonths(6), $start->copy()->addMonths(9)->subDay()],
                4 => [$start->copy()->addMonths(9), $start->copy()->addMonths(12)->subDay()],
            ];

            foreach ($periods as $q => [$from, $to]) {

                FinancialPeriod::create([
                    'financial_year_id' => $year->id,
                    'quarter' => $q,
                    'label' => "Q{$q}-FY-{$fyLabel}",
                    'start_date' => $from->toDateString(),
                    'end_date' => $to->toDateString(),
                    'status' => 'OPEN',
                ]);
            }
        });
    }
}
<?php

namespace App\Services;

use Carbon\Carbon;

class AccountAllocationService
{
    public static function allocate(float $annualTarget, Carbon $startDate, Carbon $endDate): array
    {
        $workingDays = 0;

        $date = $startDate->copy();

        while ($date->lte($endDate)) {
            if ($date->dayOfWeek !== Carbon::SUNDAY) {
                $workingDays++;
            }
            $date->addDay();
        }

        $qtr = round($annualTarget / 4, 2);
        $month = round($annualTarget / 12, 2);
        $week = round($annualTarget / 52, 2);
        $day = round($annualTarget / max($workingDays, 1), 2);

        return [
            'q1_target_accounts' => $qtr,
            'q2_target_accounts' => $qtr,
            'q3_target_accounts' => $qtr,
            'q4_target_accounts' => $qtr,

            'monthly_target_accounts' => $month,
            'weekly_target_accounts' => $week,
            'daily_target_accounts' => $day,
        ];
    }
}
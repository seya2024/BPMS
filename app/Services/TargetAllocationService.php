<?php

namespace App\Services;

use Carbon\Carbon;

class TargetAllocationService
{
    public static function allocate(float $annualTarget, $startDate, $endDate): array
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $workingDays = 0;

        $date = $startDate->copy();

        while ($date->lte($endDate)) {
            if ($date->dayOfWeek !== Carbon::SUNDAY) {
                $workingDays++;
            }
            $date->addDay();
        }

        return [
            'q1_target_amount' => round($annualTarget / 4, 2),
            'q2_target_amount' => round($annualTarget / 4, 2),
            'q3_target_amount' => round($annualTarget / 4, 2),
            'q4_target_amount' => round($annualTarget / 4, 2),

            'monthly_target_amount' => round($annualTarget / 12, 2),
            'weekly_target_amount' => round($annualTarget / 52, 2),
            'daily_target_amount' => round($annualTarget / max($workingDays, 1), 2),
            ''
        ];
    }
}
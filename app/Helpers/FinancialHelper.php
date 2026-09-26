<?php

namespace App\Helpers;

use App\Models\FinancialYear;
use App\Models\FinancialPeriod;
use Carbon\Carbon;

class FinancialHelper
{
    /**
     * Get current Financial Year (based on OPEN or date range)
     */
    public static function currentFY(): ?FinancialYear
    {
        // Priority 1: OPEN FY (banking rule)
        $open = FinancialYear::where('status', 'OPEN')->first();

        if ($open) {
            return $open;
        }

        // Fallback: based on date range
        return FinancialYear::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Get current Financial Period (Quarter)
     */
    public static function currentQuarter(): ?FinancialPeriod
    {
        $fy = self::currentFY();

        if (! $fy) {
            return null;
        }

        return FinancialPeriod::where('financial_year_id', $fy->id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Get current quarter number (1-4)
     */
    public static function currentQuarterNumber(): ?int
    {
        return self::currentQuarter()?->quarter;
    }

    /**
     * Get FY label like FY-2026/27
     */
    public static function currentFYLabel(): ?string
    {
        return self::currentFY()?->name;
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnualPlanResource;
use App\Models\AnnualPlan;
use App\Models\BranchAccountPlan;
use App\Models\BranchDepositPlan;
use App\Models\DailyAccountPerformance;
use App\Models\DailyDepositPerformance;
use App\Models\FinancialPeriod;
use App\Models\FinancialYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FinancialController extends Controller
{
    /**
     * Get all financial years.
     */
    public function financialYears(Request $request): AnonymousResourceCollection
    {
        $financialYears = FinancialYear::with('periods')
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('start_date', 'desc')
            ->paginate($request->per_page ?? 15);

        return AnnualPlanResource::collection($financialYears);
    }

    /**
     * Get the current (open) financial year.
     */
    public function currentFinancialYear(Request $request): JsonResponse
    {
        $currentYear = FinancialYear::where('status', 'OPEN')
            ->with('periods')
            ->first();

        if (! $currentYear) {
            return response()->json([
                'success' => false,
                'message' => 'No open financial year found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $currentYear->id,
                'name' => $currentYear->name,
                'start_date' => $currentYear->start_date,
                'end_date' => $currentYear->end_date,
                'status' => $currentYear->status,
                'opened_at' => $currentYear->opened_at,
                'periods' => $currentYear->periods->map(function ($period) {
                    return [
                        'id' => $period->id,
                        'quarter' => $period->quarter,
                        'label' => $period->label,
                        'start_date' => $period->start_date,
                        'end_date' => $period->end_date,
                        'status' => $period->status,
                    ];
                }),
            ],
        ], 200);
    }

    /**
     * Get all financial periods.
     */
    public function financialPeriods(Request $request): JsonResponse
    {
        $periods = FinancialPeriod::with('financialYear')
            ->when($request->financial_year_id, function ($query) use ($request) {
                $query->where('financial_year_id', $request->financial_year_id);
            })
            ->when($request->quarter, function ($query) use ($request) {
                $query->where('quarter', $request->quarter);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('start_date', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $periods,
        ], 200);
    }

    /**
     * Get all annual plans.
     */
    public function annualPlans(Request $request): AnonymousResourceCollection
    {
        $annualPlans = AnnualPlan::with(['financialYear', 'district', 'creator'])
            ->when($request->financial_year_id, function ($query) use ($request) {
                $query->where('financial_year_id', $request->financial_year_id);
            })
            ->when($request->district_id, function ($query) use ($request) {
                $query->where('district_id', $request->district_id);
            })
            ->when($request->approval_status, function ($query) use ($request) {
                $query->where('approval_status', $request->approval_status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return AnnualPlanResource::collection($annualPlans);
    }

    /**
     * Get a specific annual plan.
     */
    public function annualPlanShow(AnnualPlan $annualPlan): JsonResponse
    {
        $annualPlan->load(['financialYear', 'district', 'creator', 'depositPlans', 'accountPlans']);

        return response()->json([
            'success' => true,
            'data' => new AnnualPlanResource($annualPlan),
        ], 200);
    }

    /**
     * Get all branch deposit plans.
     */
    public function branchDepositPlans(Request $request): JsonResponse
    {
        $plans = BranchDepositPlan::with(['branch', 'annualDepositPlan'])
            ->when($request->branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })
            ->when($request->annual_deposit_plan_id, function ($query) use ($request) {
                $query->where('annual_deposit_plan_id', $request->annual_deposit_plan_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $plans,
        ], 200);
    }

    /**
     * Get all branch account plans.
     */
    public function branchAccountPlans(Request $request): JsonResponse
    {
        $plans = BranchAccountPlan::with(['branch', 'annualAccountPlan'])
            ->when($request->branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })
            ->when($request->annual_account_plan_id, function ($query) use ($request) {
                $query->where('annual_account_plan_id', $request->annual_account_plan_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $plans,
        ], 200);
    }

    /**
     * Get all daily deposit performances.
     */
    public function dailyDepositPerformances(Request $request): JsonResponse
    {
        $performances = DailyDepositPerformance::with('branch')
            ->when($request->branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })
            ->when($request->date_from, function ($query) use ($request) {
                $query->where('business_day', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                $query->where('business_day', '<=', $request->date_to);
            })
            ->orderBy('business_day', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $performances,
        ], 200);
    }

    /**
     * Get all daily account performances.
     */
    public function dailyAccountPerformances(Request $request): JsonResponse
    {
        $performances = DailyAccountPerformance::with('branch')
            ->when($request->branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })
            ->when($request->date_from, function ($query) use ($request) {
                $query->where('business_day', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                $query->where('business_day', '<=', $request->date_to);
            })
            ->orderBy('business_day', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $performances,
        ], 200);
    }
}

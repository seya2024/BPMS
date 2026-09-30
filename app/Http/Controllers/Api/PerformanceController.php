<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PerformanceResource;
use App\Models\Branch;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    /**
     * Get overall performance summary for dashboard.
     */
    public function summary(Request $request): JsonResponse
    {
        $financialYearId = $request->financial_year_id;

        $branches = Branch::with(['district', 'bankingType'])
            ->when($financialYearId, function ($query) use ($financialYearId) {
                $query->whereHas('branchDepositPlans', function ($q) use ($financialYearId) {
                    $q->whereHas('annualDepositPlan', function ($aq) use ($financialYearId) {
                        $aq->where('financial_year_id', $financialYearId);
                    });
                });
            })
            ->get();

        $branchPerformances = $branches->map(function ($branch) {
            $depositPlan = $branch->branchDepositPlans->first();
            $accountPlan = $branch->branchAccountPlans->first();

            $depositTarget = $depositPlan?->annual_target_amount ?? 0;
            $depositActual = $branch->dailyDepositPerformances->sum('total_deposit_amount');
            $depositAchievement = $depositTarget > 0 ? round(($depositActual / $depositTarget) * 100, 2) : 0;

            $accountTarget = $accountPlan?->annual_target_accounts ?? 0;
            $accountActual = $branch->dailyAccountPerformances->sum('new_accounts');
            $accountAchievement = $accountTarget > 0 ? round(($accountActual / $accountTarget) * 100, 2) : 0;

            return [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'branch_code' => $branch->code,
                'district' => $branch->district?->name,
                'deposit_target' => $depositTarget,
                'deposit_actual' => $depositActual,
                'deposit_achievement' => $depositAchievement,
                'account_target' => $accountTarget,
                'account_actual' => $accountActual,
                'account_achievement' => $accountAchievement,
            ];
        });

        $totalBranches = $branches->count();
        $avgDepositAchievement = $branchPerformances->avg('deposit_achievement') ?? 0;
        $avgAccountAchievement = $branchPerformances->avg('account_achievement') ?? 0;

        $topDepositPerformers = $branchPerformances->sortByDesc('deposit_achievement')->take(5)->values();
        $topAccountPerformers = $branchPerformances->sortByDesc('account_achievement')->take(5)->values();

        return response()->json([
            'success' => true,
            'data' => [
                'total_branches' => $totalBranches,
                'average_deposit_achievement' => round($avgDepositAchievement, 2),
                'average_account_achievement' => round($avgAccountAchievement, 2),
                'top_deposit_performers' => $topDepositPerformers,
                'top_account_performers' => $topAccountPerformers,
                'all_branches' => $branchPerformances,
            ],
        ], 200);
    }

    /**
     * Get detailed performance for a specific branch.
     */
    public function branchPerformance(Branch $branch): JsonResponse
    {
        $branch->load([
            'district',
            'bankingType',
            'branchDepositPlans',
            'branchAccountPlans',
            'dailyDepositPerformances' => function ($query) {
                $query->orderBy('business_day', 'desc');
            },
            'dailyAccountPerformances' => function ($query) {
                $query->orderBy('business_day', 'desc');
            },
        ]);

        $depositPlan = $branch->branchDepositPlans->first();
        $accountPlan = $branch->branchAccountPlans->first();

        $depositTarget = $depositPlan?->annual_target_amount ?? 0;
        $depositActual = $branch->dailyDepositPerformances->sum('total_deposit_amount');
        $depositAchievement = $depositTarget > 0 ? round(($depositActual / $depositTarget) * 100, 2) : 0;

        $accountTarget = $accountPlan?->annual_target_accounts ?? 0;
        $accountActual = $branch->dailyAccountPerformances->sum('new_accounts');
        $accountAchievement = $accountTarget > 0 ? round(($accountActual / $accountTarget) * 100, 2) : 0;

        $performanceData = [
            'branch' => [
                'id' => $branch->id,
                'code' => $branch->code,
                'name' => $branch->name,
                'grade' => $branch->grade,
                'district' => $branch->district?->name,
                'bankingType' => $branch->bankingType?->name,
            ],
            'deposit' => [
                'target' => $depositTarget,
                'actual' => $depositActual,
                'achievement_percentage' => $depositAchievement,
                'daily_records' => $branch->dailyDepositPerformances->map(function ($record) {
                    return [
                        'date' => $record->business_day,
                        'total_deposit' => $record->total_deposit_amount,
                        'new_deposit' => $record->new_deposit_amount,
                        'inflow' => $record->deposit_inflow_amount,
                        'outflow' => $record->deposit_outflow_amount,
                        'net_change' => $record->net_deposit_change,
                    ];
                }),
            ],
            'account' => [
                'target' => $accountTarget,
                'actual' => $accountActual,
                'achievement_percentage' => $accountAchievement,
                'daily_records' => $branch->dailyAccountPerformances->map(function ($record) {
                    return [
                        'date' => $record->business_day,
                        'total_accounts' => $record->total_accounts,
                        'active_accounts' => $record->active_accounts,
                        'new_accounts' => $record->new_accounts,
                        'dormant_accounts' => $record->dormant_accounts,
                        'reactivated_accounts' => $record->reactivated_accounts,
                    ];
                }),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $performanceData,
        ], 200);
    }

    /**
     * Get performance summary for a district.
     */
    public function districtPerformance(District $district): JsonResponse
    {
        $branches = Branch::with(['district', 'bankingType', 'branchDepositPlans', 'branchAccountPlans', 'dailyDepositPerformances', 'dailyAccountPerformances'])
            ->where('district_id', $district->id)
            ->get();

        $branchPerformances = $branches->map(function ($branch) {
            $depositPlan = $branch->branchDepositPlans->first();
            $accountPlan = $branch->branchAccountPlans->first();

            $depositTarget = $depositPlan?->annual_target_amount ?? 0;
            $depositActual = $branch->dailyDepositPerformances->sum('total_deposit_amount');
            $depositAchievement = $depositTarget > 0 ? round(($depositActual / $depositTarget) * 100, 2) : 0;

            $accountTarget = $accountPlan?->annual_target_accounts ?? 0;
            $accountActual = $branch->dailyAccountPerformances->sum('new_accounts');
            $accountAchievement = $accountTarget > 0 ? round(($accountActual / $accountTarget) * 100, 2) : 0;

            return [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'branch_code' => $branch->code,
                'grade' => $branch->grade,
                'deposit_target' => $depositTarget,
                'deposit_actual' => $depositActual,
                'deposit_achievement' => $depositAchievement,
                'account_target' => $accountTarget,
                'account_actual' => $accountActual,
                'account_achievement' => $accountAchievement,
            ];
        });

        $totalDepositTarget = $branchPerformances->sum('deposit_target');
        $totalDepositActual = $branchPerformances->sum('deposit_actual');
        $totalAccountTarget = $branchPerformances->sum('account_target');
        $totalAccountActual = $branchPerformances->sum('account_actual');

        return response()->json([
            'success' => true,
            'data' => [
                'district' => [
                    'id' => $district->id,
                    'name' => $district->name,
                    'location_type' => $district->location_type,
                ],
                'summary' => [
                    'total_branches' => $branches->count(),
                    'total_deposit_target' => $totalDepositTarget,
                    'total_deposit_actual' => $totalDepositActual,
                    'deposit_achievement' => $totalDepositTarget > 0 ? round(($totalDepositActual / $totalDepositTarget) * 100, 2) : 0,
                    'total_account_target' => $totalAccountTarget,
                    'total_account_actual' => $totalAccountActual,
                    'account_achievement' => $totalAccountTarget > 0 ? round(($totalAccountActual / $totalAccountTarget) * 100, 2) : 0,
                ],
                'branches' => $branchPerformances,
            ],
        ], 200);
    }
}

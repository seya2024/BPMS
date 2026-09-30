<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BranchController extends Controller
{
    /**
     * Display a listing of branches.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $branches = Branch::with(['district', 'bankingType'])
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            })
            ->when($request->district_id, function ($query) use ($request) {
                $query->where('district_id', $request->district_id);
            })
            ->when($request->banking_type_id, function ($query) use ($request) {
                $query->where('bankingType_id', $request->banking_type_id);
            })
            ->orderBy('name')
            ->paginate($request->per_page ?? 15);

        return BranchResource::collection($branches);
    }

    /**
     * Display the specified branch.
     */
    public function show(Branch $branch): JsonResponse
    {
        $branch->load([
            'district',
            'bankingType',
            'branchDepositPlans',
            'branchAccountPlans',
            'dailyDepositPerformances' => function ($query) {
                $query->orderBy('business_day', 'desc')->limit(30);
            },
            'dailyAccountPerformances' => function ($query) {
                $query->orderBy('business_day', 'desc')->limit(30);
            },
        ]);

        return response()->json([
            'success' => true,
            'data' => new BranchResource($branch),
        ], 200);
    }

    /**
     * Display a listing of districts.
     */
    public function districts(Request $request): JsonResponse
    {
        $districts = District::withCount('branches')
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })
            ->orderBy('name')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $districts,
        ], 200);
    }

    /**
     * Display the specified district with branches.
     */
    public function districtShow(District $district): JsonResponse
    {
        $district->load(['branches' => function ($query) {
            $query->with('bankingType')->orderBy('name');
        }]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $district->id,
                'name' => $district->name,
                'location_type' => $district->location_type,
                'branches_count' => $district->branches->count(),
                'branches' => $district->branches->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'code' => $branch->code,
                        'name' => $branch->name,
                        'grade' => $branch->grade,
                        'bankingType' => $branch->bankingType ? [
                            'id' => $branch->bankingType->id,
                            'name' => $branch->bankingType->name,
                        ] : null,
                    ];
                }),
                'created_at' => $district->created_at,
                'updated_at' => $district->updated_at,
            ],
        ], 200);
    }
}

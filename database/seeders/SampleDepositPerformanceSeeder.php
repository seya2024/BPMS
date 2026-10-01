<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\DailyDepositPerformance;
use App\Models\DailyDepositPerformanceDetail;
use App\Models\BusinessSegment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SampleDepositPerformanceSeeder extends Seeder
{
    public function run(): void
    {
        $businessDay = now()->subDay()->toDateString();
        
        // Get branches for Jimma district (district_id = 1)
        $branches = Branch::where('district_id', 1)->orderBy('name')->get();
        
        if ($branches->isEmpty()) {
            $this->command->warn('No branches found for district_id=1');
            return;
        }

        $segments = BusinessSegment::whereIn('name', ['Corporate', 'Retail', 'MSME'])
            ->orderByRaw("CASE name WHEN 'Corporate' THEN 1 WHEN 'Retail' THEN 2 WHEN 'MSME' THEN 3 ELSE 4 END")
            ->get(['id', 'name'])
            ->keyBy('name');

        $this->command->info("Seeding deposit performance for {$branches->count()} branches on {$businessDay}");

        // Sample data patterns for different branches
        $sampleData = [
            // Branch name => [Conventional segments, IFB segments]
            'Jimma Branch' => [
                'conventional' => ['Corporate' => 2450000.00, 'Retail' => 1250000.00, 'MSME' => 875000.00],
                'ifb' => ['Corporate' => 1800000.00, 'Retail' => 950000.00, 'MSME' => 650000.00],
            ],
            'Agaro Branch' => [
                'conventional' => ['Corporate' => 1850000.00, 'Retail' => 980000.00, 'MSME' => 720000.00],
                'ifb' => ['Corporate' => 1400000.00, 'Retail' => 750000.00, 'MSME' => 520000.00],
            ],
            'Limmugenet Branch' => [
                'conventional' => ['Corporate' => 2100000.00, 'Retail' => 1100000.00, 'MSME' => 780000.00],
                'ifb' => ['Corporate' => 1650000.00, 'Retail' => 880000.00, 'MSME' => 590000.00],
            ],
            'Yebu Branch' => [
                'conventional' => ['Corporate' => 1650000.00, 'Retail' => 890000.00, 'MSME' => 650000.00],
                'ifb' => ['Corporate' => 1200000.00, 'Retail' => 650000.00, 'MSME' => 450000.00],
            ],
            'Bilida Outlet' => [
                'conventional' => ['Corporate' => 980000.00, 'Retail' => 520000.00, 'MSME' => 380000.00],
                'ifb' => ['Corporate' => 750000.00, 'Retail' => 410000.00, 'MSME' => 280000.00],
            ],
            'Al-nur IFB' => [
                'conventional' => ['Corporate' => 0.00, 'Retail' => 0.00, 'MSME' => 0.00],
                'ifb' => ['Corporate' => 2200000.00, 'Retail' => 1150000.00, 'MSME' => 820000.00],
            ],
            'Gecha Branch' => [
                'conventional' => ['Corporate' => 1450000.00, 'Retail' => 780000.00, 'MSME' => 560000.00],
                'ifb' => ['Corporate' => 980000.00, 'Retail' => 540000.00, 'MSME' => 380000.00],
            ],
            'Bedele Branch' => [
                'conventional' => ['Corporate' => 1850000.00, 'Retail' => 990000.00, 'MSME' => 710000.00],
                'ifb' => ['Corporate' => 1350000.00, 'Retail' => 720000.00, 'MSME' => 500000.00],
            ],
            'Furisa Abawoga' => [
                'conventional' => ['Corporate' => 1320000.00, 'Retail' => 710000.00, 'MSME' => 510000.00],
                'ifb' => ['Corporate' => 890000.00, 'Retail' => 480000.00, 'MSME' => 340000.00],
            ],
            'Chora Outlet' => [
                'conventional' => ['Corporate' => 920000.00, 'Retail' => 490000.00, 'MSME' => 350000.00],
                'ifb' => ['Corporate' => 650000.00, 'Retail' => 350000.00, 'MSME' => 240000.00],
            ],
        ];

        $saved = 0;

        DB::transaction(function () use ($branches, $businessDay, $sampleData, $segments, &$saved) {
            foreach ($branches as $branch) {
                $branchData = $sampleData[$branch->name] ?? null;
                
                if (!$branchData) {
                    // Generate random data for branches not in sample
                    $branchData = [
                        'conventional' => [
                            'Corporate' => rand(800000, 2500000) / 100 * 100,
                            'Retail' => rand(400000, 1300000) / 100 * 100,
                            'MSME' => rand(300000, 900000) / 100 * 100,
                        ],
                        'ifb' => [
                            'Corporate' => rand(600000, 1800000) / 100 * 100,
                            'Retail' => rand(350000, 1000000) / 100 * 100,
                            'MSME' => rand(250000, 700000) / 100 * 100,
                        ],
                    ];
                }

                // Calculate totals
                $conventionalTotal = array_sum($branchData['conventional']);
                $ifbTotal = array_sum($branchData['ifb']);
                $grandTotal = $conventionalTotal + $ifbTotal;

                // Create/update main record
                DailyDepositPerformance::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'business_day' => $businessDay,
                    ],
                    [
                        'total_deposit_amount' => $grandTotal,
                        'new_deposit_amount' => $conventionalTotal * 0.15, // 15% new
                        'deposit_inflow_amount' => $grandTotal * 0.12,
                        'deposit_outflow_amount' => $grandTotal * 0.08,
                        'net_deposit_change' => $grandTotal * 0.04,
                        'remarks' => 'Batch seeded sample data',
                    ],
                );

                // A branch belongs to exactly ONE banking type. Only seed the segment
                // rows for the branch's own type, otherwise the opposite set of columns
                // fills with figures the branch can never actually report.
                $bankingTypeId = $branch->bankingType_id;
                $bankingKey = $bankingTypeId === 2 ? 'ifb' : 'conventional';
                $bankingLabel = $bankingTypeId === 2 ? 'IFB' : 'Conventional';

                foreach (['Corporate', 'Retail', 'MSME'] as $segmentName) {
                    $segment = $segments->get($segmentName);
                    if ($segment) {
                        DailyDepositPerformanceDetail::updateOrCreate(
                            [
                                'branch_id' => $branch->id,
                                'business_day' => $businessDay,
                                'banking_type_id' => $bankingTypeId,
                                'business_segment_id' => $segment->id,
                            ],
                            [
                                'amount' => $branchData[$bankingKey][$segmentName] ?? 0,
                                'remarks' => "{$bankingLabel} - {$segmentName}",
                            ],
                        );
                    }
                }

                $saved++;
            }
        });

        $this->command->info("Successfully seeded {$saved} branches with deposit performance data for {$businessDay}");
        $this->command->info("Check at: /admin/daily-deposit-performances/batch");
    }
}
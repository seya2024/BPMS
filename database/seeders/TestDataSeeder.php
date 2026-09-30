<?php

namespace Database\Seeders;

use App\Models\AnnualAccountPlan;
use App\Models\AnnualDepositPlan;
use App\Models\AnnualPlan;
use App\Models\BankingType;
use App\Models\Branch;
use App\Models\BusinessSegment;
use App\Models\District;
use App\Models\FinancialPeriod;
use App\Models\FinancialYear;
use App\Models\Kpi;
use App\Models\KpiCategory;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure super_admin role exists
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Create or update the super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'seidm2031@gmail.com'],
            [
                'name' => 'Seid Mohammed',
                'password' => Hash::make('123'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['super_admin']);

        // Ensure districts exist (from SQL dump)
        $districtsData = [
            ['id' => 1, 'name' => 'Jimma', 'location_type' => 'Upcountry'],
            ['id' => 2, 'name' => 'South West', 'location_type' => 'Upcountry'],
            ['id' => 3, 'name' => 'Nekemte', 'location_type' => 'Upcountry'],
            ['id' => 4, 'name' => 'Hawasa', 'location_type' => 'Upcountry'],
            ['id' => 5, 'name' => 'Adama', 'location_type' => 'Upcountry'],
            ['id' => 6, 'name' => 'Dire Dawa', 'location_type' => 'Upcountry'],
            ['id' => 7, 'name' => 'Mekelle', 'location_type' => 'Upcountry'],
            ['id' => 8, 'name' => 'Dessie', 'location_type' => 'Upcountry'],
            ['id' => 9, 'name' => 'Bahir Dar', 'location_type' => 'Upcountry'],
            ['id' => 10, 'name' => 'North Addis', 'location_type' => 'City'],
            ['id' => 11, 'name' => 'East Addis', 'location_type' => 'City'],
            ['id' => 12, 'name' => 'West Addis', 'location_type' => 'City'],
            ['id' => 13, 'name' => 'South Addis', 'location_type' => 'City'],
            ['id' => 14, 'name' => 'Wolaita', 'location_type' => 'Upcountry'],
            ['id' => 15, 'name' => 'Head Office', 'location_type' => 'City'],
        ];

        foreach ($districtsData as $data) {
            District::updateOrCreate(['id' => $data['id']], $data);
        }

        // Banking Types
        $bankingTypesData = [
            ['id' => 1, 'name' => 'Conventional Banking'],
            ['id' => 2, 'name' => 'Islamic Banking (IFB)'],
        ];

        foreach ($bankingTypesData as $data) {
            BankingType::updateOrCreate(['id' => $data['id']], $data);
        }

        // Business Segments
        $businessSegmentsData = [
            ['id' => 1, 'name' => 'MSME', 'description' => 'MSME (Micro, Small & Medium Enterprises)'],
            ['id' => 2, 'name' => 'Retail', 'description' => 'Retail (Personal Banking)\nMeaning: Individual customers, not businesses.'],
            ['id' => 3, 'name' => 'Corporate', 'description' => 'Large, structured companies with formal governance and high financial capacity.'],
        ];

        foreach ($businessSegmentsData as $data) {
            BusinessSegment::updateOrCreate(['id' => $data['id']], $data);
        }

        // Branches (from SQL dump - 31 branches)
        $branchesData = [
            ['id' => 1, 'code' => '21', 'name' => 'Jimma Branch', 'grade' => 'II', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 2, 'code' => '22', 'name' => 'Agaro Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 3, 'code' => '23', 'name' => 'Limmugenet Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 4, 'code' => 'YB', 'name' => 'Yebu Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 5, 'code' => 'BL', 'name' => 'Bilida Outlet', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 6, 'code' => 'AL', 'name' => 'Al-nur IFB', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 2],
            ['id' => 7, 'code' => 'CH', 'name' => 'Gecha Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 8, 'code' => 'BD', 'name' => 'Bedele Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 9, 'code' => 'FR', 'name' => 'Furisa Abawoga', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 10, 'code' => 'CHR', 'name' => 'Chora Outlet', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 11, 'code' => 'YY', 'name' => 'Yayo Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 12, 'code' => 'MT', 'name' => 'Mettu Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 13, 'code' => 'GH', 'name' => 'Gechi Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 14, 'code' => 'MSH', 'name' => 'Masha Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 15, 'code' => 'MTI', 'name' => 'Meti Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 16, 'code' => 'YR', 'name' => 'Yeri Outlet', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 17, 'code' => 'SHE', 'name' => 'Shebe Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 18, 'code' => 'CHD', 'name' => 'Chida Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 19, 'code' => 'DNB', 'name' => 'Deneba Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 20, 'code' => 'SJ', 'name' => 'Saja Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 21, 'code' => 'SK', 'name' => 'Sokoru Outlet', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 22, 'code' => 'TLY', 'name' => 'Tollay Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 23, 'code' => 'SLA', 'name' => 'SilkAmba Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 24, 'code' => 'ALF', 'name' => 'Alif Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 2],
            ['id' => 26, 'code' => 'HIR', 'name' => 'Hirmata Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 27, 'code' => 'MNR', 'name' => 'Meneharia Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 28, 'code' => 'IQR', 'name' => 'IFB - Iqra Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 2],
            ['id' => 29, 'code' => 'ABJ', 'name' => 'Abajifar Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 30, 'code' => 'ABJS', 'name' => 'Abajifar Outlet', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
            ['id' => 31, 'code' => 'FRJ', 'name' => 'Ferenji Arada Branch', 'grade' => 'I', 'district_id' => 1, 'bankingType_id' => 1],
        ];

        foreach ($branchesData as $data) {
            Branch::updateOrCreate(['id' => $data['id']], $data);
        }

        // Financial Years
        $financialYear = FinancialYear::updateOrCreate(
            ['id' => 5],
            [
                'name' => 'FY2026/27',
                'start_date' => '2026-07-01',
                'end_date' => '2027-06-30',
                'status' => 'OPEN',
            ]
        );

        // Financial Periods
        $periodsData = [
            ['id' => 28, 'financial_year_id' => 5, 'quarter' => 1, 'label' => 'Q1-FY-2026/27', 'start_date' => '2026-07-01', 'end_date' => '2026-09-30', 'status' => 'OPEN'],
            ['id' => 29, 'financial_year_id' => 5, 'quarter' => 2, 'label' => 'Q2-FY-2026/27', 'start_date' => '2026-10-01', 'end_date' => '2026-12-31', 'status' => 'OPEN'],
            ['id' => 30, 'financial_year_id' => 5, 'quarter' => 3, 'label' => 'Q3-FY-2026/27', 'start_date' => '2027-01-01', 'end_date' => '2027-03-31', 'status' => 'OPEN'],
            ['id' => 31, 'financial_year_id' => 5, 'quarter' => 4, 'label' => 'Q4-FY-2026/27', 'start_date' => '2027-04-01', 'end_date' => '2027-06-30', 'status' => 'OPEN'],
        ];

        foreach ($periodsData as $data) {
            FinancialPeriod::updateOrCreate(['id' => $data['id']], $data);
        }

        // Annual Plans
        $annualPlansData = [
            ['id' => 4, 'financial_year_id' => 5, 'district_id' => 1, 'deposit' => 1000000000.00, 'account' => 50000, 'supperappsubscription' => 20000, 'created_by' => 1],
            ['id' => 5, 'financial_year_id' => 5, 'district_id' => 9, 'deposit' => 500000000.00, 'account' => 30000, 'supperappsubscription' => 20000, 'created_by' => 1],
        ];

        foreach ($annualPlansData as $data) {
            AnnualPlan::updateOrCreate(['id' => $data['id']], $data);
        }

        // Annual Account Plans
        AnnualAccountPlan::updateOrCreate(
            ['id' => 3],
            [
                'annual_plan_id' => 4,
                'annual_target_accounts' => 100000,
                'q1_target_accounts' => 25000,
                'q2_target_accounts' => 25000,
                'q3_target_accounts' => 25000,
                'q4_target_accounts' => 25000,
                'monthly_target_accounts' => 8333,
                'weekly_target_accounts' => 1923,
                'daily_target_accounts' => 319,
            ]
        );

        // Annual Deposit Plans
        AnnualDepositPlan::updateOrCreate(
            ['id' => 6],
            [
                'annual_plan_id' => 4,
                'annual_target_amount' => 200000000.00,
                'q1_target_amount' => 50000000.00,
                'q2_target_amount' => 50000000.00,
                'q3_target_amount' => 50000000.00,
                'q4_target_amount' => 50000000.00,
                'daily_target_amount' => 638977.64,
                'monthly_target_amount' => 16666666.67,
                'weekly_target_amount' => 3846153.85,
            ]
        );

        // KPI Categories
        $kpiCategoriesData = [
            ['id' => 1, 'name' => 'Customer Acquisition'],
            ['id' => 2, 'name' => 'Deposit Mobilization'],
            ['id' => 3, 'name' => 'Digital Banking'],
            ['id' => 4, 'name' => 'Service Quality'],
        ];

        foreach ($kpiCategoriesData as $data) {
            KpiCategory::updateOrCreate(['id' => $data['id']], $data);
        }

        // KPIs
        $kpisData = [
            ['id' => 1, 'name' => 'Account Opening', 'category_id' => 1, 'unit' => 'count', 'calculation_method' => 'Existing plus new Active account'],
            ['id' => 2, 'name' => 'Deposit', 'category_id' => 2, 'unit' => 'amount', 'calculation_method' => 'Last Jun plus new deposit'],
        ];

        foreach ($kpisData as $data) {
            Kpi::updateOrCreate(['id' => $data['id']], $data);
        }

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Super admin: seidm2031@gmail.com / 123');
    }
}
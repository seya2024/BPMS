<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGroup;
use Database\Seeders\SampleDepositPerformanceSeeder;
use Database\Seeders\TestDataSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            Roles\RolesAndPermissionsSeeder::class,
            TestDataSeeder::class,
            SampleDepositPerformanceSeeder::class,
        ]);

        // Create user groups
        $adminGroup = UserGroup::firstOrCreate(
            ['name' => 'Administrators'],
            [
                'description' => 'System administrators with full access',
                'color' => '#EF4444',
                'is_active' => true,
            ]
        );

        $managerGroup = UserGroup::firstOrCreate(
            ['name' => 'Managers'],
            [
                'description' => 'District and branch managers',
                'color' => '#3B82F6',
                'is_active' => true,
            ]
        );

        $branchGroup = UserGroup::firstOrCreate(
            ['name' => 'Branch Managers'],
            [
                'description' => 'Branch-level managers',
                'color' => '#10B981',
                'is_active' => true,
            ]
        );

        $viewerGroup = UserGroup::firstOrCreate(
            ['name' => 'Viewers'],
            [
                'description' => 'Read-only users',
                'color' => '#6B7280',
                'is_active' => true,
            ]
        );

        // Assign permissions to groups
        $adminGroup->syncPermissions([
            'view dashboard', 'view reports',
            'view branches', 'create branches', 'edit branches', 'delete branches',
            'view districts', 'create districts', 'edit districts', 'delete districts',
            'view financial years', 'create financial years', 'edit financial years', 'delete financial years', 'close financial years',
            'view financial periods', 'create financial periods', 'edit financial periods', 'delete financial periods',
            'view annual plans', 'create annual plans', 'edit annual plans', 'delete annual plans',
            'submit annual plans', 'approve annual plans', 'reject annual plans',
            'view branch plans', 'create branch plans', 'edit branch plans', 'delete branch plans',
            'submit branch plans', 'approve branch plans', 'reject branch plans',
            'view performances', 'create performances', 'edit performances', 'delete performances',
            'view kpis', 'create kpis', 'edit kpis', 'delete kpis',
            'view settings', 'edit settings',
            'view users', 'create users', 'edit users', 'delete users',
            'export data',
        ]);

        $managerGroup->syncPermissions([
            'view dashboard', 'view reports',
            'view branches', 'view districts',
            'view financial years', 'view financial periods',
            'view annual plans', 'create annual plans', 'edit annual plans',
            'submit annual plans',
            'view branch plans', 'create branch plans', 'edit branch plans',
            'submit branch plans',
            'view performances', 'create performances', 'edit performances',
            'view kpis', 'view users', 'export data',
        ]);

        $branchGroup->syncPermissions([
            'view dashboard', 'view branches', 'view performances',
            'create performances', 'edit performances', 'view reports',
        ]);

        $viewerGroup->syncPermissions([
            'view dashboard', 'view branches', 'view districts',
            'view financial years', 'view financial periods',
            'view annual plans', 'view branch plans', 'view performances',
            'view kpis', 'view reports',
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@bpms.com'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('password'),
                'group_id' => $adminGroup->id,
            ]
        );
        $admin->assignRole('admin');

        // Create manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@bpms.com'],
            [
                'name' => 'District Manager',
                'password' => bcrypt('password'),
                'group_id' => $managerGroup->id,
            ]
        );
        $manager->assignRole('manager');

        // Create branch manager user
        $branchManager = User::firstOrCreate(
            ['email' => 'branch@bpms.com'],
            [
                'name' => 'Branch Manager',
                'password' => bcrypt('password'),
                'group_id' => $branchGroup->id,
            ]
        );
        $branchManager->assignRole('branch_manager');

        // Create viewer user
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@bpms.com'],
            [
                'name' => 'Viewer',
                'password' => bcrypt('password'),
                'group_id' => $viewerGroup->id,
            ]
        );
        $viewer->assignRole('viewer');
    }
}

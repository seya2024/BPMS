<?php

namespace Database\Seeders\Roles;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard
            'view dashboard',
            'view reports',

            // Branches
            'view branches',
            'create branches',
            'edit branches',
            'delete branches',

            // Districts
            'view districts',
            'create districts',
            'edit districts',
            'delete districts',

            // Financial Years
            'view financial years',
            'create financial years',
            'edit financial years',
            'delete financial years',
            'close financial years',

            // Financial Periods
            'view financial periods',
            'create financial periods',
            'edit financial periods',
            'delete financial periods',

            // Annual Plans
            'view annual plans',
            'create annual plans',
            'edit annual plans',
            'delete annual plans',
            'submit annual plans',
            'approve annual plans',
            'reject annual plans',

            // Branch Plans
            'view branch plans',
            'create branch plans',
            'edit branch plans',
            'delete branch plans',
            'submit branch plans',
            'approve branch plans',
            'reject branch plans',

            // Daily Performances
            'view performances',
            'create performances',
            'edit performances',
            'delete performances',

            // KPI
            'view kpis',
            'create kpis',
            'edit kpis',
            'delete kpis',

            // Settings
            'view settings',
            'edit settings',

            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Export
            'export data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo([
            'view dashboard',
            'view reports',
            'view branches',
            'view districts',
            'view financial years',
            'view financial periods',
            'view annual plans',
            'view branch plans',
            'view performances',
            'view kpis',
            'view users',
            'export data',
            'create annual plans',
            'edit annual plans',
            'submit annual plans',
            'create branch plans',
            'edit branch plans',
            'submit branch plans',
            'create performances',
            'edit performances',
        ]);

        $branchManagerRole = Role::firstOrCreate(['name' => 'branch_manager', 'guard_name' => 'web']);
        $branchManagerRole->givePermissionTo([
            'view dashboard',
            'view branches',
            'view performances',
            'create performances',
            'edit performances',
            'view reports',
        ]);

        $viewerRole = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewerRole->givePermissionTo([
            'view dashboard',
            'view branches',
            'view districts',
            'view financial years',
            'view financial periods',
            'view annual plans',
            'view branch plans',
            'view performances',
            'view kpis',
            'view reports',
        ]);

        $this->command->info('Roles and permissions created successfully.');
    }
}

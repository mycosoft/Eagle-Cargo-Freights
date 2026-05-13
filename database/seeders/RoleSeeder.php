<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Clients
            'view clients',
            'manage clients',
            'create clients',
            'edit clients',
            'delete clients',

            // Shipments
            'view shipments',
            'manage shipments',
            'create shipments',
            'edit shipments',
            'delete shipments',

            // Air Cargo
            'view air cargo',
            'view air cargo dashboard',
            'create air cargo',
            'edit air cargo',
            'delete air cargo',

            // Sea Cargo
            'view sea cargo',
            'view sea cargo dashboard',
            'create sea cargo',
            'edit sea cargo',
            'delete sea cargo',

            // Batches
            'view batches',
            'create batches',
            'edit batches',
            'delete batches',

            // Invoices
            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',

            // Payments
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',

            // Expenses
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
            'approve expenses',

            // Expense Categories
            'view expense categories',
            'create expense categories',
            'edit expense categories',
            'delete expense categories',

            // Transactions
            'view transactions',
            'create transactions',
            'delete transactions',

            // Reports
            'view reports',
            'view revenue reports',
            'view outstanding balance',
            'view paid invoices',

            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Roles
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Notifications
            'send notifications',
            'send bulk messages',
            'send broadcast',

            // Settings
            'manage settings',

            // Tracking
            'manage tracking',

            // Status Updates
            'manage status updates',

            // Activity Logs
            'view activity logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->syncPermissions([
            'view clients',
            'create clients',
            'edit clients',

            'view shipments',
            'create shipments',
            'edit shipments',

            'view air cargo',
            'view air cargo dashboard',
            'create air cargo',
            'edit air cargo',

            'view sea cargo',
            'view sea cargo dashboard',
            'create sea cargo',
            'edit sea cargo',

            'manage status updates',

            'view batches',
            'create batches',
            'edit batches',

            'view invoices',
            'edit invoices',

            'view payments',
            'create payments',

            'view expenses',
            'create expenses',
            'edit expenses',

            'view transactions',
            'create transactions',

            'view reports',

            'send notifications',
            'send bulk messages',
            'send broadcast',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Admin role: Full access to all permissions');
        $this->command->info('Staff role: View/Create/Edit for most resources, no delete access');
    }
}

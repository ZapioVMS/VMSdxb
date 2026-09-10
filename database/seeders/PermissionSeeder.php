<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'visitors.view_all', 'module' => 'visitors', 'description' => 'View all visitors'],
            ['name' => 'visitors.checkin', 'module' => 'visitors', 'description' => 'Check-in and Check-out visitors'],
            ['name' => 'visitors.preregister', 'module' => 'visitors', 'description' => 'Pre-register visitors'],
            ['name' => 'visitors.print_badges', 'module' => 'visitors', 'description' => 'Print visitor badges'],
            ['name' => 'security.manage_watchlist', 'module' => 'security', 'description' => 'Manage security watchlist'],
            ['name' => 'security.block_visitors', 'module' => 'security', 'description' => 'Block visitors on watchlist match'],
            ['name' => 'security.trigger_evacuation', 'module' => 'security', 'description' => 'Trigger emergency evacuation'],
            ['name' => 'employees.view', 'module' => 'employees', 'description' => 'View staff directory'],
            ['name' => 'employees.edit', 'module' => 'employees', 'description' => 'Add or edit staff profiles'],
            ['name' => 'employees.delete', 'module' => 'employees', 'description' => 'Delete staff profiles'],
            ['name' => 'system.view_reports', 'module' => 'system', 'description' => 'View analytics and export reports'],
            ['name' => 'system.manage_settings', 'module' => 'system', 'description' => 'Manage system settings'],
            ['name' => 'system.manage_users', 'module' => 'system', 'description' => 'Manage users and roles'],
            ['name' => 'system.manage_nda', 'module' => 'system', 'description' => 'Manage NDA templates'],
            ['name' => 'system.access_billing', 'module' => 'system', 'description' => 'Access billing and subscriptions'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }
    }
}

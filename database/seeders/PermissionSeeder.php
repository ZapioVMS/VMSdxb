<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'All Permissions', 'module' => 'System', 'description' => 'Full access to everything'],
            ['name' => 'Manage Users', 'module' => 'System', 'description' => 'Create and edit users'],
            ['name' => 'Billing', 'module' => 'System', 'description' => 'Access billing and invoices'],
            ['name' => 'Multi-tenant', 'module' => 'System', 'description' => 'Manage multiple tenants'],
            ['name' => 'Visitors', 'module' => 'Access', 'description' => 'Manage visitors'],
            ['name' => 'Employees', 'module' => 'Access', 'description' => 'Manage employees'],
            ['name' => 'Reports', 'module' => 'Insights', 'description' => 'View reports and logs'],
            ['name' => 'Settings', 'module' => 'System', 'description' => 'System configurations'],
            ['name' => 'Watchlist', 'module' => 'Security', 'description' => 'Manage security watchlists'],
            ['name' => 'NDA', 'module' => 'Compliance', 'description' => 'Manage NDA agreements'],
        ];

        foreach ($permissions as $p) {
            $key = strtolower(str_replace([' ', '-'], '_', $p['name']));
            Permission::updateOrCreate(['name' => $p['name']], [
                'name' => $p['name'],
                'module' => $p['module'],
                'description' => $p['description'],
                'key' => $key,
                'action' => $key,
                'is_active' => true,
            ]);
        }
    }
}

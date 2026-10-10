<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Organization;
use Carbon\Carbon;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::firstOrCreate(
            ['name' => 'Zapio Default'],
            ['timezone' => 'UTC']
        );

        $now = Carbon::now();

        // 1. Define Permissions
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
            DB::table('permissions')->updateOrInsert(
                ['name' => $p['name']],
                [
                    'key' => $key,
                    'module' => $p['module'],
                    'action' => $key,
                    'description' => $p['description'],
                    'is_active' => true,
                ]
            );
        }

        $allPerms = DB::table('permissions')->pluck('id', 'name')->toArray();

        // 2. Define Roles
        $roles = [
            'super_admin' => ['name' => 'Super Admin', 'description' => 'Platform administration', 'protected' => true],
            'admin' => ['name' => 'Admin', 'description' => 'Visitor employee location settings', 'protected' => true],
            'manager' => ['name' => 'Manager', 'description' => 'Manage scoped visitors pre-registrations', 'protected' => true],
            'security' => ['name' => 'Security', 'description' => 'Identity verification check-in watchlist', 'protected' => true],
            'receptionist' => ['name' => 'Receptionist', 'description' => 'Walk-in registration check-in', 'protected' => true],
            'host' => ['name' => 'Host', 'description' => 'Create invitations approve own arrivals', 'protected' => true],
            'viewer' => ['name' => 'Viewer', 'description' => 'Read visitor logs dashboards', 'protected' => true],
        ];

        foreach ($roles as $key => $r) {
            DB::table('roles')->updateOrInsert(
                ['organization_id' => $org->id, 'role_key' => $key],
                [
                    'name' => $r['name'],
                    'description' => $r['description'],
                    'is_protected' => $r['protected'],
                    'status' => 'active',
                ]
            );
        }

        $allRoles = DB::table('roles')->where('organization_id', $org->id)->pluck('id', 'role_key')->toArray();

        // 3. Map Role Permissions
        $matrix = [
            'super_admin' => [
                'All Permissions' => 'full', 'Manage Users' => 'full', 'Billing' => 'full', 'Multi-tenant' => 'full',
                'Visitors' => 'full', 'Employees' => 'full', 'Reports' => 'full', 'Settings' => 'full',
                'Watchlist' => 'full', 'NDA' => 'full',
            ],
            'admin' => [
                'All Permissions' => 'none', 'Manage Users' => 'full', 'Billing' => 'none', 'Multi-tenant' => 'full',
                'Visitors' => 'full', 'Employees' => 'full', 'Reports' => 'full', 'Settings' => 'full',
                'Watchlist' => 'full', 'NDA' => 'full',
            ],
            'manager' => [
                'All Permissions' => 'none', 'Manage Users' => 'none', 'Billing' => 'none', 'Multi-tenant' => 'none',
                'Visitors' => 'full', 'Employees' => 'none', 'Reports' => 'full', 'Settings' => 'none',
                'Watchlist' => 'none', 'NDA' => 'none',
            ],
        ];

        foreach ($allRoles as $rKey => $rId) {
            DB::table('role_permissions')->where('role_id', $rId)->delete();
            $roleMatrix = $matrix[$rKey] ?? [];
            
            $inserts = [];
            foreach ($roleMatrix as $pName => $accessLevel) {
                if (isset($allPerms[$pName])) {
                    $inserts[] = [
                        'organization_id' => $org->id,
                        'role_id' => $rId,
                        'permission_id' => $allPerms[$pName],
                        'access_level' => $accessLevel
                    ];
                }
            }
            if (!empty($inserts)) {
                DB::table('role_permissions')->insert($inserts);
            }
        }
        
        $superAdminUser = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@zapio.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'organization_id' => $org->id,
            ]
        );
        
        DB::table('user_roles')->updateOrInsert(
            ['organization_id' => $org->id, 'user_id' => $superAdminUser->id, 'role_id' => $allRoles['super_admin']],
            ['assigned_at' => $now]
        );
        
        DB::table('user_scopes')->updateOrInsert(
            ['organization_id' => $org->id, 'user_id' => $superAdminUser->id, 'scope_type' => 'platform']
        );
    }
}

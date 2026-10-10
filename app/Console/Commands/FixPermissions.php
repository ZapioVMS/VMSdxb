<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixPermissions extends Command
{
    protected $signature = 'app:fix-permissions';
    protected $description = 'Globally reset and fix all roles and permissions across all organizations';

    public function handle()
    {
        $this->info('Cleaning up old permissions...');
        
        // Universally safe delete (works on both MySQL and PostgreSQL without foreign key issues)
        DB::table('role_permissions')->delete();
        DB::table('permissions')->delete();

        $this->info('Inserting simplified permissions...');
        $simplifiedPermissions = [
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

        $permIds = [];
        foreach ($simplifiedPermissions as $perm) {
            $id = DB::table('permissions')->insertGetId([
                'name' => $perm['name'],
                'module' => $perm['module'],
                'description' => $perm['description'],
                'key' => strtolower(str_replace([' ', '-'], '_', $perm['name'])),
                'action' => strtolower(str_replace([' ', '-'], '_', $perm['name'])),
                'is_active' => true,
            ]);
            $permIds[] = $id;
        }

        $this->info('Re-mapping roles for all organizations...');
        
        // Super Admins get everything
        $superAdmins = DB::table('roles')->where('name', 'Super Admin')->get();
        foreach ($superAdmins as $sa) {
            foreach ($permIds as $pId) {
                DB::table('role_permissions')->insert([
                    'organization_id' => $sa->organization_id,
                    'role_id' => $sa->id,
                    'permission_id' => $pId,
                    'access_level' => 'full'
                ]);
            }
        }

        // Admins get everything except Billing and All Permissions
        $admins = DB::table('roles')->where('name', 'Admin')->get();
        foreach ($admins as $admin) {
            foreach ($permIds as $index => $pId) {
                $name = $simplifiedPermissions[$index]['name'];
                if (in_array($name, ['Manage Users', 'Multi-tenant', 'Visitors', 'Employees', 'Reports', 'Settings', 'Watchlist', 'NDA'])) {
                    DB::table('role_permissions')->insert([
                        'organization_id' => $admin->organization_id,
                        'role_id' => $admin->id,
                        'permission_id' => $pId,
                        'access_level' => 'full'
                    ]);
                } elseif (in_array($name, ['All Permissions', 'Billing'])) {
                    DB::table('role_permissions')->insert([
                        'organization_id' => $admin->organization_id,
                        'role_id' => $admin->id,
                        'permission_id' => $pId,
                        'access_level' => 'none'
                    ]);
                }
            }
        }

        // Managers get specific access
        $managers = DB::table('roles')->where('name', 'Manager')->get();
        foreach ($managers as $manager) {
            foreach ($permIds as $index => $pId) {
                $name = $simplifiedPermissions[$index]['name'];
                if (in_array($name, ['Visitors', 'Reports'])) {
                    DB::table('role_permissions')->insert([
                        'organization_id' => $manager->organization_id,
                        'role_id' => $manager->id,
                        'permission_id' => $pId,
                        'access_level' => 'full'
                    ]);
                } elseif (in_array($name, ['Settings', 'Billing', 'All Permissions', 'Manage Users'])) {
                    DB::table('role_permissions')->insert([
                        'organization_id' => $manager->organization_id,
                        'role_id' => $manager->id,
                        'permission_id' => $pId,
                        'access_level' => 'none'
                    ]);
                }
            }
        }

        $this->info('Permissions successfully fixed across all organizations!');
    }
}

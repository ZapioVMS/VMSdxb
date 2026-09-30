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
        // Ensure a default organization exists for seeding built-in roles
        $org = Organization::firstOrCreate(
            ['name' => 'Zapio Default'],
            ['timezone' => 'UTC']
        );

        $now = Carbon::now();

        // 1. Define Permissions
        $permissions = [
            // Visitor Permissions
            ['key' => 'visitors.view_all', 'module' => 'visitors', 'action' => 'view_all', 'description' => 'View all visitors'],
            ['key' => 'visitors.check_in_out', 'module' => 'visitors', 'action' => 'check_in_out', 'description' => 'Check-in and check-out'],
            ['key' => 'visitors.pre_register', 'module' => 'visitors', 'action' => 'pre_register', 'description' => 'Pre-register visitors'],
            ['key' => 'visitors.print_badges', 'module' => 'visitors', 'action' => 'print_badges', 'description' => 'Print badges'],

            // Security Permissions
            ['key' => 'security.manage_watchlist', 'module' => 'security', 'action' => 'manage_watchlist', 'description' => 'Manage watchlist'],
            ['key' => 'security.block_visitors', 'module' => 'security', 'action' => 'block_visitors', 'description' => 'Block visitors'],
            ['key' => 'security.trigger_evacuation', 'module' => 'security', 'action' => 'trigger_evacuation', 'description' => 'Trigger evacuation'],

            // Employee Permissions
            ['key' => 'employees.view', 'module' => 'employees', 'action' => 'view', 'description' => 'View employees'],
            ['key' => 'employees.manage', 'module' => 'employees', 'action' => 'manage', 'description' => 'Add and edit employees'],
            ['key' => 'employees.delete', 'module' => 'employees', 'action' => 'delete', 'description' => 'Delete employees'],

            // System Permissions
            ['key' => 'system.view_reports', 'module' => 'system', 'action' => 'view_reports', 'description' => 'View reports'],
            ['key' => 'system.manage_settings', 'module' => 'system', 'action' => 'manage_settings', 'description' => 'Manage settings'],
            ['key' => 'system.manage_users_roles', 'module' => 'system', 'action' => 'manage_users_roles', 'description' => 'Manage users and roles'],
            ['key' => 'system.manage_nda_templates', 'module' => 'system', 'action' => 'manage_nda_templates', 'description' => 'Manage NDA templates'],
            ['key' => 'system.access_billing', 'module' => 'system', 'action' => 'access_billing', 'description' => 'Access billing'],

            // Required Approval Permissions
            ['key' => 'approvals.view', 'module' => 'approvals', 'action' => 'view', 'description' => 'Open the Approvals page and view requests within assigned scope'],
            ['key' => 'approvals.pre_registration.approve', 'module' => 'approvals', 'action' => 'pre_registration.approve', 'description' => 'Approve a pending pre-registration'],
            ['key' => 'approvals.pre_registration.reject', 'module' => 'approvals', 'action' => 'pre_registration.reject', 'description' => 'Reject a pending pre-registration'],
            ['key' => 'approvals.walk_in.approve', 'module' => 'approvals', 'action' => 'walk_in.approve', 'description' => 'Approve a pending kiosk or self-service walk-in'],
            ['key' => 'approvals.walk_in.reject', 'module' => 'approvals', 'action' => 'walk_in.reject', 'description' => 'Reject a pending kiosk or self-service walk-in'],
            ['key' => 'approvals.gate_pass.approve', 'module' => 'approvals', 'action' => 'gate_pass.approve', 'description' => 'Activate a pending visitor or delivery gate pass'],
            ['key' => 'approvals.gate_pass.reject', 'module' => 'approvals', 'action' => 'gate_pass.reject', 'description' => 'Reject a pending gate pass'],
            ['key' => 'approvals.security.clear', 'module' => 'approvals', 'action' => 'security.clear', 'description' => 'Clear a security hold after identity and watchlist review'],
            ['key' => 'approvals.override', 'module' => 'approvals', 'action' => 'override', 'description' => 'Override another decision with a mandatory reason and audit record'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $p['key']],
                [
                    'key' => $p['key'],
                    'module' => $p['module'],
                    'action' => $p['action'],
                    'description' => $p['description'],
                    'is_active' => true,
                ]
            );
        }

        // Fetch permissions for mapping
        $allPerms = DB::table('permissions')->pluck('id', 'key')->toArray();

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
            $roleId = DB::table('roles')->updateOrInsert(
                ['organization_id' => $org->id, 'role_key' => $key],
                [
                    'name' => $r['name'],
                    'description' => $r['description'],
                    'is_protected' => $r['protected'],
                    'status' => 'active',
                ]
            );
        }

        // Fetch roles for mapping
        $allRoles = DB::table('roles')->where('organization_id', $org->id)->pluck('id', 'role_key')->toArray();

        // 3. Map Role Permissions
        // Defines access_level (full, view, none)
        // If not specified, assumed none.
        $matrix = [
            'super_admin' => [
                'visitors.view_all' => 'full', 'visitors.check_in_out' => 'full', 'visitors.pre_register' => 'full', 'visitors.print_badges' => 'full',
                'security.manage_watchlist' => 'full', 'security.block_visitors' => 'full', 'security.trigger_evacuation' => 'full',
                'employees.view' => 'full', 'employees.manage' => 'full', 'employees.delete' => 'full',
                'system.view_reports' => 'full', 'system.manage_settings' => 'full', 'system.manage_users_roles' => 'full', 'system.manage_nda_templates' => 'full', 'system.access_billing' => 'full',
                'approvals.view' => 'full', 'approvals.pre_registration.approve' => 'full', 'approvals.pre_registration.reject' => 'full', 'approvals.walk_in.approve' => 'full', 'approvals.walk_in.reject' => 'full', 'approvals.gate_pass.approve' => 'full', 'approvals.gate_pass.reject' => 'full', 'approvals.security.clear' => 'full', 'approvals.override' => 'full',
            ],
            'admin' => [
                'visitors.view_all' => 'full', 'visitors.check_in_out' => 'full', 'visitors.pre_register' => 'full', 'visitors.print_badges' => 'full',
                'security.manage_watchlist' => 'full', 'security.block_visitors' => 'full', 'security.trigger_evacuation' => 'full',
                'employees.view' => 'full', 'employees.manage' => 'full',
                'system.view_reports' => 'full', 'system.manage_settings' => 'full', 'system.manage_users_roles' => 'view', 'system.manage_nda_templates' => 'full',
                'approvals.view' => 'full', 'approvals.pre_registration.approve' => 'full', 'approvals.pre_registration.reject' => 'full', 'approvals.walk_in.approve' => 'full', 'approvals.walk_in.reject' => 'full', 'approvals.gate_pass.approve' => 'full', 'approvals.gate_pass.reject' => 'full', 'approvals.security.clear' => 'full', 'approvals.override' => 'full',
            ],
            'manager' => [
                'visitors.view_all' => 'view', 'visitors.check_in_out' => 'full', 'visitors.pre_register' => 'full',
                'employees.view' => 'full',
                'system.view_reports' => 'full',
                'approvals.view' => 'full', 'approvals.pre_registration.approve' => 'full', 'approvals.pre_registration.reject' => 'full', 'approvals.walk_in.approve' => 'full', 'approvals.walk_in.reject' => 'full', 'approvals.gate_pass.approve' => 'full', 'approvals.gate_pass.reject' => 'full',
            ],
            'security' => [
                'visitors.view_all' => 'full', 'visitors.check_in_out' => 'full', 'visitors.print_badges' => 'full',
                'security.manage_watchlist' => 'full', 'security.block_visitors' => 'full', 'security.trigger_evacuation' => 'full',
                'employees.view' => 'full',
                'approvals.view' => 'full', 'approvals.security.clear' => 'full',
            ],
            'receptionist' => [
                'visitors.view_all' => 'full', 'visitors.check_in_out' => 'full', 'visitors.pre_register' => 'full', 'visitors.print_badges' => 'full',
                'employees.view' => 'view',
            ],
            'host' => [
                'visitors.view_all' => 'view', 'visitors.pre_register' => 'full',
                'approvals.view' => 'full', 'approvals.pre_registration.approve' => 'full', 'approvals.pre_registration.reject' => 'full', 'approvals.walk_in.approve' => 'full', 'approvals.walk_in.reject' => 'full',
            ],
            'viewer' => [
                'visitors.view_all' => 'full',
                'system.view_reports' => 'full',
            ],
        ];

        // Ensure we wipe old permissions for these protected roles to keep matrix synced
        foreach ($allRoles as $rKey => $rId) {
            DB::table('role_permissions')->where('role_id', $rId)->delete();
            $roleMatrix = $matrix[$rKey] ?? [];
            
            $inserts = [];
            foreach ($roleMatrix as $pKey => $accessLevel) {
                if (isset($allPerms[$pKey])) {
                    $inserts[] = [
                        'organization_id' => $org->id,
                        'role_id' => $rId,
                        'permission_id' => $allPerms[$pKey],
                        'access_level' => $accessLevel
                    ];
                }
            }
            if (!empty($inserts)) {
                DB::table('role_permissions')->insert($inserts);
            }
        }
        
        // 4. Optionally, ensure the Super Admin user exists for the org
        // This acts as the bootstrap method
        $superAdminUser = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@zapio.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'organization_id' => $org->id,
            ]
        );
        
        // Assign Super Admin role to the user
        DB::table('user_roles')->updateOrInsert(
            ['organization_id' => $org->id, 'user_id' => $superAdminUser->id, 'role_id' => $allRoles['super_admin']],
            ['assigned_at' => $now]
        );
        
        // Give the super admin the platform scope
        DB::table('user_scopes')->updateOrInsert(
            ['organization_id' => $org->id, 'user_id' => $superAdminUser->id, 'scope_type' => 'platform']
        );
    }
}

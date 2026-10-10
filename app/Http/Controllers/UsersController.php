<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\TenantScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\RedirectResponse;

class UsersController extends Controller
{
    /**
     * Display the Users & Roles hub.
     */
    public function index(Request $request): View
    {
        $orgId = TenantScope::getTenantId() ?? 1;
        $activeTab = $request->get('tab', 'users');

        // Fetch users
        $users = User::with(['roles', 'employee'])
            ->where('organization_id', $orgId)
            ->orderBy('name')
            ->get();

        // Fetch roles
        $roles = Role::where('organization_id', $orgId)
            ->orderBy('name')
            ->get();
            
        // Fetch permissions for matrix
        $permissions = DB::table('permissions')->orderBy('module')->orderBy('id')->get();
        $rolePermissions = DB::table('role_permissions')->where('organization_id', $orgId)->get();

        // Helpers for add forms
        $employees = Employee::where('organization_id', $orgId)->orderBy('first_name')->get();
        $sites = Site::where('organization_id', $orgId)->orderBy('name')->get();

        return view('users.index', compact(
            'users', 
            'roles', 
            'permissions',
            'rolePermissions',
            'employees', 
            'sites', 
            'activeTab'
        ));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role_id' => ['required', 'exists:roles,id'],
            // 'phone' is accepted in the form but currently not saved as the DB lacks a phone column
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $user = User::create([
            'organization_id' => $orgId,
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email' => $validated['email'],
            'password' => Hash::make(Str::random(16)), // Auto-generate secure password
            'status' => 'active', // Default to active
        ]);

        // Attach single role
        DB::table('user_roles')->insert([
            'organization_id' => $orgId,
            'user_id' => $user->id,
            'role_id' => $validated['role_id'],
            'assigned_at' => now(),
        ]);
        
        // Default organization scope
        DB::table('user_scopes')->insert([
            'organization_id' => $orgId,
            'user_id' => $user->id,
            'scope_type' => 'organization',
        ]);

        return redirect()->route('users.index', ['tab' => 'users'])->with('success', 'User invited successfully.');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;
        $user = User::where('organization_id', $orgId)->findOrFail($id);

        $validated = $request->validate([
            'employee_id' => ['nullable', 'exists:employees,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', Password::defaults()],
            'role_ids' => ['required', 'array'],
            'role_ids.*' => ['exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
            'scopes' => ['nullable', 'array'],
        ]);

        $updateData = [
            'employee_id' => $validated['employee_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Update roles
        DB::table('user_roles')->where('user_id', $user->id)->delete();
        foreach ($validated['role_ids'] as $roleId) {
            DB::table('user_roles')->insert([
                'organization_id' => $orgId,
                'user_id' => $user->id,
                'role_id' => $roleId,
                'assigned_at' => now(),
            ]);
        }
        
        // Update scopes
        DB::table('user_scopes')->where('user_id', $user->id)->delete();
        if (!empty($validated['scopes'])) {
            foreach ($validated['scopes'] as $type => $ids) {
                if ($type === 'organization' || $type === 'platform') {
                    DB::table('user_scopes')->insert([
                        'organization_id' => $orgId,
                        'user_id' => $user->id,
                        'scope_type' => $type,
                    ]);
                } else if (is_array($ids)) {
                    foreach ($ids as $scopeId) {
                        DB::table('user_scopes')->insert([
                            'organization_id' => $orgId,
                            'user_id' => $user->id,
                            'scope_type' => $type,
                            'scope_id' => $scopeId,
                        ]);
                    }
                }
            }
        } else {
             // Default organization scope if nothing provided
             DB::table('user_scopes')->insert([
                'organization_id' => $orgId,
                'user_id' => $user->id,
                'scope_type' => 'organization',
            ]);
        }

        return redirect()->route('users.index', ['tab' => 'users'])->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;
        $user = User::where('organization_id', $orgId)->findOrFail($id);
        
        // Block self deletion
        if (auth()->id() == $user->id) {
            return redirect()->route('users.index', ['tab' => 'users'])->withErrors('You cannot delete your own account.');
        }
        
        // Check if user is the last super admin (optional logic, skipped for brevity)

        $user->delete();

        return redirect()->route('users.index', ['tab' => 'users'])->with('success', 'User deleted successfully.');
    }

    /**
     * Update a role (or create a custom role).
     */
        /**
     * Store a newly created role.
     */
    public function storeRole(Request $request): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array']
        ]);
        
        $role = Role::create([
            'organization_id' => $orgId,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => 'active',
            'is_protected' => false,
        ]);
        
        if (isset($validated['permissions'])) {
            foreach ($validated['permissions'] as $permId => $accessLevel) {
                $level = ($accessLevel === 'on' || $accessLevel == '1' || $accessLevel === 'full') ? 'full' : 'none';
                DB::table('role_permissions')->insert([
                    'organization_id' => $orgId,
                    'role_id' => $role->id,
                    'permission_id' => $permId,
                    'access_level' => $level
                ]);
            }
        }
        
        return redirect()->route('users.index', ['tab' => 'roles'])->with('success', 'Role created successfully.');
    }

    public function updateRole(Request $request, string $id): RedirectResponse
    {
        $orgId = TenantScope::getTenantId() ?? 1;
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'permissions' => ['nullable', 'array'] // [permission_id => access_level]
        ]);
        
        $role = Role::where('organization_id', $orgId)->findOrFail($id);
        
        if (!$role->is_protected) {
            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);
        }
        
        // Map permissions if provided
        if (isset($validated['permissions'])) {
            DB::table('role_permissions')->where('role_id', $role->id)->delete();
            foreach ($validated['permissions'] as $permId => $accessLevel) {
                if ($accessLevel !== 'none') {
                    DB::table('role_permissions')->insert([
                        'organization_id' => $orgId,
                        'role_id' => $role->id,
                        'permission_id' => $permId,
                        'access_level' => $accessLevel
                    ]);
                }
            }
        }
        
        return redirect()->route('users.index', ['tab' => 'roles'])->with('success', 'Role updated successfully.');
    }
}





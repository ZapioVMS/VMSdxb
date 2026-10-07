<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthorizationService
{
    /**
     * Check if a user has a specific permission key with 'full' or 'view' access.
     */
    public function hasPermission(User $user, string $permissionKey, bool $requireFull = false): bool
    {
        $query = DB::table('role_permissions')
            ->join('user_roles', 'user_roles.role_id', '=', 'role_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('user_roles.user_id', $user->id)
            ->where('permissions.key', $permissionKey);

        if ($requireFull) {
            $query->where('role_permissions.access_level', 'full');
        } else {
            $query->whereIn('role_permissions.access_level', ['full', 'view']);
        }

        return $query->exists();
    }

    /**
     * Get all assigned scopes for a user.
     * Returns an array mapping scope_type to an array of scope_ids.
     */
    public function getUserScopes(User $user): array
    {
        $scopes = DB::table('user_scopes')
            ->where('user_id', $user->id)
            ->get();

        $result = [];
        foreach ($scopes as $scope) {
            if (!isset($result[$scope->scope_type])) {
                $result[$scope->scope_type] = [];
            }
            if ($scope->scope_id !== null) {
                $result[$scope->scope_type][] = $scope->scope_id;
            }
        }
        
        return $result;
    }

    /**
     * Check if a user has access to a specific record based on their scopes.
     */
    public function hasScope(User $user, string $scopeType, ?int $scopeId = null): bool
    {
        $scopes = $this->getUserScopes($user);

        // If they have platform or organization scope (with no specific ID restrictions), they have access
        if (isset($scopes['platform']) || isset($scopes['organization'])) {
            return true;
        }

        if (!isset($scopes[$scopeType])) {
            return false;
        }

        // If the scope has no specific IDs restricted (e.g. they have access to all branches)
        if (empty($scopes[$scopeType])) {
            return true;
        }

        // Check if the specific ID is in their assigned scope IDs
        return in_array($scopeId, $scopes[$scopeType]);
    }
}

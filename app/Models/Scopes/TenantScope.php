<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Explicit tenant ID override for queue jobs, background tasks, or testing.
     */
    protected static ?int $overrideTenantId = null;

    /**
     * Flag indicating whether tenancy scope is globally bypassed.
     */
    protected static bool $bypassTenancy = false;

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (static::$bypassTenancy) {
            return;
        }

        $tenantId = static::getTenantId();

        if ($tenantId !== null) {
            $builder->where($model->getTable() . '.organization_id', '=', $tenantId);
        } else {
            // Fail-closed security rule: If no valid tenant context is resolved and tenancy is NOT
            // explicitly bypassed, block the query to prevent accidental cross-tenant data leakage.
            $builder->whereRaw('1 = 0');
        }
    }

    /**
     * Resolve the authorized organization ID server-side.
     */
    public static function getTenantId(): ?int
    {
        // 1. Explicit static override (e.g. background job context)
        if (static::$overrideTenantId !== null) {
            return static::$overrideTenantId;
        }

        // 2. Authenticated user context
        if (Auth::check()) {
            $user = Auth::user();

            // Platform super-admin (NULL organization_id) inspecting a specific tenant via header
            if ($user->organization_id === null) {
                if (request()->hasHeader('X-Organization-ID')) {
                    return (int) request()->header('X-Organization-ID');
                }
                // Super-admin without header operates without single-tenant scoping
                return null;
            }

            // Standard tenant user: verify header against authorized organization
            if (request()->hasHeader('X-Organization-ID')) {
                $requestedId = (int) request()->header('X-Organization-ID');
                if ($requestedId === (int) $user->organization_id) {
                    return $requestedId;
                }
                // Reject unauthorized header manipulation
                return (int) $user->organization_id;
            }

            return (int) $user->organization_id;
        }

        // 3. Unauthenticated session or verified request context (e.g. kiosk device)
        if (session()->has('organization_id')) {
            return (int) session('organization_id');
        }

        return null;
    }

    /**
     * Explicitly set the tenant context for background jobs or tests.
     */
    public static function setTenantId(?int $tenantId): void
    {
        static::$overrideTenantId = $tenantId;
    }

    /**
     * Execute a callback without single-tenant query scoping (e.g., platform admin operations).
     */
    public static function withoutTenancy(callable $callback)
    {
        $previousBypass = static::$bypassTenancy;
        static::$bypassTenancy = true;

        try {
            return $callback();
        } finally {
            static::$bypassTenancy = $previousBypass;
        }
    }
}

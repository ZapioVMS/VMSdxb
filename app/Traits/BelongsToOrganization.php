<?php

namespace App\Traits;

use App\Models\Organization;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    /**
     * Boot the trait to attach global TenantScope and auto-assign organization_id on creation.
     */
    protected static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (!$model->organization_id) {
                $tenantId = TenantScope::getTenantId();
                if ($tenantId) {
                    $model->organization_id = $tenantId;
                }
            }
        });
    }

    /**
     * Eloquent relationship to the owning organization.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Scope a query to disable tenant filtering (for authorized platform operations).
     */
    public function scopeWithoutTenancy(Builder $query): Builder
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }
}

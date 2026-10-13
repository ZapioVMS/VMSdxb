<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GatePass extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'gate_passes';

    protected $fillable = [
        'organization_id',
        'site_id',
        'pass_code',
        'type',
        'office_unit',
        'visitor_name',
        'company_plate',
        'purpose',
        'id_type',
        'id_number',
        'email',
        'phone',
        'expected_date',
        'expected_duration',
        'carrier',
        'package_size',
        'tracking_number',
        'description',
        'notes',
        'status',
        'redeemed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'expected_date' => 'date',
        'redeemed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'delivery') {
            return $this->description ?: ($this->carrier ?: 'Delivery');
        }
        return $this->visitor_name ?: 'Visitor';
    }

    public function getDisplaySubAttribute(): string
    {
        if ($this->type === 'delivery') {
            $parts = [];
            if ($this->carrier) $parts[] = $this->carrier;
            if ($this->tracking_number) $parts[] = $this->tracking_number;
            return implode(' · ', $parts);
        }
        return $this->company_plate ?: '';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeVisitor(Builder $query): Builder
    {
        return $query->where('type', 'visitor');
    }

    public function scopeDelivery(Builder $query): Builder
    {
        return $query->where('type', 'delivery');
    }
}

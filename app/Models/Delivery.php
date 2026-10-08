<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'site_id',
        'delivery_code',
        'carrier',
        'tracking_number',
        'size',
        'description',
        'recipient_employee_id',
        'recipient_name',
        'recipient_email',
        'department',
        'received_at',
        'received_by',
        'status',
        'collected_at',
        'collected_by',
        'notified_at',
        'notify_recipient',
        'notes',
        'photo_path',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'collected_at' => 'datetime',
        'notified_at' => 'datetime',
        'notify_recipient' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recipient_employee_id');
    }

    public function getIsUnclaimedAttribute(): bool
    {
        return $this->status === 'awaiting-pickup' && $this->received_at && $this->received_at->diffInDays(now()) >= 3;
    }

    public function getAgeDaysAttribute(): int
    {
        return $this->received_at ? (int) $this->received_at->diffInDays(now()) : 0;
    }

    public function scopeAwaitingPickup(Builder $query): Builder
    {
        return $query->where('status', 'awaiting-pickup');
    }

    public function scopeCollected(Builder $query): Builder
    {
        return $query->where('status', 'collected');
    }

    public function scopeUnclaimed(Builder $query): Builder
    {
        return $query->where('status', 'awaiting-pickup')
                     ->where('received_at', '<=', now()->subDays(3));
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvacuationEvent extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'evacuation_events';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'site_id',
        'started_by',
        'started_at',
        'ended_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function startedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function records(): HasMany
    {
        return $this->hasMany(EvacuationRecord::class, 'evacuation_event_id');
    }
}

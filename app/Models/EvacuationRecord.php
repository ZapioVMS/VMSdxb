<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvacuationRecord extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'evacuation_records';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'evacuation_event_id',
        'person_type',
        'employee_id',
        'visit_id',
        'status',
        'marked_safe_by',
        'marked_safe_at',
    ];

    protected $casts = [
        'marked_safe_at' => 'datetime',
    ];

    public function evacuationEvent(): BelongsTo
    {
        return $this->belongsTo(EvacuationEvent::class, 'evacuation_event_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function markedSafeByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_safe_by');
    }
}

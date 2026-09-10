<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Visit extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'visits';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'visitor_id',
        'site_id',
        'host_employee_id',
        'department_id',
        'purpose_id',
        'visitor_type_id',
        'scheduled_start_at',
        'scheduled_end_at',
        'check_in_at',
        'check_out_at',
        'status',
        'source',
        'notes',
    ];

    protected $casts = [
        'scheduled_start_at' => 'datetime',
        'scheduled_end_at' => 'datetime',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class, 'visitor_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function hostEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'host_employee_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function purpose(): BelongsTo
    {
        return $this->belongsTo(VisitPurpose::class, 'purpose_id');
    }

    public function visitorType(): BelongsTo
    {
        return $this->belongsTo(VisitorType::class, 'visitor_type_id');
    }

    public function invitation(): HasOne
    {
        return $this->hasOne(Invitation::class, 'visit_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'visit_id');
    }

    public function watchlistMatches(): HasMany
    {
        return $this->hasMany(WatchlistMatch::class, 'visit_id');
    }

    public function visitAgreements(): HasMany
    {
        return $this->hasMany(VisitAgreement::class, 'visit_id');
    }

    public function badges(): HasMany
    {
        return $this->hasMany(Badge::class, 'visit_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'visit_id');
    }
}

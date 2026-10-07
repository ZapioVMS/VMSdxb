<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'approvals';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'visit_id',
        'approver_user_id',
        'status',
        'decision_at',
        'rejection_reason',
        'comments',
    ];

    protected $casts = [
        'decision_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function approverUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}

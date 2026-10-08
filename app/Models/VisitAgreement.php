<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitAgreement extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'visit_agreements';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'visit_id',
        'agreement_id',
        'accepted_at',
        'signature_type',
        'signature_data',
        'witness_user_id',
        'email_copy_sent_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'email_copy_sent_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(Agreement::class, 'agreement_id');
    }

    public function witnessUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'witness_user_id');
    }
}

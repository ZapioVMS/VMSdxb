<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agreement extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'agreements';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'name',
        'version',
        'visitor_type_id',
        'valid_from',
        'valid_until',
        'status',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    public function visitorType(): BelongsTo
    {
        return $this->belongsTo(VisitorType::class, 'visitor_type_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(AgreementSection::class, 'agreement_id')->orderBy('sort_order');
    }

    public function visitAgreements(): HasMany
    {
        return $this->hasMany(VisitAgreement::class, 'agreement_id');
    }
}

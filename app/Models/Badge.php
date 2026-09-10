<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Badge extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'badges';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'visit_id',
        'badge_number',
        'template_id',
        'issued_at',
        'expires_at',
        'printed_at',
        'returned_at',
        'status',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'printed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(BadgeTemplate::class, 'template_id');
    }
}

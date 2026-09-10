<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'invitations';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'visit_id',
        'access_code',
        'qr_token_hash',
        'expected_visitor_count',
        'items_to_bring',
        'special_instructions',
        'sent_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'expected_visitor_count' => 'integer',
        'sent_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }
}

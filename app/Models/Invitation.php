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
        'recurring',
        'meeting_room',
        'duration',
        'send_confirmation_email',
        'notify_host_on_arrival',
        'send_qr_self_checkin',
        'requires_nda',
        'wifi_guest_access',
        'parking_required',
        'escort_required',
    ];

    protected $casts = [
        'expected_visitor_count' => 'integer',
        'duration' => 'integer',
        'send_confirmation_email' => 'boolean',
        'notify_host_on_arrival' => 'boolean',
        'send_qr_self_checkin' => 'boolean',
        'requires_nda' => 'boolean',
        'wifi_guest_access' => 'boolean',
        'parking_required' => 'boolean',
        'escort_required' => 'boolean',
        'sent_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }
}

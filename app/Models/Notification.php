<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'notifications';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'visit_id',
        'recipient_type',
        'channel',
        'template_id',
        'status',
        'sent_at',
        'provider_message_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }
}

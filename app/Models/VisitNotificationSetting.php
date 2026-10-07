<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitNotificationSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'arrival_email_enabled' => 'boolean',
        'arrival_push_enabled' => 'boolean',
        'arrival_sms_enabled' => 'boolean',
        'arrival_desktop_enabled' => 'boolean',
        'prereg_host_notification_enabled' => 'boolean',
        'morning_briefing_enabled' => 'boolean',
        'prereg_visitor_reminder_enabled' => 'boolean',
        'escalation_enabled' => 'boolean',
        'checkout_notification_enabled' => 'boolean',
        'overstay_alert_enabled' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}

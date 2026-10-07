<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KioskDevice extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'kiosk_devices';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'site_id',
        'name',
        'device_identifier',
        'status',
        'last_seen_at',
        'photo_capture',
        'qr_scanning',
        'id_scanning',
        'auto_print_badge',
        'require_agreement',
        'idle_timeout',
        'language',
        'printer_settings',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'photo_capture' => 'boolean',
        'qr_scanning' => 'boolean',
        'id_scanning' => 'boolean',
        'auto_print_badge' => 'boolean',
        'require_agreement' => 'boolean',
        'idle_timeout' => 'integer',
        'printer_settings' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function checkinEvents(): HasMany
    {
        return $this->hasMany(KioskCheckinEvent::class, 'kiosk_device_id');
    }
}

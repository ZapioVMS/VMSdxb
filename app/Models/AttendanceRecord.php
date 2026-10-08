<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'attendance_records';

    protected $fillable = [
        'organization_id',
        'employee_id',
        'site_id',
        'kiosk_device_id',
        'attendance_date',
        'check_in_at',
        'check_out_at',
        'status',
        'source',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function kioskDevice(): BelongsTo
    {
        return $this->belongsTo(KioskDevice::class, 'kiosk_device_id');
    }
}

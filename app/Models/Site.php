<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'sites';

    protected $fillable = [
        'organization_id',
        'name',
        'name_ar',
        'city',
        'code',
        'address',
        'timezone',
        'capacity',
        'visiting_hours',
        'status',
    ];

    protected $casts = [
        'visiting_hours' => 'array',
        'capacity' => 'integer',
    ];

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'site_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'site_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'site_id');
    }

    public function kioskDevices(): HasMany
    {
        return $this->hasMany(KioskDevice::class, 'site_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'site_id');
    }

    public function evacuationEvents(): HasMany
    {
        return $this->hasMany(EvacuationEvent::class, 'site_id');
    }
}

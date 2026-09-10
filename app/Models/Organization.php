<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'trade_license_no',
        'timezone',
        'default_language',
        'status',
    ];

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class, 'organization_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'organization_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'organization_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'organization_id');
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(Visitor::class, 'organization_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'organization_id');
    }

    public function visitorTypes(): HasMany
    {
        return $this->hasMany(VisitorType::class, 'organization_id');
    }

    public function visitPurposes(): HasMany
    {
        return $this->hasMany(VisitPurpose::class, 'organization_id');
    }

    public function watchlistEntries(): HasMany
    {
        return $this->hasMany(WatchlistEntry::class, 'organization_id');
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class, 'organization_id');
    }

    public function kioskDevices(): HasMany
    {
        return $this->hasMany(KioskDevice::class, 'organization_id');
    }

    public function notificationTemplates(): HasMany
    {
        return $this->hasMany(NotificationTemplate::class, 'organization_id');
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'employees';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'site_id',
        'department_id',
        'employee_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'job_title',
        'manager_id',
        'shift_id',
        'photo_path',
        'status',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function hostedVisits(): HasMany
    {
        return $this->hasMany(Visit::class, 'host_employee_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'employee_id');
    }

    public function accessCredentials(): HasMany
    {
        return $this->hasMany(EmployeeAccessCredential::class, 'employee_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->last_name ?? ''));
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'departments';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'description',
        'head_employee_id',
        'contact_email',
        'contact_phone',
        'kiosk_selectable',
        'require_nda',
        'escort_required',
        'status',
    ];

    protected $casts = [
        'kiosk_selectable' => 'boolean',
        'require_nda' => 'boolean',
        'escort_required' => 'boolean',
    ];

    public function headEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'head_employee_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department_id');
    }
}

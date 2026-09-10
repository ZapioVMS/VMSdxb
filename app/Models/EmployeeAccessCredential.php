<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAccessCredential extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'employee_access_credentials';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'employee_id',
        'credential_type',
        'token_hash',
        'qr_enabled',
        'valid_from',
        'valid_until',
        'status',
        'last_used_at',
    ];

    protected $casts = [
        'qr_enabled' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}

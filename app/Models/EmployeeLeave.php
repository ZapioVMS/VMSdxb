<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeave extends Model
{
    use HasFactory;

    protected $table = 'employee_leaves';

    protected $fillable = [
        'employee_id',
        'date',
        'reason',
    ];

    protected $casts = [
        // Plain calendar date (e.g. 2026-10-20). Without the format, JSON output becomes a UTC
        // timestamp ("2026-10-19T20:00:00Z" in Dubai time), which shifted leave days back by one in the calendar.
        'date' => 'date:Y-m-d',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}

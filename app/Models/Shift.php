<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'shifts';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'name',
        'start_time',
        'end_time',
        'effective_from',
        'effective_to',
        'grace_minutes',
        'status',
    ];

    protected $casts = [
        'grace_minutes' => 'integer',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'shift_id');
    }
}

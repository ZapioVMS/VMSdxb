<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Area extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'areas';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'floor_id',
        'name',
        'code',
        'capacity',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }
}

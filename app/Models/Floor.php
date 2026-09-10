<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'floors';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'building_id',
        'name',
        'code',
        'capacity',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class, 'floor_id');
    }
}

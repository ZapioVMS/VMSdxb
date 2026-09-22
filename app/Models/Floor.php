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
        'site_id',
        'building_id',
        'name',
        'description',
        'code',
        'capacity',
        'status',
    ];

    protected $appends = [
        'areas_text',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class, 'floor_id');
    }

    public function getAreasTextAttribute(): string
    {
        return $this->areas->pluck('name')->implode(', ');
    }
}

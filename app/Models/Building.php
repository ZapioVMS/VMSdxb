<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'buildings';

    protected $fillable = [
        'organization_id',
        'site_id',
        'name',
        'code',
        'status',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class, 'building_id');
    }
}

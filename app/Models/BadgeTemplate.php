<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BadgeTemplate extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'badge_templates';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'name',
        'layout_json',
        'status',
    ];

    protected $casts = [
        'layout_json' => 'array',
    ];

    public function badges(): HasMany
    {
        return $this->hasMany(Badge::class, 'template_id');
    }
}

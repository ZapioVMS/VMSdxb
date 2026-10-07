<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationSetting extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'organization_settings';

    protected $fillable = [
        'organization_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}

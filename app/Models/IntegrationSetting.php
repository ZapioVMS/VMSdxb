<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationSetting extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'integration_settings';

    protected $fillable = [
        'organization_id',
        'type',
        'config_encrypted',
        'status',
    ];

    protected $casts = [
        'config_encrypted' => 'encrypted:array',
    ];
}

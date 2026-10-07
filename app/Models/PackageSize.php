<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageSize extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'package_sizes';

    protected $fillable = [
        'organization_id',
        'name',
        'description',
    ];
}

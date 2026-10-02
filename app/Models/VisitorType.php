<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorType extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'visitor_types';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'name',
        'requires_approval',
        'requires_nda',
        'requires_escort',
        'status',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'requires_nda' => 'boolean',
        'requires_escort' => 'boolean',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class, 'visitor_type_id');
    }
}

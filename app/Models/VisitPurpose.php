<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitPurpose extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'visit_purposes';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'name',
        'status',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class, 'purpose_id');
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryCarrier extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'delivery_carriers';

    protected $fillable = [
        'organization_id',
        'name',
        'code',
    ];
}

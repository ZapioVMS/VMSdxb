<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'meeting_rooms';

    protected $fillable = [
        'organization_id',
        'name',
    ];
}

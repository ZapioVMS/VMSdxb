<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingRoom extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'meeting_rooms';

    protected $fillable = [
        'organization_id',
        'site_id',
        'name',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
}

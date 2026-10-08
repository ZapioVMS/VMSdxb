<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplate extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'notification_templates';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'name',
        'event',
        'channel',
        'language',
        'subject',
        'body',
        'variables_json',
        'status',
    ];

    protected $casts = [
        'variables_json' => 'array',
    ];

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'template_id');
    }
}

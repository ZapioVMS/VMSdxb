<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WatchlistEntry extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'watchlist_entries';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'name',
        'id_type_id',
        'id_number_hash',
        'reason',
        'severity',
        'status',
    ];

    public function idType(): BelongsTo
    {
        return $this->belongsTo(IdType::class, 'id_type_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(WatchlistMatch::class, 'watchlist_entry_id');
    }
}

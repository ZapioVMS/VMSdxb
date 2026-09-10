<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchlistMatch extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'watchlist_matches';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'visit_id',
        'watchlist_entry_id',
        'match_type',
        'score',
        'verified_by',
        'verified_at',
        'decision',
    ];

    protected $casts = [
        'score' => 'float',
        'verified_at' => 'datetime',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function watchlistEntry(): BelongsTo
    {
        return $this->belongsTo(WatchlistEntry::class, 'watchlist_entry_id');
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

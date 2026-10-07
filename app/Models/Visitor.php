<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'visitors';

    protected $fillable = [
        'organization_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'nationality_id',
        'id_type_id',
        'id_number',
        'company_name',
        'photo_path',
        'vehicle_plate',
    ];

    protected $casts = [
        'id_number' => 'encrypted',
    ];

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }

    public function idType(): BelongsTo
    {
        return $this->belongsTo(IdType::class, 'id_type_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'visitor_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->last_name ?? ''));
    }
}

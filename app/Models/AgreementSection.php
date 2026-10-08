<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgreementSection extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'agreement_sections';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'agreement_id',
        'title',
        'content',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(Agreement::class, 'agreement_id');
    }
}

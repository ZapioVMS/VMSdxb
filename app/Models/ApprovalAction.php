<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalAction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function approvalRequest()
    {
        return $this->belongsTo(ApprovalRequest::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}

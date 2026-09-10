<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdType extends Model
{
    use HasFactory;

    protected $table = 'id_types';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'status',
    ];
}

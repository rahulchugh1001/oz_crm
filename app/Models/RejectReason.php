<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RejectReason extends Model
{
    protected $fillable = [
        'name',
        'category',
        'status',
        'is_deleted',
    ];

    protected $casts = [
        'category' => 'string',
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RejectReason extends Model
{
    protected $fillable = [
        'name',
        'status',
        'is_deleted',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];
}

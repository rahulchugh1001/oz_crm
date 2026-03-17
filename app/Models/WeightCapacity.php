<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightCapacity extends Model
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


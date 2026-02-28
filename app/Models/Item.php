<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'code',
        'size',
        'weight',
        'status',
        'is_deleted'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];


}

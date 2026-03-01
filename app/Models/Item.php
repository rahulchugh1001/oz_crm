<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function productionReports(): HasMany
    {
        return $this->hasMany(ProductionReport::class, 'slide_size_id');
    }
}

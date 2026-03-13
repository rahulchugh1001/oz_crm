<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoilStock extends Model
{
    use HasFactory;

    protected $table = 'coil_stock';

    protected $fillable = [
        'manufacture_id',
        'coil_no',
        'coil_size',
        'thickness',
        'net_weight_kg',
        'process',
        'status',
        'is_deleted',
    ];

    protected $casts = [
        'thickness' => 'decimal:3',
        'net_weight_kg' => 'decimal:3',
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function manufacture(): BelongsTo
    {
        return $this->belongsTo(CoilManufacture::class, 'manufacture_id');
    }
}

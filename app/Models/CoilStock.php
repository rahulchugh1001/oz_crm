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
        'process_type',
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

    /**
     * The machines that belong to the coil.
     */
    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'coil_machine', 'coil_stock_id', 'machine_id')->withTimestamps();
    }

    public function loadNumbers()
    {
        return $this->hasMany(CoilLoadNumber::class, 'coil_id');
    }
}

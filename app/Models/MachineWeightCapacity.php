<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class MachineWeightCapacity extends Model
{
    protected $fillable = [
        'machine_id',
        'weight_capacity_id',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function weightCapacity(): BelongsTo
    {
        return $this->belongsTo(WeightCapacity::class);
    }
}

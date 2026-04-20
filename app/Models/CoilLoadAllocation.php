<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoilLoadAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'coil_id',
        'machine_id',
        'coil_no',
        'allocated_weight',
        'consumed_weight',
        'remaining_weight',
        'status',
        'load_track_id',
        'unload_track_id'
    ];

    public function coil()
    {
        return $this->belongsTo(CoilStock::class, 'coil_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }
}

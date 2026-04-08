<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoilLoadNumber extends Model
{
    protected $table = 'coil_load_numbers';

    protected $fillable = [
        'coil_id',
        'coil_machine_track_id',
        'coil_no',
        'created_by',
    ];

    public function coil(): BelongsTo
    {
        return $this->belongsTo(CoilStock::class, 'coil_id');
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(CoilMachineTrack::class, 'coil_machine_track_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

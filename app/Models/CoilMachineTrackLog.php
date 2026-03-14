<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoilMachineTrackLog extends Model
{
    use HasFactory;

    protected $table = 'coil_machine_track_logs';

    protected $fillable = [
        'coil_machine_track_id',
        'machine_id',
        'coil_id',
        'action_type',
        'load_weight',
        'unload_weight',
        'total_weight',
        'old_data',
        'new_data',
        'message',
        'created_by',
        'status',
        'is_deleted',
    ];

    protected $casts = [
        'load_weight' => 'decimal:3',
        'unload_weight' => 'decimal:3',
        'total_weight' => 'decimal:3',
        'old_data' => 'array',
        'new_data' => 'array',
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function track(): BelongsTo
    {
        return $this->belongsTo(CoilMachineTrack::class, 'coil_machine_track_id');
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function coil(): BelongsTo
    {
        return $this->belongsTo(CoilStock::class, 'coil_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoilMachineTrack extends Model
{
    use HasFactory;

    public const ACTION_LOAD = 'load';
    public const ACTION_UNLOAD = 'unload';

    protected $table = 'coil_machine_track';

    protected $fillable = [
        'machine_id',
        'coil_id',
        'load_weight',
        'unload_weight',
        'type',
        'reference_track_id',
        'event_at',
        'remark',
        'created_by',
        'status',
        'is_deleted',
    ];

    protected $casts = [
        'load_weight' => 'decimal:3',
        'unload_weight' => 'decimal:3',
        'event_at' => 'datetime',
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public static function manageActionTabs(): array
    {
        return [
            self::ACTION_LOAD => 'Load',
            self::ACTION_UNLOAD => 'Unload',
        ];
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

    public function referenceTrack(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reference_track_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CoilMachineTrackLog::class, 'coil_machine_track_id');
    }

    public function loadNumber(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CoilLoadNumber::class, 'coil_machine_track_id');
    }
}

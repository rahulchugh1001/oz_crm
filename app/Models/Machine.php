<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;


class Machine extends Model
{
    
 use HasFactory;

    public const RF_SET_OPTIONS = [
        'Inner',
        'Outer',
        'Middle',
        'Ball Cage',
    ];

    public const WEIGHT_CAPACITY_OPTIONS = [
        '35',
        '45',
    ];

    protected $fillable = [
    'name',
    'machine_code',
    'rf_set',
    'coil_id',
    'weight_capacity',
    'status',
    'is_deleted',
];

    public function coil(): BelongsTo
    {
        return $this->belongsTo(CoilStock::class, 'coil_id');
    }

    /**
     * Get the value of the model's route key (encrypted).
     */
    public function getRouteKey(): mixed
    {
        return Crypt::encryptString($this->getKey());
    }

    /**
     * Retrieve the model for a bound value (decrypt).
     */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        try {
            $decryptedId = Crypt::decryptString($value);
            return $this->where($this->getKeyName(), $decryptedId)->first();
        } catch (\Exception $e) {
            return null;
        }
    }
}

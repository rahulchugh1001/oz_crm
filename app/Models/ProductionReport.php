<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class ProductionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id',
        'coil_id',
        'created_by',
        'slide_size_id',
        'report_date',
        'shift',
        'total_set_shift',
        'set_per_hour',
        'hour_8_9',
        'hour_9_10',
        'hour_10_11',
        'hour_11_12',
        'hour_12_1',
        'hour_1_2',
        'hour_2_3',
        'hour_3_4',
        'hour_4_5',
        'hour_5_6',
        'hour_6_7',
        'hour_7_8',
        'actual_set_shift',
        'workman_count',
        'staff_count',
        'status',
        'is_deleted',
        'is_draft'
    ];

    public function slideSize()
    {
        return $this->belongsTo(Item::class, 'slide_size_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function coil()
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
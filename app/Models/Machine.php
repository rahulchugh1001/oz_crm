<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;


class Machine extends Model
{
    
 use HasFactory;

    protected $fillable = [
    'name',
    'machine_code',
    'rf_set',
    'status',
    'is_deleted',
];

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

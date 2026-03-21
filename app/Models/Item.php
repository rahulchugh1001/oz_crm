<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Item extends Model
{
    protected $fillable = [
        'name',
        'code',
        'category',
        'name_sf2',
        'code_sf2',
        'size',
        'weight',
        'quantity',
        'status',
        'is_deleted'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'quantity' => 'integer',
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function productionReports(): HasMany
    {
        return $this->hasMany(ProductionReport::class, 'slide_size_id');
    }

    public function machines(): BelongsToMany
    {
        return $this->belongsToMany(Machine::class)->withTimestamps();
    }

    public function sf3Products(): HasMany
    {
        return $this->hasMany(ItemSf3Product::class);
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

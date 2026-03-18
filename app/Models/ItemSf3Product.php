<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemSf3Product extends Model
{
    protected $fillable = [
        'item_id',
        'product',
        'quantity',
    ];

    protected $casts = [
        'product'  => 'integer',
        'quantity' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** The linked product item (SF1-SF2 or Store). */
    public function productItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'product');
    }
}

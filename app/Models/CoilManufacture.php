<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoilManufacture extends Model
{
    use HasFactory;

    protected $table = 'coil_manufacture';

    protected $fillable = [
        'name',
        'status',
        'is_deleted',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function coils(): HasMany
    {
        return $this->hasMany(CoilStock::class, 'manufacture_id');
    }
}

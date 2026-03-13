<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoilManufacture extends Model
{
    use HasFactory;

    protected $table = 'coil_manufacture';

    protected $fillable = [
        'name',
        'status',
        'is_deleted',
    ];
}

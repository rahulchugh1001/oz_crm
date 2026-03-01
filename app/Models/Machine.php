<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


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


}

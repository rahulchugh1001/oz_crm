<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sf2ProductionReport extends Model
{
    use HasFactory;

    protected $table = 'sf2_production_reports';

    protected $fillable = [
        'type',
        'created_by',
        'report_date',
        'shift',
        'transfered_id',
        'item_id',
        'set_per_hour',
        'total_set_shift',
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
        'manpower_workman',
        'staff_count',
        'status',
        'is_deleted',
    ];

    protected $casts = [
        'report_date' => 'date',
        'set_per_hour' => 'float',
        'total_set_shift' => 'float',
        'hour_8_9' => 'float',
        'hour_9_10' => 'float',
        'hour_10_11' => 'float',
        'hour_11_12' => 'float',
        'hour_12_1' => 'float',
        'hour_1_2' => 'float',
        'hour_2_3' => 'float',
        'hour_3_4' => 'float',
        'hour_4_5' => 'float',
        'hour_5_6' => 'float',
        'hour_6_7' => 'float',
        'hour_7_8' => 'float',
        'actual_set_shift' => 'float',
        'manpower_workman' => 'float',
        'staff_count' => 'integer',
        'status' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

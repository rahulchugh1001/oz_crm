<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeightCapacitySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $names = [
            '45kg-STD',
            '35kg-SC',
            '25kg-SC',
            '30kg-STD',
            '40kg-STD',
            'Common',
        ];

        DB::table('weight_capacities')->upsert(
            array_map(function (string $name) use ($now) {
                return [
                    'name' => $name,
                    'status' => 1,
                    'is_deleted' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $names),
            ['name'],
            ['status', 'is_deleted', 'updated_at']
        );
    }
}


<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Machine;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        // Disable foreign key checks (important)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Machine::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $machines = [

            // RF-01
            ['name' => 'Machine No. 01 Outer-SF-1', 'machine_code' => 'M01', 'rf_set' => 'RF-01'],
            ['name' => 'Machine No. 02 Middle-SF-1', 'machine_code' => 'M02', 'rf_set' => 'RF-01'],
            ['name' => 'Machine No. 03 Inner-SF-1', 'machine_code' => 'M03', 'rf_set' => 'RF-01'],

            // RF-02
            ['name' => 'Machine No. 04 Outer-SF-1', 'machine_code' => 'M04', 'rf_set' => 'RF-02'],
            ['name' => 'Machine No. 05 Middle-SF-1', 'machine_code' => 'M05', 'rf_set' => 'RF-02'],
            ['name' => 'Machine No. 06 Inner-SF-1', 'machine_code' => 'M06', 'rf_set' => 'RF-02'],

            // RF-03
            ['name' => 'Machine No. 07 Outer-SF-1', 'machine_code' => 'M07', 'rf_set' => 'RF-03'],
            ['name' => 'Machine No. 08 Middle-SF-1', 'machine_code' => 'M08', 'rf_set' => 'RF-03'],
            ['name' => 'Machine No. 09 Inner-SF-1', 'machine_code' => 'M09', 'rf_set' => 'RF-03'],

            // RF-04
            ['name' => 'Machine No. 10 Outer-SF-1', 'machine_code' => 'M10', 'rf_set' => 'RF-04'],
            ['name' => 'Machine No. 11 Middle-SF-1', 'machine_code' => 'M11', 'rf_set' => 'RF-04'],
            ['name' => 'Machine No. 12 Inner-SF-1', 'machine_code' => 'M12', 'rf_set' => 'RF-04'],

            // RF-05
            ['name' => 'Machine No. 13 Outer-SF-1', 'machine_code' => 'M13', 'rf_set' => 'RF-05'],
            ['name' => 'Machine No. 14 Middle-SF-1', 'machine_code' => 'M14', 'rf_set' => 'RF-05'],
            ['name' => 'Machine No. 15 Inner-SF-1', 'machine_code' => 'M15', 'rf_set' => 'RF-05'],

            // RF-06
            ['name' => 'Machine No. 16 Outer-SF-1', 'machine_code' => 'M16', 'rf_set' => 'RF-06'],
            ['name' => 'Machine No. 17 Middle-SF-1', 'machine_code' => 'M17', 'rf_set' => 'RF-06'],
            ['name' => 'Machine No. 18 Inner-SF-1', 'machine_code' => 'M18', 'rf_set' => 'RF-06'],

            // Ball Cage
            ['name' => 'Machine No. 02', 'machine_code' => 'BC02', 'rf_set' => 'Ball Cage'],
            ['name' => 'Machine No. 04', 'machine_code' => 'BC04', 'rf_set' => 'Ball Cage'],
        ];

        foreach ($machines as $machine) {
            Machine::create($machine);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\RejectReason;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RejectReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        RejectReason::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $reasons = [
            ['name' => 'Scratch', 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Dent', 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Rust', 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Coating Issue', 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Dimension Out', 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Welding Issue', 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Color Variation', 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Burr / Sharp Edge', 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Crack', 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Other', 'status' => 1, 'is_deleted' => 0],
        ];

        foreach ($reasons as $reason) {
            RejectReason::create($reason);
        }
    }
}

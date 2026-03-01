<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Item::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $items = [
            ['name' => 'Item 1',  'code' => 'I1',  'size' => '100 mm', 'weight' => 1.0, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 2',  'code' => 'I2',  'size' => '120 mm', 'weight' => 1.5, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 3',  'code' => 'I3',  'size' => '140 mm', 'weight' => 1.7, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 4',  'code' => 'I4',  'size' => '160 mm', 'weight' => 1.9, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 5',  'code' => 'I5',  'size' => '180 mm', 'weight' => 2.1, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 6',  'code' => 'I6',  'size' => '200 mm', 'weight' => 2.4, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 7',  'code' => 'I7',  'size' => '220 mm', 'weight' => 2.8, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 8',  'code' => 'I8',  'size' => '240 mm', 'weight' => 3.0, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 9',  'code' => 'I9',  'size' => '260 mm', 'weight' => 3.3, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 10', 'code' => 'I10', 'size' => '280 mm', 'weight' => 3.6, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 11', 'code' => 'I11', 'size' => '300 mm', 'weight' => 4.0, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 12', 'code' => 'I12', 'size' => '320 mm', 'weight' => 4.4, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 13', 'code' => 'I13', 'size' => '340 mm', 'weight' => 4.9, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 14', 'code' => 'I14', 'size' => '360 mm', 'weight' => 5.3, 'status' => 1, 'is_deleted' => 0],
            ['name' => 'Item 15', 'code' => 'I15', 'size' => '380 mm', 'weight' => 5.8, 'status' => 1, 'is_deleted' => 0],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\CoilManufacture;
use Illuminate\Database\Seeder;

class CoilManufactureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = ['Uttam', 'Tata', 'JSW'];

        foreach ($names as $name) {
            CoilManufacture::updateOrCreate(
                ['name' => $name],
                [
                    'status' => 1,
                    'is_deleted' => 0,
                ]
            );
        }
    }
}

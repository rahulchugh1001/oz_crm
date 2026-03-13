<?php

namespace Database\Seeders;

use App\Models\CoilManufacture;
use App\Models\CoilStock;
use Illuminate\Database\Seeder;

class CoilStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manufactureMap = CoilManufacture::query()
            ->whereIn('name', ['Uttam', 'Tata', 'JSW'])
            ->pluck('id', 'name');

        // Ensure required manufacture records exist for FK mapping.
        foreach (['Uttam', 'Tata', 'JSW'] as $name) {
            if (!isset($manufactureMap[$name])) {
                $manufacture = CoilManufacture::query()->updateOrCreate(
                    ['name' => $name],
                    ['status' => 1, 'is_deleted' => 0]
                );

                $manufactureMap[$name] = $manufacture->id;
            }
        }

        $rows = [
            [
                'manufacture_name' => 'Uttam',
                'coil_no' => 'OZ-BBDS-CRC Coil - 53.10 X 1 mm',
                'coil_size' => '53.10 X 1 mm',
                'thickness' => 0.950,
                'net_weight_kg' => 161,
                'process' => 'available',
                'status' => 1,
                'is_deleted' => 0,
            ],
            [
                'manufacture_name' => 'Uttam',
                'coil_no' => 'OZ-BBDS-CRC Coil - 65.5 X 1 mm',
                'coil_size' => '65.5 X 1 mm',
                'thickness' => 0.950,
                'net_weight_kg' => 175,
                'process' => 'available',
                'status' => 1,
                'is_deleted' => 0,
            ],
            [
                'manufacture_name' => 'Tata',
                'coil_no' => 'OZ-BBDS-CRC Coil - 34.70 X 1.20 mm',
                'coil_size' => '34.70 X 1.20 mm',
                'thickness' => 1.150,
                'net_weight_kg' => 185,
                'process' => 'in_use',
                'status' => 1,
                'is_deleted' => 0,
            ],
            [
                'manufacture_name' => 'JSW',
                'coil_no' => 'OZ-BBDS-GP Coil- Width- 89.8 mm to 90 mm X Thick- 0.6 mm',
                'coil_size' => '89.8 mm to 90 mm',
                'thickness' => 1.050,
                'net_weight_kg' => 170,
                'process' => 'completed',
                'status' => 1,
                'is_deleted' => 0,
            ],
            [
                'manufacture_name' => 'Tata',
                'coil_no' => 'CRCA Coil 0.8 MM Thk SPCC 4D',
                'coil_size' => '0.8 MM',
                'thickness' => 0.800,
                'net_weight_kg' => 0,
                'process' => 'available',
                'status' => 1,
                'is_deleted' => 0,
            ],
        ];

        foreach ($rows as $row) {
            CoilStock::query()->updateOrCreate(
                ['coil_no' => $row['coil_no']],
                [
                    'manufacture_id' => $manufactureMap[$row['manufacture_name']],
                    'coil_size' => $row['coil_size'],
                    'thickness' => $row['thickness'],
                    'net_weight_kg' => $row['net_weight_kg'],
                    'process' => $row['process'],
                    'status' => $row['status'],
                    'is_deleted' => $row['is_deleted'],
                ]
            );
        }
    }
}

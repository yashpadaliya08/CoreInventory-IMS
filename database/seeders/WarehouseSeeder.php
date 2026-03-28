<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'name'             => 'Main Warehouse',
                'code'             => 'WH-MAIN',
                'location_address' => 'Plot 12, Industrial Zone A, Surat, Gujarat',
                'locations' => [
                    ['name' => 'Receiving Bay',    'type' => 'vendor'],
                    ['name' => 'Dispatch Bay',     'type' => 'customer'],
                    ['name' => 'Rack A — Storage', 'type' => 'internal'],
                    ['name' => 'Rack B — Storage', 'type' => 'internal'],
                ],
            ],
            [
                'name'             => 'Secondary Hub',
                'code'             => 'WH-SEC',
                'location_address' => 'Unit 5, Logistics Park, Ahmedabad, Gujarat',
                'locations' => [
                    ['name' => 'Inbound Dock',     'type' => 'vendor'],
                    ['name' => 'Outbound Dock',    'type' => 'customer'],
                    ['name' => 'Cold Storage',     'type' => 'internal'],
                ],
            ],
        ];

        foreach ($warehouses as $wData) {
            $locationData = $wData['locations'];
            unset($wData['locations']);

            $warehouse = Warehouse::updateOrCreate(['code' => $wData['code']], $wData);

            foreach ($locationData as $loc) {
                Location::updateOrCreate(
                    ['name' => $loc['name'], 'warehouse_id' => $warehouse->id],
                    ['type' => $loc['type'], 'warehouse_id' => $warehouse->id]
                );
            }
        }

        $this->command->info('  ✔ Warehouses & Locations seeded: 2 warehouses, 7 locations');
    }
}

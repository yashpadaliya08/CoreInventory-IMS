<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Raw Materials
            ['name' => 'Steel Rods (12mm)',     'sku' => 'RM-STEEL-12',  'category' => 'Raw Materials',  'unit_of_measure' => 'KG',    'reorder_level' => 500],
            ['name' => 'Aluminum Sheet (3mm)',  'sku' => 'RM-ALU-03',    'category' => 'Raw Materials',  'unit_of_measure' => 'Sheet', 'reorder_level' => 100],
            ['name' => 'Copper Wire (1.5mm)',   'sku' => 'RM-COP-15',    'category' => 'Raw Materials',  'unit_of_measure' => 'KG',    'reorder_level' => 200],

            // Finished Goods
            ['name' => 'Electric Motor (5HP)',  'sku' => 'FG-EMOT-05',   'category' => 'Finished Goods', 'unit_of_measure' => 'Units', 'reorder_level' => 20],
            ['name' => 'Control Panel Box',     'sku' => 'FG-CPAN-01',   'category' => 'Finished Goods', 'unit_of_measure' => 'Units', 'reorder_level' => 15],
            ['name' => 'Industrial Fan (24")',  'sku' => 'FG-IFAN-24',   'category' => 'Finished Goods', 'unit_of_measure' => 'Units', 'reorder_level' => 10],

            // Consumables
            ['name' => 'Welding Rods Box',      'sku' => 'CON-WELD-01',  'category' => 'Consumables',    'unit_of_measure' => 'Box',   'reorder_level' => 50],
            ['name' => 'Safety Gloves (Pair)',  'sku' => 'CON-GLOVE-01', 'category' => 'Consumables',    'unit_of_measure' => 'Pair',  'reorder_level' => 100],
            ['name' => 'Cutting Disc (9")',     'sku' => 'CON-DISC-09',  'category' => 'Consumables',    'unit_of_measure' => 'Units', 'reorder_level' => 200],

            // Packaging
            ['name' => 'Cardboard Box (Large)', 'sku' => 'PKG-BOX-LG',   'category' => 'Packaging',      'unit_of_measure' => 'Units', 'reorder_level' => 300],
            ['name' => 'Bubble Wrap Roll',      'sku' => 'PKG-BWRAP-01', 'category' => 'Packaging',      'unit_of_measure' => 'Roll',  'reorder_level' => 50],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['sku' => $p['sku']], $p);
        }

        $this->command->info('  ✔ Products seeded: 11 products across 4 categories');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order matters — later seeders depend on earlier ones:
     * Users → Warehouses/Locations → Products → Receipts → Movements
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🌱 CoreInventory — Database Seeder');
        $this->command->info('──────────────────────────────────');

        $this->call([
            UserSeeder::class,      // Admin, Manager, Staff accounts
            WarehouseSeeder::class, // 2 Warehouses + 7 Locations
            ProductSeeder::class,   // 11 Products across 4 categories
            ReceiptSeeder::class,   // 3 Receipts (2 Done + 1 Waiting) + ledger entries
            MovementSeeder::class,  // Deliveries, Transfers, Adjustments + ledger entries
        ]);

        $this->command->info('──────────────────────────────────');
        $this->command->info('✅ All seed data inserted successfully!');
        $this->command->info('');
        $this->command->info('Demo Accounts:');
        $this->command->info('  admin@coreinventory.local   → Admin@12345');
        $this->command->info('  manager@coreinventory.local → Manager@12345');
        $this->command->info('  staff@coreinventory.local   → Staff@12345');
        $this->command->info('');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'System Administrator',
                'email'    => 'admin@coreinventory.local',
                'password' => Hash::make('Admin@12345'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Warehouse Manager',
                'email'    => 'manager@coreinventory.local',
                'password' => Hash::make('Manager@12345'),
                'role'     => 'manager',
            ],
            [
                'name'     => 'Stock Staff',
                'email'    => 'staff@coreinventory.local',
                'password' => Hash::make('Staff@12345'),
                'role'     => 'staff',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }

        $this->command->info('  ✔ Users seeded: admin, manager, staff');
    }
}

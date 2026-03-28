<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Extends the users role enum to include 'admin' alongside 'manager' and 'staff'.
 * Also seeds a default admin account if none exists.
 *
 * Roles:
 *  - admin   → Full system access: manage users, settings, validate all documents
 *  - manager → Can create/edit/validate documents, cannot manage users
 *  - staff   → Read-only + draft creation; cannot validate or delete
 */
return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires re-declaring the column to change an ENUM definition
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'staff') NOT NULL DEFAULT 'staff'");

        // Seed a default super-admin if the table is empty
        if (DB::table('users')->count() === 0) {
            DB::table('users')->insert([
                'name'       => 'System Administrator',
                'email'      => 'admin@coreinventory.local',
                'password'   => bcrypt('Admin@12345'),
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Revert enum back to original two values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('manager', 'staff') NOT NULL DEFAULT 'manager'");
    }
};

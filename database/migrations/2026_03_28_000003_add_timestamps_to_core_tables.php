<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds updated_at and created_at timestamps to core tables.
 * 
 * Required because soft-deletes inherently rely on Eloquent timestamps.
 * Enabling public $timestamps = true; on the models triggers Eloquent
 * to look for updated_at / created_at columns during insertion.
 */
return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'products', 
            'warehouses', 
            'locations', 
            'receipts', 
            'deliveries', 
            'transfers', 
            'adjustments'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                // Check if timestamps don't exist yet before adding
                if (!Schema::hasColumn($t->getTable(), 'created_at')) {
                    $t->timestamps();
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'products', 
            'warehouses', 
            'locations', 
            'receipts', 
            'deliveries', 
            'transfers', 
            'adjustments'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropTimestamps();
            });
        }
    }
};

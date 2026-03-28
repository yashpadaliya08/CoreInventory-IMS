<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Backend Phase 2: Data Integrity — Add SoftDeletes to core tables.
 *
 * Why SoftDeletes?
 * The stock_ledger is an immutable audit trail. If you hard-delete a Product
 * or Location, orphaned ledger rows lose their reference and stock calculations
 * break silently. With SoftDeletes, the row is "hidden" (deleted_at is set) but
 * the data remains, preserving all historical ledger integrity.
 *
 * Tables getting SoftDeletes:
 *  - products     (most critical — referenced by every ledger row)
 *  - warehouses   (parent of locations — cascaded soft-delete logic in model)
 *  - locations    (referenced by ledger, adjustments, transfers)
 *  - receipts     (document may have stock already validated against it)
 *  - deliveries   (same)
 *  - transfers    (same)
 *  - adjustments  (same)
 *
 * NOT soft-deleted:
 *  - stock_ledger (append-only audit log — never deleted)
 *  - receipt_items, delivery_items, transfer_items (line items die with document)
 *  - users (handled separately if needed)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Products — most critical, referenced by every ledger row
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes()->after('reorder_level');
        });

        // Warehouses — parent of locations
        Schema::table('warehouses', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Locations — referenced by ledger, adjustments, transfers
        Schema::table('locations', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Document tables — may have already triggered ledger writes
        Schema::table('receipts', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('adjustments', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        $tables = ['products', 'warehouses', 'locations', 'receipts', 'deliveries', 'transfers', 'adjustments'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropSoftDeletes();
            });
        }
    }
};

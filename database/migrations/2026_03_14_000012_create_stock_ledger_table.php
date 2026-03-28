<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->string('reference_type');   // Polymorphic string (e.g. App\Models\Receipt)
            $table->unsignedBigInteger('reference_id');
            $table->integer('quantity_change')->default(0); // Can be negative
            $table->timestamps();

            $table->index('product_id', 'idx_stock_ledger_product_id');
            $table->index('location_id', 'idx_stock_ledger_location_id');
            $table->index(['reference_type', 'reference_id'], 'idx_stock_ledger_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ledger');
    }
};

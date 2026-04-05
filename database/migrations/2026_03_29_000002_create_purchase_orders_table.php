<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 100)->unique();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->enum('status', ['Draft', 'Sent', 'Approved', 'Cancelled'])->default('Draft');
            $table->date('expected_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('receipt_id')->nullable()->constrained('receipts');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};

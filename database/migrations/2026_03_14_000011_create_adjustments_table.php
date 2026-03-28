<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 100)->unique();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('recorded_quantity')->default(0);
            $table->integer('physical_quantity')->default(0);
            $table->integer('difference_quantity')->default(0);
            $table->enum('status', ['Draft', 'Done'])->default('Draft');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjustments');
    }
};

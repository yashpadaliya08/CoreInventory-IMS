<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku', 100)->unique();
            $table->string('category')->nullable();
            $table->string('unit_of_measure', 50)->nullable();
            $table->integer('reorder_level')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

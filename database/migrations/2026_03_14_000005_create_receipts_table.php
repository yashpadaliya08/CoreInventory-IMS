<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 100)->unique();
            $table->string('vendor_name')->nullable();
            $table->enum('status', ['Draft', 'Waiting', 'Ready', 'Done', 'Canceled'])->default('Draft');
            $table->date('expected_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};

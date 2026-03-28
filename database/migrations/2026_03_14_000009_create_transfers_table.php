<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 100)->unique();
            $table->foreignId('from_location_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('to_location_id')->constrained('locations')->onDelete('cascade');
            $table->enum('status', ['Draft', 'Ready', 'Done'])->default('Draft');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};

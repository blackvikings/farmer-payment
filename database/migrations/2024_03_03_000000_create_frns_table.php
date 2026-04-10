<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frns', function (Blueprint $table) {
            // Using Lot No as Primary transaction key
            $table->string('lot_number')->primary();
            $table->foreign('lot_number')->references('lot_number')->on('lots')->cascadeOnDelete();

            // Prevent Duplicate FRN
            $table->string('frn_number')->unique();

            $table->date('arrival_date');
            $table->decimal('gross_weight', 10, 2);
            $table->string('vehicle_number')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frns');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entry_instructions', function (Blueprint $table) {
            $table->id();

            // Link to the FRN using lot_number (which is the primary key of the frns table)
            $table->string('lot_number')->unique();
            $table->foreign('lot_number')->references('lot_number')->on('frns')->cascadeOnDelete();

            // Set default status as per FR-4 requirements
            $table->string('status')->default('Processing Initiated');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_instructions');
    }
};

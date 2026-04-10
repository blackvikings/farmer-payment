<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // General Ledger for Farmers and Organizers
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // 'farmer' or 'organizer'
            $table->unsignedBigInteger('entity_id');
            $table->string('transaction_type'); // 'advance', 'payment', 'adjustment'
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable();
            $table->unsignedBigInteger('lot_id')->nullable(); // Optional link to a specific lot transaction
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Parameter Standards to define acceptable ranges for Quality Control
        Schema::create('parameter_standards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parameter_id')->constrained()->cascadeOnDelete();
            $table->decimal('min_accepted', 10, 2)->nullable();
            $table->decimal('max_accepted', 10, 2)->nullable();
            $table->decimal('min_conditional', 10, 2)->nullable();
            $table->decimal('max_conditional', 10, 2)->nullable();
            $table->timestamps();
        });

        // Store actual observed values for a lot
        Schema::create('quality_checks', function (Blueprint $table) {
            $table->id();
            $table->string('lot_number');
            $table->foreign('lot_number')->references('lot_number')->on('lots')->cascadeOnDelete();
            $table->foreignId('parameter_id')->constrained()->cascadeOnDelete();
            $table->decimal('observed_value', 10, 2);
            $table->string('status'); // Accepted, Conditional, Rejected for this specific parameter
            $table->timestamps();
        });

        // Add overall QC Status and Payment Block flag to Lots
        Schema::table('lots', function (Blueprint $table) {
            $table->string('qc_status')->default('Pending')->after('status');
            $table->boolean('payment_blocked')->default(false)->after('qc_status');
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn(['qc_status', 'payment_blocked']);
        });
        Schema::dropIfExists('quality_checks');
        Schema::dropIfExists('parameter_standards');
    }
};

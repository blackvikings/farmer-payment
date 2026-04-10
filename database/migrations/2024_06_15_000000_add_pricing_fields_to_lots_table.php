<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->decimal('base_amount', 12, 2)->nullable();
            $table->decimal('quality_deduction', 12, 2)->nullable();
            $table->decimal('bonus_amount', 12, 2)->nullable();
            $table->decimal('compensation_amount', 12, 2)->nullable();
            $table->decimal('debit_recovery', 12, 2)->nullable();
            $table->decimal('gross_payable', 12, 2)->nullable();
            $table->decimal('net_payable', 12, 2)->nullable();
            $table->string('payment_status')->default('pending'); // pending, approved, paid
            $table->boolean('pricing_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'base_amount',
                'quality_deduction',
                'bonus_amount',
                'compensation_amount',
                'debit_recovery',
                'gross_payable',
                'net_payable',
                'payment_status',
                'pricing_approved',
                'approved_by',
                'approved_at'
            ]);
        });
    }
};

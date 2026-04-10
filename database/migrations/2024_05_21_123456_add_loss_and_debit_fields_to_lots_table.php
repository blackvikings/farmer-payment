<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->decimal('final_quantity', 8, 2)->nullable()->after('quantity');
            $table->decimal('process_loss', 8, 2)->nullable()->after('final_quantity');
            $table->unsignedBigInteger('debit_note_id')->nullable()->after('process_loss');
            $table->boolean('debit_override')->default(false)->after('debit_note_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn(['final_quantity', 'process_loss', 'debit_note_id', 'debit_override']);
        });
    }
};

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
        Schema::table('loss_rules', function (Blueprint $table) {
            $table->dropColumn(['condition', 'deduction_amount']);
            $table->decimal('max_allowable_loss_percentage', 5, 2)->after('rule_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loss_rules', function (Blueprint $table) {
            $table->string('condition');
            $table->decimal('deduction_amount', 8, 2);
            $table->dropColumn('max_allowable_loss_percentage');
        });
    }
};

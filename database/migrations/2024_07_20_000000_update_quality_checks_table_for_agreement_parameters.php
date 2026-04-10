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
    public function up(): void
    {
        Schema::table('quality_checks', function (Blueprint $table) {
            // 1. Drop the existing foreign key constraint
            $table->dropForeign(['parameter_id']);

            // 2. Rename the column
            $table->renameColumn('parameter_id', 'agreement_parameter_id');
        });

        Schema::table('quality_checks', function (Blueprint $table) {
            // 3. Add the new foreign key constraint to agreement_parameters
            $table->foreign('agreement_parameter_id')->references('id')->on('agreement_parameters')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('quality_checks', function (Blueprint $table) {
            // 1. Drop the new foreign key constraint
            $table->dropForeign(['agreement_parameter_id']);

            // 2. Rename the column back
            $table->renameColumn('agreement_parameter_id', 'parameter_id');
        });

        Schema::table('quality_checks', function (Blueprint $table) {
            // 3. Re-add the old foreign key constraint (if the 'parameters' table still exists or is recreated)
            // For simplicity in rollback, we'll assume the 'parameters' table would be restored by other rollbacks.
            // If not, this foreign key would fail.
            // $table->foreign('parameter_id')->references('id')->on('parameters')->cascadeOnDelete();
        });
    }
};

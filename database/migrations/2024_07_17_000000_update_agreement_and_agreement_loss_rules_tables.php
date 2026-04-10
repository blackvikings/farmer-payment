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
        Schema::table('agreements', function (Blueprint $table) {
            // Drop columns from previous migrations if they exist
            if (Schema::hasColumn('agreements', 'base_price')) {
                $table->dropColumn('base_price');
            }
            if (Schema::hasColumn('agreements', 'bonus_condition')) {
                $table->dropColumn('bonus_condition');
            }
            if (Schema::hasColumn('agreements', 'bonus_amount')) {
                $table->dropColumn('bonus_amount');
            }
            if (Schema::hasColumn('agreements', 'base_rate')) {
                $table->dropColumn('base_rate');
            }

            // Add the new columns
            $table->string('rate')->nullable()->after('end_date');
            $table->string('bonus')->nullable()->after('rate');
        });

        Schema::table('agreement_loss_rules', function (Blueprint $table) {
            // Drop columns from previous migrations if they exist
            if (Schema::hasColumn('agreement_loss_rules', 'rule_name')) {
                $table->dropColumn('rule_name');
            }
            if (Schema::hasColumn('agreement_loss_rules', 'max_allowable_loss_percentage')) {
                $table->dropColumn('max_allowable_loss_percentage');
            }
            if (Schema::hasColumn('agreement_loss_rules', 'condition_description')) {
                $table->dropColumn('condition_description');
            }
            if (Schema::hasColumn('agreement_loss_rules', 'loss_percentage')) {
                $table->dropColumn('loss_percentage');
            }

            // Add the new columns
            $table->string('name')->after('agreement_id');
            $table->string('value')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            $table->dropColumn(['rate', 'bonus']);
            // Re-add old columns for rollback if necessary, or leave as is if they were already dropped by other migrations
            // For simplicity, we won't re-add the old columns here as they were part of a refactor.
            // If a full rollback is needed, the previous migrations would handle it.
        });

        Schema::table('agreement_loss_rules', function (Blueprint $table) {
            $table->dropColumn(['name', 'value']);
            // Re-add old columns for rollback if necessary
        });
    }
};

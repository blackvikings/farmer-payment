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
            // Step 1: Remove the old 'terms' column if it exists.
            if (Schema::hasColumn('agreements', 'terms')) {
                $table->dropColumn('terms');
            }

            // Step 2: Add new foreign key columns for the rules.
            // These are nullable in case an agreement doesn't need a specific rule.
            $table->foreignId('rate_id')->nullable()->after('end_date')->constrained('rates')->onDelete('set null');
            $table->foreignId('bonus_rule_id')->nullable()->after('rate_id')->constrained('bonus_rules')->onDelete('set null');
            $table->foreignId('loss_rule_id')->nullable()->after('bonus_rule_id')->constrained('loss_rules')->onDelete('set null');
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
            // To make this reversible, we add the 'terms' column back.
            $table->text('terms')->nullable();

            // And we drop the foreign key constraints and columns.
            $table->dropForeign(['rate_id']);
            $table->dropForeign(['bonus_rule_id']);
            $table->dropForeign(['loss_rule_id']);
            $table->dropColumn(['rate_id', 'bonus_rule_id', 'loss_rule_id']);
        });
    }
};

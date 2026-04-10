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
        // First, drop the foreign key constraints from the previous migration if they exist
        Schema::table('agreements', function (Blueprint $table) {
            if (Schema::hasColumn('agreements', 'rate_id')) {
                $table->dropForeign(['rate_id']);
                $table->dropColumn('rate_id');
            }
            if (Schema::hasColumn('agreements', 'bonus_rule_id')) {
                $table->dropForeign(['bonus_rule_id']);
                $table->dropColumn('bonus_rule_id');
            }
            if (Schema::hasColumn('agreements', 'loss_rule_id')) {
                $table->dropForeign(['loss_rule_id']);
                $table->dropColumn('loss_rule_id');
            }
        });

        // Now, add the new columns for direct input
        Schema::table('agreements', function (Blueprint $table) {
            $table->decimal('base_rate', 10, 2)->nullable()->after('end_date');
            $table->decimal('bonus_amount', 10, 2)->nullable()->after('base_rate');
        });

        // Create the new table for dynamic loss rules
        Schema::create('agreement_loss_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agreement_id')->constrained()->onDelete('cascade');
            $table->string('condition_description'); // e.g., "If moisture > 15%"
            $table->decimal('loss_percentage', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_loss_rules');

        Schema::table('agreements', function (Blueprint $table) {
            $table->dropColumn(['base_rate', 'bonus_amount']);

            // Re-add the old foreign keys to make it reversible
            $table->foreignId('rate_id')->nullable()->constrained('rates');
            $table->foreignId('bonus_rule_id')->nullable()->constrained('bonus_rules');
            $table->foreignId('loss_rule_id')->nullable()->constrained('loss_rules');
        });
    }
};

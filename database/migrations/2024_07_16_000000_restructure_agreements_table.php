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
            // Drop foreign keys and columns if they exist
            if (Schema::hasColumn('agreements', 'rate_id')) {
                try {
                    $table->dropForeign(['rate_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, ignore
                }
                $table->dropColumn('rate_id');
            }

            if (Schema::hasColumn('agreements', 'bonus_rule_id')) {
                try {
                    $table->dropForeign(['bonus_rule_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, ignore
                }
                $table->dropColumn('bonus_rule_id');
            }

            if (Schema::hasColumn('agreements', 'loss_rule_id')) {
                try {
                    $table->dropForeign(['loss_rule_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, ignore
                }
                $table->dropColumn('loss_rule_id');
            }

            // Add direct fields for rate and bonus, if they don't already exist
            if (!Schema::hasColumn('agreements', 'base_price')) {
                $table->decimal('base_price', 10, 2)->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('agreements', 'bonus_condition')) {
                $table->string('bonus_condition')->nullable()->after('base_price');
            }
            if (!Schema::hasColumn('agreements', 'bonus_amount')) {
                $table->decimal('bonus_amount', 10, 2)->nullable()->after('bonus_condition');
            }
        });

        // Create the new table for the one-to-many loss rules relationship, if it doesn't exist
        if (!Schema::hasTable('agreement_loss_rules')) {
            Schema::create('agreement_loss_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agreement_id')->constrained()->onDelete('cascade');
                $table->string('rule_name');
                $table->decimal('max_allowable_loss_percentage', 5, 2);
                $table->timestamps();
            });
        }
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
            if (Schema::hasColumn('agreements', 'base_price')) {
                $table->dropColumn('base_price');
            }
            if (Schema::hasColumn('agreements', 'bonus_condition')) {
                $table->dropColumn('bonus_condition');
            }
            if (Schema::hasColumn('agreements', 'bonus_amount')) {
                $table->dropColumn('bonus_amount');
            }

            // Add the old foreign keys back if they don't exist
            if (!Schema::hasColumn('agreements', 'rate_id')) {
                $table->foreignId('rate_id')->nullable()->constrained('rates');
            }
            if (!Schema::hasColumn('agreements', 'bonus_rule_id')) {
                $table->foreignId('bonus_rule_id')->nullable()->constrained('bonus_rules');
            }
            if (!Schema::hasColumn('agreements', 'loss_rule_id')) {
                $table->foreignId('loss_rule_id')->nullable()->constrained('loss_rules');
            }
        });
    }
};

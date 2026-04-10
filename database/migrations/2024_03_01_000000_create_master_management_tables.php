<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Organizers
        Schema::create('organizers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_info')->nullable();
            $table->timestamps();
        });

        // Farmers
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone_number')->unique();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // Agreements
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('terms')->nullable();
            $table->timestamps();
        });

        // Parameters
        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Moisture, Size
            $table->string('type'); // e.g., quality, production
            $table->timestamps();
        });

        // Rates (with version control)
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parameter_id')->constrained()->cascadeOnDelete();
            $table->decimal('base_price', 10, 2);
            $table->integer('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Bonus Rules (with version control)
        Schema::create('bonus_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');
            $table->string('condition'); // e.g., > 90% quality
            $table->decimal('bonus_amount', 10, 2);
            $table->integer('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Loss Rules (with version control)
        Schema::create('loss_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');
            $table->string('condition'); // e.g., < 50% quality
            $table->decimal('deduction_amount', 10, 2);
            $table->integer('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loss_rules');
        Schema::dropIfExists('bonus_rules');
        Schema::dropIfExists('rates');
        Schema::dropIfExists('parameters');
        Schema::dropIfExists('agreements');
        Schema::dropIfExists('farmers');
        Schema::dropIfExists('organizers');
    }
};

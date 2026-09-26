<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

      if (Schema::hasTable('annual_deposit_plans')) {
        return;
    }
    
        Schema::create('annual_deposit_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annual_plan_id')->constrained()->cascadeOnDelete();
            $table->decimal('annual_target_amount', 20, 2)->nullable();

            $table->decimal('q1_target_amount', 20, 2)->nullable();
            $table->decimal('q2_target_amount', 20, 2)->nullable();
            $table->decimal('q3_target_amount', 20, 2)->nullable();
            $table->decimal('q4_target_amount', 20, 2)->nullable();

            $table->decimal('daily_target_amount', 20, 2)->nullable();
            $table->decimal('monthly_target_amount', 18, 2)->nullable();;
            $table->decimal('weekly_target_amount', 18, 2)->nullable();

            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_deposit_plans');
    }
};

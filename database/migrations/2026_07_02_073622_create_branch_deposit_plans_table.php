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
        Schema::create('branch_deposit_plans', function (Blueprint $table) {
       
            $table->id();
            $table->foreignId('annual_deposit_plan_id')->constrained('annual_deposit_plans')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained() ->cascadeOnDelete();
            $table->decimal('annual_target_amount', 20, 2)->nullable();

            $table->decimal('q1_target_amount', 20, 2)->nullable();
            $table->decimal('q2_target_amount', 20, 2)->nullable();
            $table->decimal('q3_target_amount', 20, 2)->nullable();
            $table->decimal('q4_target_amount', 20, 2)->nullable();

            $table->decimal('monthly_target_amount', 20, 2)->nullable();
            $table->decimal('weekly_target_amount', 20, 2)->nullable();
            $table->decimal('daily_target_amount', 20, 2)->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique(['annual_deposit_plan_id', 'branch_id']);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_deposit_plans');
    }
};
